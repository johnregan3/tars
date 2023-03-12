<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Memory;
use App\Http\Controllers\OpenAIAPIController as OpenAI;

class Summary extends Model
{
	use HasFactory;

	protected $fillable = ['content', 'embedding'];

	public function memories()
	{
		return $this->belongsToMany(Memory::class);
	}

	/**
	 * When the content attribute is set, also set the embedding attribute.
	 *
	 * Note that this runs an API call when the model is saved.
	 *
	 * @param string $value The content of the summary.
	 */
	public function setContentAttribute($value)
	{
		$this->attributes['content'] = $value;
		$this->attributes['embedding'] = OpenAI::gpt3Embedding($value);
	}
}
