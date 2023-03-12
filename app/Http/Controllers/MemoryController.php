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
	 * The recent conversation, as a string.
	 *
	 * @var string
	 */
	private $conversationString = '';

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
		$this->conversationString = $this->getConversationString();
	}

	/**
	 * Create a New Memory from Tars' POV.
	 *
	 * This is the response to the user's input message.
	 *
	 * @param Memory $memory A Memory object.
	 *
	 * @return string The completion content.
	 */
	public function createAIMemory()
	{

		$this->createSummary();

		$memories = $this->getConversation();

		$messages = [];
		foreach ($memories as $memory) {
			$messages[] = [
				'role'    => $memory['speaker_id'] == env('CHAT_TARS_ID', 2) ? 'assistant' : 'user',
				'content' => $memory['content'],
			];
		}

		$content = OpenAI::gpt3Chat($messages);

		// @todo Needs a sanity check.
		if (empty($content)) {
			Log::info('MemoryController::createAIMemory() : empty content');
			return;
		}

		Memory::create([
			'speaker_id' => env('CHAT_TARS_ID', 2),
			'content'    => $content,
		]);

		return $content;
	}

	/**
	 * Get the 4 most recent messages, placing the oldest at the top of the list.
	 *
	 * @return array
	 */
	protected function getConversation()
	{
		return Memory::latest()->take(4)->get()->reverse()->toArray();
	}

	/**
	 * Get the recent conversation, as a string.
	 *
	 * Used for filling in Salience and Anticipation templates.
	 *
	 * @return string
	 */
	protected function getConversationString()
	{
		$memories = $this->getConversation();

		$output = array_map(function ($memory) {
			return strtoupper(User::find($memory['speaker_id'])->name) . ': ' . Utils::normalizedDate($memory['created_at']) . ' - ' . $memory['content'];
		}, $memories);

		$output = implode( PHP_EOL . PHP_EOL, $output);

		return empty($output) ? strtoupper(User::find($this->memory['speaker_id'])->name) . ': ' . Utils::normalizedDate($this->memory['created_at']) . ' - This is the start of our conversation.' . PHP_EOL . PHP_EOL : $output;
	}

	/**
	 * Fill in the Salience template.
	 *
	 * @todo not presently used.
	 *
	 * @return string
	 */
	protected function getSalience()
	{
		$prompt = sprintf(self::salientPointsTemplate(), $this->conversationString);
		return OpenAI::gpt3Completion($prompt);
	}

	/**
	 * Fill in the Anticipation template.
	 *
	 * @todo not presently used.
	 *
	 * @return string
	 */
	protected function getAnticipation()
	{
		$prompt = sprintf(self::anticipationTemplate(), $this->conversationString);
		return OpenAI::gpt3Completion($prompt);
	}

	/**
	 * Template for the Assistant portion of the Chat Message.
	 *
	 * @todo Not presently used. The recent chat log is sent directly instead.
	 * May eventually be needed in Chat API Messages somewhere.
	 *
	 * @return string
	 */
	protected function getAssistantMessage()
	{
		return sprintf(self::assistantTemplate(), $this->getSalience(), $this->getAnticipation());
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
		// Get the *related* memories, not recent ones.
		$memories = $this->memory->relatedMemories()->toArray();

		$relatedConversation = array_map(function ($memory) {
			return User::find($memory['speaker_id'])->name . ': ' . Utils::normalizedDate($memory['created_at']) . ' - ' . $memory['content'];
		}, $memories);

		$relatedConversation = implode(PHP_EOL . PHP_EOL, $relatedConversation);
		$relatedConversation = empty($relatedConversation) ? 'Our conversation has not gone on long enough to summarize. Keep on chatting!' : trim($relatedConversation);

		$prompt  = sprintf(self::summaryTemplate(), $relatedConversation);

		$completion = OpenAI::gpt3Completion($prompt);
		if (empty($completion)) {
			Log::info('MemoryController::createSummary() : empty completion');
			return;
		}
		$summary = Summary::create([
			'content' => $completion,
		]);

		// Attach the related memories to the summary.
		array_map(function ($memory) use ($summary) {
			$summary->memories()->attach($memory['id']);
		}, $memories);
	}

	/**
	 * Template for the prompt to generate TARS' anticipation completion.
	 *
	 * @return string
	 */
	protected static function anticipationTemplate()
	{
		return 'Given the following chat log, infer the user\'s actual information needs. Attempt to anticipate what the user truly needs even if the user does not fully understand it yet themselves, or is asking the wrong questions.' . PHP_EOL . PHP_EOL . 'CHAT LOG:' . PHP_EOL . '%s' . PHP_EOL . PHP_EOL . 'ANTICIPATED USER NEEDS:' . PHP_EOL;
	}

	/**
	 * Template for the prompt to generate TARS' salience completion.
	 *
	 * @return string
	 */
	protected static function salientPointsTemplate()
	{
		return 'Given the following chat log, write a brief summary of only the most salient points of the conversation.' . PHP_EOL . PHP_EOL . 'CHAT LOG:' . PHP_EOL . '%s' . PHP_EOL . PHP_EOL . 'SALIENT POINTS:' . PHP_EOL;
	}

	/**
	 * Template for the the Assistant's message.
	 *
	 * @todo Not presently used. May be needed in Chat API Messages somewhere.
	 *
	 * @return string
	 */
	protected static function assistantTemplate()
	{
		return 'I am in the middle of a conversation: %1$s. I anticipate the user needs: %2$s. I will do my best to fulfill my objectives.';
	}

	/**
	 * Template for the prompt to summarize the conversation.
	 * This is the input to the GPT-3 API.
	 *
	 * @return string
	 */
	protected static function summaryTemplate()
	{
		return 'Write detailed notes of the following in a hyphenated list format like "- "' . PHP_EOL . PHP_EOL . '%1$s' . PHP_EOL . PHP_EOL . 'Notes:' . PHP_EOL;
	}
}
