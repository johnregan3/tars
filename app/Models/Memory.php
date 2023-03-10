<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Summary;
use Illuminate\Support\Facades\DB;

class Memory extends Model
{
	use HasFactory;

	protected $fillable = ['speaker_id', 'content', 'embedding'];

	public function summaries()
	{
		return $this->belongsToMany(Summary::class);
	}

	public function speaker()
    {
        return $this->belongsTo(User::class, 'speaker_id');
    }


	/**
	 * Fetch the 10 memories with the smallest distance to the current memory.
	 * This distance is calculated using the cosine similarity of the memory embeddings.
	 *
	 * Requires the ankane/pgvector composer package to be installed as a PostgreSQL extension.
	 *
	 * @param int $limit The number of memories to return.
	 *
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function relatedMemories($limit = 10)
	{
		// Get the embedding of the current memory
		$currentEmbedding = $this->getAttribute('embedding');

		// Build the query to find the 10 memories with the highest cosine similarity to the current memory
		$similarMemories = Memory::select('*', DB::raw("cosine_distance(embedding, '{$currentEmbedding}') as similarity"))
			->where('id', '!=', $this->id)
			->orderBy('similarity', 'desc')
			->orderBy('created_at', 'desc')
			->limit($limit)
			->get();

		return $similarMemories;
	}
}
