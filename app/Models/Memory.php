<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Summary;
use App\Http\Controllers\OpenAIAPIController as OpenAI;
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
	 * When the content attribute is set, also set the embedding attribute.
	 *
	 * Note that this runs an API call when the model is saved.
	 *
	 * @param string $value The content of the memory.
	 */
	public function setContentAttribute($value) {
		$this->attributes['content'] = $value;
        $this->attributes['embedding'] = OpenAI::gpt3Embedding($value);
	}

	/**
	 * Fetch the 10 memories with the smallest distance to the current memory.
	 * This distance is calculated using the cosine similarity of the memory embeddings.
	 *
	 * Requires the ankane/pgvector composer package to be installed as
	 * a PostgreSQL extension.
	 *
	 * @todo Perhaps start by fetching the most relevant/related Summary, then
	 * fetch the memories associated with that summary.
	 *
	 * @param int $limit The number of memories to return.
	 *
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function relatedMemories($limit = 5)
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
