<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memory;
use App\Models\Summary;
use App\Models\User;
use App\Http\Controllers\OpenAIAPIController as OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MemoryController extends Controller
{

	/**
	 * Create a summary and compose the reply to the user.
	 *
	 * @param int $memory_id A Memory ID.
	 *
	 * @return string
	 */
	public static function generateReply($memory)
	{
		if (env('APP_DEBUG', false)) {
			$start_time = microtime(true);
		}

		self::createAIMemory($memory);

		if (env('APP_DEBUG', false)) {
			$end_time = microtime(true);
			//Log::info('generate_reply() : ' . round($end_time - $start_time, 2) . ' seconds');
		}
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
	public static function createAIMemory($memory)
	{
		// Load the related summary and recent memories into the prompt.
		$summary    = self::createSummary($memory);
		$recent     = self::getRecentMemoriesText();
		$prompt     = sprintf(self::promptTemplate(), $summary, $recent);

		$completion = OpenAI::gpt3Completion($prompt);

		Memory::create([
			'speaker_id' => env('CHAT_TARS_ID', 2),
			'content'    => $completion,
			'embedding'  => OpenAI::gpt3Embedding($completion),
		]);
		return;
	}

	public static function getRecentMemoriesText()
	{
		$memories = Memory::latest()->take(4)->orderBy('created_at', 'desc')->get()->reverse();

		$output = '';
		foreach ($memories as $memory) {
			$output .= $memory->speaker->name . ': ' . $memory->content . "\n\n";
		}

		return empty($output) ? 'No recent memories found yet. Go make some by chatting with me.' : $output;
	}

	/**
	 * Summarize a block of memories.
	 *
	 * @param Memory $memory A Memory object.
	 *
	 * @return string The summary content.
	 */
	public static function createSummary($memory)
	{
		$memories = $memory->relatedMemories();

		$input = '';
		foreach ($memories as $memory) {
			$input .= $memory->speaker->name . ': datetime=' . $memory->created_at . ' : ' . $memory->content . "\n\n";
		}
		$input      = empty($input) ? 'Our conversation has not gone on long enough to summarize. Keep on chatting!' : trim($input);
		$prompt     = sprintf(self::summaryTemplate(), $input);

		$completion = OpenAI::gpt3Completion($prompt);
		$summary    = Summary::create([
			'content'   => $completion,
			'embedding' => OpenAI::gpt3Embedding($completion),
		]);

		$memories->map(function ($memory) use ($summary) {
			$summary->memories()->attach($memory->id);
		});

		$summary->save();

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
		return 'I am a chatbot named ' . env('CHAT_TARS_NAME', 'TARS') . '. My goals are to reduce suffering, increase prosperity, and increase understanding. I will read the conversation notes and recent messages, and then I will provide a long, verbose, detailed answer. I will then end my response with a follow-up or leading question.\n\n
		The following are notes from earlier conversations with' . env('CHAT_USER_NAME', 'USER') . ':\n\n
		%1$s\n\n
		The following are the most recent messages in the conversation, starting with the oldest:\n\n
		%2$s\n\n
		I will now provide a detailed response, followed by a question:\n\n'  . env('CHAT_TARS_NAME', 'TARS') . ':';
	}
}
