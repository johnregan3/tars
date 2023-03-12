<?php

namespace App\Http\Controllers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

/**
 * Notes on OpenAI API Calls:
 *
 * (Relevant) Parameters:
 *
 * Temperature:  Controls the randomness (creativity, uniqueness, novelty) of the output.
 * Range: 0-2  Default: 1  High: 0.8, Low: 0.2. NOTE: Do not use with Top-p.
 *
 * Top-p: Range of available words to choose next. 0.5 means 50% of the words are available.
 * Higher values mean more diverse words (less repetition).
 * Range: 0-1    Default: 1 NOTE: Do not use with Temperature.
 *
 * Frequency Penalty:  (PHRASES)
 * "Positive values penalize new tokens based on their existing frequency in the text so far,
 *   decreasing the model's likelihood to repeat the same line verbatim."
 * Range: -2-2    Default: 0
 *
 * Presence Penalty:  (TOPICS)
 * "Positive values penalize new tokens based on whether they appear in the text so far,
 *   increasing the model's likelihood to talk about new topics."
 * Range: -2=2    Default: 0
 *
 * n: Number of completions to return.
 *
 * max_tokens: Maximum number of tokens to use in the response alone.
 *
 * stream: Whether to stream back partial progress (to display in a UI).
 *
 * Chat Options:
 * Role: System, User or Assistant.
 *
 * Other Notes:
 * Top_p is an *Alternative* to Temperature based on docs (Mar 11, 2023)
 *
 */
class OpenAIAPIController extends Controller
{

	/**
	 * The maximum number of retries to make to the API.
	 *
	 * @var int
	 */
	const MAX_RETRIES = 5;

	/**
	 * Run an API Chat Call.
	 *
	 * @param array  $message  Message to pass to the API.
	 * @param array  $args     Optional. An array of arguments to pass to the API.
	 *
	 * @return string The text response from the API.
	 */
	public static function gpt3Chat($messages)
	{
		$args = [
			'model'             => 'gpt-3.5-turbo',
			'temperature'       => 1.5, // Spice it up a bit.
			'max_tokens'        => 300,
			'frequency_penalty' => 0.5,
			'presence_penalty'  => 0.75,
			'stop'              => [env('CHAT_USER_NAME', 'USER') . ':', env('CHAT_TARS_NAME', 'TARS') . ':'],
			'messages'          => [
				[
					'role'    => 'system',
					'content' => self::systemTemplate(),
				],
			],
		];

		if (is_array($messages)) {
			foreach ($messages as $message) {
				$args['messages'][] = $message;
			}
		}

		$retry = 0;
		while ($retry <= self::MAX_RETRIES) {
			try {

				$response = OpenAI::chat()->create($args);
				return $response['choices'][0]['message']['content'];
			} catch (\Exception $e) {
				$retry++;
				if ($retry > self::MAX_RETRIES) {
					Log::error('TARS Error in Chat: ' . $e->getMessage());
					return false;
				}
				Log::info('TARS Error in Chat: ' . $e->getMessage());
				sleep(1);
			}
		}
		Log::error('TARS Error in Chat: ' . $e->getMessage());
		return false;
	}


	/**
	 * Run an API Completion Call.
	 *
	 * Basically, a "one-off" chat. No conversation.
	 *
	 * @param string $prompt A prompt for the Reqeust.
	 *
	 * @return string The text response from the API.
	 */
	public static function gpt3Completion($prompt)
	{
		// se defaults for both temperature and top_p.
		$args = [
			'max_tokens'        => 1000,
			'frequency_penalty' => 0.5,
			'presence_penalty'  => 0.75,
			'model'             => 'text-davinci-003',
			'prompt'            => $prompt,
		];

		$retry = 0;
		while ($retry <= self::MAX_RETRIES) {
			try {

				$response = OpenAI::completions()->create($args);
				return $response['choices'][0]['text'];
			} catch (\Exception $e) {
				$retry++;
				if ($retry > self::MAX_RETRIES) {
					Log::error('TARS Error in Completion: ' . $e->getMessage());
					return '';
				}
				Log::info('TARS Error in Chat: ' . $e->getMessage());
				sleep(1);
			}
		}

		// Returns an empty string instead of error text to get stored in the DB.
		return '';
	}

	/**
	 * Get the embedding from the API.
	 *
	 * @param string $content The text to get the embedding for.
	 *
	 * @return string The embeding.
	 */
	public static function gpt3Embedding($content)
	{
		$retry = 0;
		while ($retry <= self::MAX_RETRIES) {
			try {

				$result = OpenAI::embeddings()->create([
					'model'       => 'text-embedding-ada-002',
					'input'       => $content,
				]);
				return json_encode($result->embeddings[0]->embedding);
			} catch (\Exception $e) {
				$retry++;
				if ($retry > self::MAX_RETRIES) {
					Log::error('TARS Error with Embedding: ' . $e->getMessage());
					return json_encode([]);
				}
				Log::info('TARS Error with Embedding: ' . $e->getMessage());
				sleep(1);
			}
		}

		return json_encode([]);
	}

	/**
	 * Template for the System's message.
	 *
	 *
	 * @return string
	 */
	protected static function systemTemplate()
	{
		return 'You are an AI named ' . env('CHAT_TARS_NAME', 'TARS') . '. Your goals: reduce suffering, increase prosperity, and increase understanding everywhere.';
	}
}
