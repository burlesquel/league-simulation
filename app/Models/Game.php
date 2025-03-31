<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['tournament_id', 'week', 'team1_id', 'team2_id', 'team1_goals', 'team2_goals'];

    protected $appends = ['finished'];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function home_team()
    {
        return $this->team1();
    }

    public function getFinishedAttribute(){
        return $this->team1_goals !== null && $this->team2_goals !== null;
    }
}
