<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memory;
use App\Models\Summary;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class OpenAIAPIController extends Controller
{

	/**
	 * Run an API Call.
	 *
	 * @param string $prompt A prompt for the Reqeust.
	 * @param string $engine The engine to use.
	 * @param array  $args   An array of arguments to pass to the API.
	 *
	 * @return string The text response from the API.
	 */
	public static function gpt3_completion($prompt, $model = 'text-davinci-003', $args = [])
	{

		$start_time = microtime(true);
		$default_args = [
			'temperature'       => 0.7,  // Randomness. 0 Makes it boring and repetitive.
			'max_tokens'        => 300,  // 100 is the max tokens allowed.
			'top_p'             => 0.75,  // A seive to remove low probability tokens.
			'frequency_penalty' => 0.5, // Penalize new words based on their existing frequency.
			'presence_penalty'  => 0.75,  // Likelihood of using new topics.
			'stop'              => [ env('CHAT_USER_NAME', 'USER' ) . ':', 'TARS:']
		];
		$args           = array_merge($default_args, $args);
		$args['prompt'] = $prompt;
		$args['model']  = $model;
		$result         = OpenAI::completions()->create($args);
		$end_time       = microtime(true);

		$text = $result['choices'][0]['text'];
		if ( env('APP_DEBUG', false ) ) {
			Log::info('Completion Execution Time: ' . ($end_time - $start_time) . ' seconds ***' . substr( $prompt, 0, 100 ));
		}
		return $text;

		/*

		$max_retry = 5;
		$retry = 0;
		while ($retry <= $max_retry) {
			try {
				$client = new Client();
				$response = $client->post('https://api.openai.com/v1/engines/' . $engine . '/completions', [
					'headers' => [
						'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
						'Content-Type'  => 'application/json',
					],
					'json' => $args,
				]);
				$result = json_decode($response->getBody(), true);
				$text = trim($result['choices'][0]['text']);
				$text = preg_replace('/[\r\n]+/', "\n", $text);
				$text = preg_replace('/[\t ]+/', ' ', $text);

				/*
				$filename = time() . '_gpt3.txt';
				if (!is_dir('gpt3_logs')) {
					mkdir('gpt3_logs');
				}
				save_file('gpt3_logs/' . $filename, $prompt . "\n\n==========\n\n" . $text);
				*

				return $text;
			} catch (\Exception $oops) {
				$retry++;
				if ($retry > $max_retry) {
					return "GPT3 error: " . $oops->getMessage();
				}
				echo "Error communicating with OpenAI: " . $oops->getMessage() . "\n";
				sleep(1);
			}
		}
		*/
	}

	/**
	 * Get the embedding from the API using the text-embedding-ada-002 engine.
	 *
	 * @param string $content The text to get the embedding for.
	 *
	 * @return array The embeding (vector).
	 */
	public static function gpt3_embedding($content, $engine = 'text-embedding-ada-002')
	{
		$start_time  = microtime(true);
		$max_retries = 5;
		$retry       = 0;
		while( $retry <= $max_retries ) {
			try {
				$result = OpenAI::embeddings()->create([
					'model' => $engine,
					'input' => $content,
				]);
				$end_time = microtime(true);
				if ( env('APP_DEBUG', false ) ) {
					Log::info('Completion Execution Time: ' . ($end_time - $start_time) . ' seconds ***' . substr( $content, 0, 100 ));
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

		$end_time = microtime(true);
		if ( env('APP_DEBUG', false ) ) {
			Log::info('Completion ERROR Execution Time: ' . ($end_time - $start_time) . ' seconds ***' . substr( $content, 0, 100 ));
		}

		return json_encode([]);
	}
}
