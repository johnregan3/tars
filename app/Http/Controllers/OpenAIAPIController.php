<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memory;
use App\Models\Summary;
use Inertia\Inertia;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIAPIController extends Controller
{

	/**
	 * Run an API Call.
	 *
	 * @param string $prompt A prompt to for the Reqeust.
	 * @param string $engine The engine to use.
	 * @param array  $args   An array of arguments to pass to the API.
	 *
	 * @return string The text response from the API.
	 */
	public static function gpt3_completion($prompt, $model = 'text-davinci-003', $args = [])
	{

		$default_args = [
			'temperature'       => 0.0,
			'max_tokens'        => 400,
			'top_p'             => 1.0,
			'frequency_penalty' => 0.0,
			'presence_penalty'  => 0.0,
			'stop'              => ['JOHN:', 'TARS:']
		];
		$args = array_merge($default_args, $args);
		$args['prompt'] = $prompt;
		$args['model'] = $model;

		$result = OpenAI::completions()->create($args);
		return $result['choices'][0]['text'];

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
		$result = OpenAI::embeddings()->create([
			'model' => $engine,
			'input' => $content,
		]);

		return json_encode($result->embeddings[0]->embedding);
	}
}
