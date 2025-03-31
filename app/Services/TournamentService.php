<?php
namespace App\Services;
use App\Models\Game;
use App\Models\Tournament;
use App\Models\Team;

class TournamentService
{
    public $dummy_teams = [
        [ "name" => "Westford Lions", "logo" => "westford.png" ],
        [ "name" => "Eastbridge United", "logo" => "eastbridge.png" ],
        [ "name" => "Southport Strikers", "logo" => "southport.png" ],
        [ "name" => "Ashbourne Falcons", "logo" => "ashbourne.png" ],
        [ "name" => "Blackhill Warriors", "logo" => "blackhill.png" ],
        [ "name" => "Redmere Rangers", "logo" => "redmere.png" ],
        [ "name" => "Grimshaw Titans", "logo" => "grimshaw.png" ],
        [ "name" => "Lancaster Wolves", "logo" => "lancaster.png" ],
        [ "name" => "Whitestone Wanderers", "logo" => "whitestone.png" ]
    ];
    public function createTournament(string $name)
    {
        return Tournament::create(['name' => $name]);
    }

    public function generateTeams()
    {
        if(empty(Team::first())){
            foreach ($this->dummy_teams as $dummy_team) {
                Team::create([
                    'name' => $dummy_team['name'],
                    'logo' => $dummy_team['logo'],
                    'strength' => rand(10, 100)
                ]);
            }
        }
        $teams = Team::all()->toArray();
        return $teams;
    }
    public function generateMatches(Tournament $tournament, array $teams)
    {
        $matches = [];
        $totalTeams = count($teams);
        $totalRounds = ($totalTeams - 1) * 2; // Each team plays twice against each other
        $gamesPerWeek = $totalTeams / 2; // 2 games per week
    
        // Create an array of team IDs
        $teamIds = array_map(fn($team) => $team["id"], $teams);
    
        // Generate first round-robin schedule
        for ($round = 0; $round < $totalTeams - 1; $round++) {
            for ($i = 0; $i < $gamesPerWeek; $i++) {
                $team1 = $teamIds[$i];
                $team2 = $teamIds[$totalTeams - 1 - $i];
    
                // Ensure home and away matches are created
                $matches[] = Game::create([
                    'tournament_id' => $tournament->id,
                    'week' => $round + 1,
                    'team1_id' => $team1,
                    'team2_id' => $team2
                ]);
    
                // Reverse the home and away for the second half of the season
                $matches[] = Game::create([
                    'tournament_id' => $tournament->id,
                    'week' => $round + $totalTeams,
                    'team1_id' => $team2,
                    'team2_id' => $team1
                ]);
            }
    
            // Rotate teams except the first one (standard round-robin algorithm)
            array_splice($teamIds, 1, 0, array_pop($teamIds));
        }
    
        return $matches;
    }
    public function simulateMatches(Tournament $tournament)
    {
        foreach ($tournament->matches as $match) {
            // If there are scores, that means this match was already played
            if ($match->finished) {
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