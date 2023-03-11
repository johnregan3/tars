<?php

namespace App\Http\Controllers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class OpenAIAPIController extends Controller
{

	/**
	 * Run an API Call.
	 *
	 * @param array  $messages Messages to pass to the API.
	 * @param array  $args     Optional. An array of arguments to pass to the API.
	 *
	 * @return string The text response from the API.
	 */
	public static function gpt3Completion($prompt, $args = [])
	{
		if ( env('APP_DEBUG', false ) ) {
			$start_time = microtime(true);
		}

		$default_args = [
			'temperature'       => 0.7,  // Randomness. 0 Makes it boring and repetitive.
			'max_tokens'        => 300,  // Max tokens allowed.
			'top_p'             => 0.75, // A seive to remove low probability tokens.
			'frequency_penalty' => 0.5,  // Penalize new words based on their existing frequency.
			'presence_penalty'  => 0.75, // Likelihood of using new topics.
			'stop'              => [ env('CHAT_USER_NAME', 'USER' ) . ':', env('CHAT_TARS_NAME', 'TARS' ) . ':'],
		];
		$args           = array_merge($default_args, $args);
		$args['model']  = 'gpt-3.5-turbo';
		$args['messages'] = [
			[
				'role' => 'system',
				'content' => 'You a chatbot named ' . env('CHAT_TARS_NAME', 'TARS') . '. Your goals are to reduce suffering, increase prosperity, and increase understanding. You will provide a long, verbose, detailed answer. You will then end your response with a follow-up or leading question.'
			],
			[
				'role' => 'user',
				'content' => $prompt,
			],
		];

		$response = OpenAI::completions()->create($args);
		$text = $response['choices'][0]['content'];

		if ( env('APP_DEBUG', false ) ) {
			$end_time = microtime(true);
			Log::info('Completion Execution Time: ' . round($end_time - $start_time, 2) . ' seconds ***');
			Log::info( $response );
		}
		return $text;
	}

	/**
	 * Get the embedding from the API using the text-embedding-ada-002 engine.
	 *
	 * @param string $content The text to get the embedding for.
	 *
	 * @return array The embeding (vector).
	 */
	public static function gpt3Embedding($content, $engine = 'text-embedding-ada-002')
	{
		if ( env('APP_DEBUG', false ) ) {
			$start_time  = microtime(true);
		}
		$max_retries = 5;
		$retry       = 0;
		while( $retry <= $max_retries ) {
			try {
				$result = OpenAI::embeddings()->create([
					'model' => $engine,
					'input' => $content,
				]);

				if ( env('APP_DEBUG', false ) ) {
					$end_time = microtime(true);
					//Log::info('Embedding Execution Time: ' . round($end_time - $start_time, 2) . ' seconds ***');
				}
				return json_encode($result->embeddings[0]->embedding);
			} catch (\Exception $e) {
				$retry++;
				if ($retry > $max_retries) {
					return "GPT3 error: " . $e->getMessage();
				}
				Log::info('Error communicating with OpenAI: ' . $e->getMessage());
				sleep(1);
			}
		}


		if ( env('APP_DEBUG', false ) ) {
			$end_time = microtime(true);
			//Log::info('Embedding ERROR Execution Time: ' . round($end_time - $start_time, 2) . ' seconds ***');
		}

		return json_encode([]);
	}
}
