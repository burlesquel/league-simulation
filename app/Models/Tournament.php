<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'current_week'];
    protected $appends = ['standings', 'teams'];

    public function matches()
    {
        return $this->hasMany(Game::class);
    }

    public function getStandingsAttribute()
    {
        $matches = $this->matches()->get();
        $teamStats = [];
        $teamInfo = [];

        foreach ($matches as $match) {
            // Get both teams
            $team1_id = $match->team1_id;
            $team2_id = $match->team2_id;
            $goals1 = $match->team1_goals;
            $goals2 = $match->team2_goals;

            // Initialize teams in standings if not already set
            if (!isset($teamStats[$team1_id])) {
                $teamStats[$team1_id] = ['team_id' => $team1_id, 'played' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0, 'goals_for' => 0, 'goals_against' => 0, 'goal_difference' => 0, 'points' => 0];
            }
            if (!isset($teamStats[$team2_id])) {
                $teamStats[$team2_id] = ['team_id' => $team2_id, 'played' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0, 'goals_for' => 0, 'goals_against' => 0, 'goal_difference' => 0, 'points' => 0];
            }

            if ($match->finished) {
                // Update match statistics for both teams
                $teamStats[$team1_id]['played']++;
                $teamStats[$team2_id]['played']++;

                $teamStats[$team1_id]['goals_for'] += $goals1;
                $teamStats[$team1_id]['goals_against'] += $goals2;
                $teamStats[$team2_id]['goals_for'] += $goals2;
                $teamStats[$team2_id]['goals_against'] += $goals1;
                // Calculate results
                if ($goals1 > $goals2) {
                    // Team 1 wins
                    $teamStats[$team1_id]['wins']++;
                    $teamStats[$team1_id]['points'] += 3;
                    $teamStats[$team2_id]['losses']++;
                } elseif ($goals1 < $goals2) {
                    // Team 2 wins
                    $teamStats[$team2_id]['wins']++;
                    $teamStats[$team2_id]['points'] += 3;
                    $teamStats[$team1_id]['losses']++;
                } else {
                    // Draw
                    $teamStats[$team1_id]['draws']++;
                    $teamStats[$team2_id]['draws']++;
                    $teamStats[$team1_id]['points'] += 1;
                    $teamStats[$team2_id]['points'] += 1;
                }
            }

            // Update goal difference
            $teamStats[$team1_id]['goal_difference'] = $teamStats[$team1_id]['goals_for'] - $teamStats[$team1_id]['goals_against'];
            $teamStats[$team2_id]['goal_difference'] = $teamStats[$team2_id]['goals_for'] - $teamStats[$team2_id]['goals_against'];
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

    public function getTeamsAttribute()
    {
        $matches = $this->matches()->get();
        $teams = [];
        foreach ($matches as $match) {
            // Set team information to be used in front
            if (!isset($teams[$match->team1_id])) {
                $teams[$match->team1_id] = $match->team1;
            }
            if (!isset($teams[$match->team2_id])) {
                $teams[$match->team2_id] = $match->team2;
            }
        }
        return $teams;
    }
}
