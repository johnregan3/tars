<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Memory;
use App\Http\Controllers\OpenAIAPIController as OpenAI;

class TarsEmbed extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tars:embed';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send OpenAI API Requests to get Embeddings';

	/**
	 * Execute the console command.
	 *
	 * This command is run by the scheduler to get embeddings for Memories.
	 *
	 * @return int
	 */
	public function handle()
	{
		ray( 'wth, man?' )->orange();
		$default_vector = json_encode(array_fill(0, 1536, 0));
		// Get Memories with empty embedding, starting with the most recent.
		$memories = Memory::where('embedding', $default_vector)->orderBy('created_at', 'desc')->get();
		ray( getType($memories) )->orange();

		// Loop through the memories and run the OpenAI API request for the embeddings.
		foreach ($memories as $memory) {
			$embedding = OpenAI::gpt3Embedding($memory->content);
			$memory->embedding = $embedding;
			$memory->save();
		}
		return Command::SUCCESS;
	}
}
