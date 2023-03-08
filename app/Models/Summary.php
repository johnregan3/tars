<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Memory;

class Summary extends Model
{
    use HasFactory;

	protected $fillable = [ 'text', 'timestamps', 'vector' ];

	public function memories()
    {
        return $this->belongsToMany(Memory::class);
    }
}
