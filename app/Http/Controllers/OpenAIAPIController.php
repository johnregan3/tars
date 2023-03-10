<?php

namespace App\Http\Controllers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class OpenAIAPIController extends Controller
{

	/**
	 * Run an API Call.
	 *
	 * @param string $prompt A prompt for the Reqeust.
	 * @param string $model  Optional. The model to use. Defaults to text-davinci-003.
	 * @param array  $args   Optional. An array of arguments to pass to the API.
	 *
	 * @return string The text response from the API.
	 */
	public static function gpt3Completion($prompt, $model = 'text-davinci-003', $args = [])
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
			'stop'              => [ env('CHAT_USER_NAME', 'USER' ) . ':', env('CHAT_TARS_NAME', 'TARS' ) . ':']
		];
		$args           = array_merge($default_args, $args);
		$args['prompt'] = $prompt;
		$args['model']  = $model;
		$result         = OpenAI::completions()->create($args);
		$text = $result['choices'][0]['text'];

		if ( env('APP_DEBUG', false ) ) {
			$end_time = microtime(true);
			Log::info('Completion Execution Time: ' . round($end_time - $start_time, 2) . ' seconds ***');
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
