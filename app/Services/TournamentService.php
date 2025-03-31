<?php
namespace App\Services;
use App\Models\Game;
use App\Models\Tournament;
use App\Models\Team;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;

class TournamentService
{
    public $dummy_teams = [
        [ 
            "name" => "Westford Lions", 
            "logo" => "westford.png", 
            "stadium" => "Lionsgate Stadium"
        ],
        [ 
            "name" => "Eastbridge United", 
            "logo" => "eastbridge.png", 
            "stadium" => "Bridgeview Arena"
        ],
        [ 
            "name" => "Southport Strikers", 
            "logo" => "southport.png", 
            "stadium" => "Southport Park"
        ],
        [ 
            "name" => "Ashbourne Falcons", 
            "logo" => "ashbourne.png", 
            "stadium" => "Falconridge Arena"
        ],
        [ 
            "name" => "Blackhill Warriors", 
            "logo" => "blackhill.png", 
            "stadium" => "Ironclad Stadium"
        ],
        [ 
            "name" => "Redmere Rangers", 
            "logo" => "redmere.png", 
            "stadium" => "Redmere Grounds"
        ],
        [ 
            "name" => "Grimshaw Titans", 
            "logo" => "grimshaw.png", 
            "stadium" => "Titan Stadium"
        ],
        [ 
            "name" => "Lancaster Wolves", 
            "logo" => "lancaster.png", 
            "stadium" => "Wolves' Den"
        ],
        [ 
            "name" => "Whitestone Wanderers", 
            "logo" => "whitestone.png", 
            "stadium" => "Stonegate Field"
        ]
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
                    'stadium' => $dummy_team['stadium'],
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
                $team1 = $teams[$i];
                $team2 = $teams[$totalTeams - 1 - $i];
    
                // Ensure home and away matches are created
                $matches[] = Game::create([
                    'tournament_id' => $tournament->id,
                    'week' => $round + 1,
                    'team1_id' => $team1["id"],
                    'team2_id' => $team2["id"]
                ]);
    
                // Reverse the home and away for the second half of the season
                $matches[] = Game::create([
                    'tournament_id' => $tournament->id,
                    'week' => $round + $totalTeams,
                    'team1_id' => $team2["id"],
                    'team2_id' => $team1["id"]
                ]);
            }
    
            // Rotate teams except the first one (standard round-robin algorithm)
            array_splice($teamIds, 1, 0, array_pop($teamIds));
        }
    
        return $matches;
    }

    public function simulateMatch(Tournament $tournament){
        $nextWeek = $tournament->current_week + 1;
        foreach ($tournament->matches as $match){
            if($match->week === $nextWeek && !$match->finished){
                // Play the game here...
                // 1- If the team is the home team, we add 10 strength boost
                if($match->home_team->id === $match->team1_id){
                    $match->team1->strength = + 10;
                }
                $team1Goals = rand(0, max(1, $match->team1->strength / 20));
                $team2Goals = rand(0, max(1, $match->team2->strength / 20));
                // 2- We add 10 strength for the winner team, substract 10 strength for the loser team
                if($team1Goals > $team2Goals){
                    $match->team1->strength = $match->team1->strength + 10;
                }
                else if($team2Goals > $team1Goals){
                    $match->team2->strength = $match->team2->strength + 10;
                }
                $match->team1->save();
                $match->team2->save();
                $match->update([
                    'team1_goals' => $team1Goals,
                    'team2_goals' => $team2Goals
                ]);
            }
        };
        $tournament->update([
            "current_week" => $nextWeek
        ]);
    }
}