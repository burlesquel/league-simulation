<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'current_week'];
    protected $appends = ['standings'];

    public function matches()
    {
        return $this->hasMany(Game::class);
    }

    public function getStandingsAttribute()
    {
        $matches = $this->matches()->get();
        $teamStats = [];

        foreach ($matches as $match) {
            // Get both teams
            $team1 = $match->team1_id;
            $team2 = $match->team2_id;
            $goals1 = $match->team1_goals;
            $goals2 = $match->team2_goals;

            // Initialize teams in standings if not already set
            if (!isset($teamStats[$team1])) {
                $teamStats[$team1] = ['team_id' => $team1, 'played' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0, 'goals_for' => 0, 'goals_against' => 0, 'goal_difference' => 0, 'points' => 0];
            }
            if (!isset($teamStats[$team2])) {
                $teamStats[$team2] = ['team_id' => $team2, 'played' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0, 'goals_for' => 0, 'goals_against' => 0, 'goal_difference' => 0, 'points' => 0];
            }

            if ($match->finished) {
                // Update match statistics for both teams
                $teamStats[$team1]['played']++;
                $teamStats[$team2]['played']++;

                $teamStats[$team1]['goals_for'] += $goals1;
                $teamStats[$team1]['goals_against'] += $goals2;
                $teamStats[$team2]['goals_for'] += $goals2;
                $teamStats[$team2]['goals_against'] += $goals1;
                // Calculate results
                if ($goals1 > $goals2) {
                    // Team 1 wins
                    $teamStats[$team1]['wins']++;
                    $teamStats[$team1]['points'] += 3;
                    $teamStats[$team2]['losses']++;
                } elseif ($goals1 < $goals2) {
                    // Team 2 wins
                    $teamStats[$team2]['wins']++;
                    $teamStats[$team2]['points'] += 3;
                    $teamStats[$team1]['losses']++;
                } else {
                    // Draw
                    $teamStats[$team1]['draws']++;
                    $teamStats[$team2]['draws']++;
                    $teamStats[$team1]['points'] += 1;
                    $teamStats[$team2]['points'] += 1;
                }
            }

            // Update goal difference
            $teamStats[$team1]['goal_difference'] = $teamStats[$team1]['goals_for'] - $teamStats[$team1]['goals_against'];
            $teamStats[$team2]['goal_difference'] = $teamStats[$team2]['goals_for'] - $teamStats[$team2]['goals_against'];
        }

        // Convert to array and sort
        $standings = array_values($teamStats);
        usort($standings, function ($a, $b) {
            return $b['points'] <=> $a['points']
                ?: $b['goal_difference'] <=> $a['goal_difference']
                ?: $b['goals_for'] <=> $a['goals_for'];
        });

        return $standings;
    }
}
