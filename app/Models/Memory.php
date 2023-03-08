<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Summary;

class Memory extends Model
{
    use HasFactory;

	protected $fillable = ['speaker_id', 'message', 'vector'];

	public function notes()
    {
        return $this->belongsToMany(Summary::class);
    }
}
