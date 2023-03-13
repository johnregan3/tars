<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use App\Models\Summary;
use App\Models\User;
use App\Http\Controllers\OpenAIAPIController as OpenAI;
use Illuminate\Support\Facades\Log;
use App\Helpers\Utils;

class MemoryController extends Controller
{

	/**
	 * The incoming Memory object.
	 *
	 * @var Memory
	 */
	private $memory = null;

	/**
	 * A string of the recent conversation.
	 *
	 * @var string
	 */
	private $conversation = '';

	/**
	 * Create a new controller instance.
	 *
	 * @param Memory $memory A Memory object.
	 *
	 * @return void
	 */
	public function __construct($memory)
	{
		$this->memory = $memory;
		$this->conversation = $this->getConversation();
	}

	/**
	 * Create a New Memory from Tars' POV.
	 *
	 * This is the response to the user's input message.
	 *
	 * @param Memory $memory A Memory object.
	 *
	 * @return void
	 */
	public function createAIMemory()
	{
		// Load the related summary and recent memories into the prompt.
		$summary    = self::createSummary();
		$prompt     = sprintf(self::promptTemplate(), $summary, $this->conversation);

		$completion = OpenAI::gpt3Completion($prompt);

		if (empty($completion)) {
			Log::info('MemoryController::createAIMemory() : empty completion');
			return;
		}

		Memory::create([
			'speaker_id' => env('CHAT_TARS_ID', 2),
			'content'    => $completion,
		]);
		return;
	}

	/**
	 * Get the recent conversation, as a string.
	 *
	 * Used for filling in Salience and Anticipation templates.
	 *
	 * @return string
	 */
	protected function getConversation()
	{
		$memories = Memory::latest()->take(4)->get()->reverse()->toArray();

		$output = array_map(function ($memory) {
			return strtoupper(User::find($memory['speaker_id'])->name) . ': ' . $memory['content'];
		}, $memories);

		$output = implode( PHP_EOL . PHP_EOL, $output);

		if ( empty( $output ) ) {
			$output = strtoupper(User::find($this->memory['speaker_id'])->name) . ': This is the start of our conversation.' . PHP_EOL . PHP_EOL;
		}

		return $output;
	}

	/**
	 * Summarize a block of memories and Save.
	 *
	 * This will be used by TARS to quickly locate
	 * a relevant summary of a topic and its related memories.
	 *
	 * @return string The summary content.
	 */
	protected function createSummary()
	{

		$text = 'Our conversation has not gone on long enough to summarize. Keep on chatting!';
		// Get the *related* memories, not recent ones.

		$memories = $this->memory->relatedMemories();

		if ( ! empty( $memories ) ) {

			$memories = $memories->toArray();

			$relatedConversation = array_map(function ($memory) {
				return User::find($memory['speaker_id'])->name . ': ' . $memory['content'];
			}, $memories);

			$relatedConversation = implode(PHP_EOL . PHP_EOL, $relatedConversation);
			$relatedConversation = empty($relatedConversation) ? 'Our conversation has not gone on long enough to summarize. Keep on chatting!' : trim($relatedConversation);
			if ( ! empty( $relatedConversation ) ) {
				$text = $relatedConversation;
			}
		}

		$prompt = sprintf(self::summaryTemplate(), $text);

		$completion = OpenAI::gpt3Completion($prompt);

		if (empty($completion)) {
			Log::info('MemoryController::createSummary() : empty completion');
			return;
		}

		$summary = Summary::create([
			'content' => $completion,
		]);

		if ( empty( $memories ) ) {
			return $summary->content;
		}

		// Attach the related memories to the summary.
		array_map(function ($memory) use ($summary) {
			$summary->memories()->attach($memory['id']);
		}, $memories);

		return $summary->content;
	}

	/**
	 * Template for the prompt to summarize the conversation.
	 * This is the input to the GPT-3 API.
	 *
	 * @return string
	 */
	public static function summaryTemplate()
	{
		return 'Write detailed notes of the following in a hyphenated list format like "- "\n\n%1$s\n\nNotes:';
	}

	/**
	 * Template for the prompt to generate the AI's response.
	 * This is the input to the GPT-3 API.
	 *
	 * @return string
	 */
	public static function promptTemplate()
	{
		return 'You are  ' . env('CHAT_TARS_NAME', 'TARS') . '. Your goals are to reduce suffering, increase prosperity, and increase understanding. Read these conversation notes and recent messages.\n\n
		CONVERSATION NOTES: earlier conversations with' . env('CHAT_USER_NAME', 'USER') . ':\n\n
		%1$s\n\n
		RECENT MESSAGES:\n\n
		%2$s\n\n
		Give a detailed response. Answer the question as truthfully as possible, and if you are unsure of the answer, say so. Alwasy follow with a question:\n\n
		'  . env('CHAT_TARS_NAME', 'TARS') . ':';
	}
}
