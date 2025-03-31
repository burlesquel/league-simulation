<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo', 'stadium', 'strength'];

    public function matches()
    {
        return $this->belongsToMany(Game::class);
    }
}
