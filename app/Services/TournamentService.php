<?php

use App\Models\Game;
use App\Models\Tournament;
use App\Models\Team;

class TournamentServie
{
    public function createTournament(string $name)
    {
        return Tournament::create(['name' => $name]);
    }

    public function generateTeams($count = 4)
    {
        // Creating teams based on dummy Team names
        $teams = [];
        for ($i = 0; $i < $count; $i++) {
            $teams[] = Team::create([
                'name' => "Team " . chr(65 + $i), // A, B, C, D...
                'strength' => rand(50, 100)
            ]);
        }
        return $teams;
    }

    public function generateMatches(Tournament $tournament, array $teams)
    {
        // Here we generate the matches (will be revised to adjust match nums)
        $matches = [];
        for ($i = 0; $i < count($teams); $i++) {
            for ($j = $i + 1; $j < count($teams); $j++) {
                $matches[] = Game::create([
                    'tournament_id' => $tournament->id,
                    'team1_id' => $teams[$i]->id,
                    'team2_id' => $teams[$j]->id,
                ]);
            }
        }
        return $matches;
    }

    public function simulateMatches(Tournament $tournament)
    {
        foreach ($tournament->matches as $match) {
            // If there are scores, that means this match was already played
            if ($match->team1_goals || $match->team2_goals ) {
                continue;
            }

            // Will find a better real-life logic for playing
            $team1Goals = rand(0, max(1, $match->team1->strength / 20));
            $team2Goals = rand(0, max(1, $match->team2->strength / 20));

            // Save results
            $match->update([
                'team1_goals' => $team1Goals,
                'team2_goals' => $team2Goals
            ]);
        }
    }
}