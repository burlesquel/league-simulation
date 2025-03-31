<?php

namespace App\Http\Controllers;
use App\Services\TournamentService;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\Game;

class TournamentController extends Controller
{
    protected $tournamentService;

    public function __construct(TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    public function create()
    {
        // We generate teams if they don't exist
        $teams = $this->tournamentService->generateTeams();
        
        $tournament = $this->tournamentService->createTournament("New Tournament");
        // We select 4 random teams among all teams
        $tournament_teams = [];
        $random_keys = array_rand($teams, 4);
        foreach ($random_keys as $key) {
            $tournament_teams[] = $teams[$key];
        }
        // We generate matches. Each team will make 2 matches with every other team
        $this->tournamentService->generateMatches($tournament, $tournament_teams);
        // We refetch the tournament data with matches and standings
        // $tournament = Tournament::with('matches')->findOrFail($tournament->id)->append(['standings', 'teams']);
        return response()->json(['message' => 'Tournament created!', 'tournament_id' => $tournament->id]);
    }

    public function simulate($id)
    {
        $tournament = Tournament::findOrFail($id);
        $this->tournamentService->simulateMatches($tournament);
        $tournament = Tournament::with('matches')->findOrFail($tournament->id)->append(['standings', 'teams']);
        return response()->json(['message' => 'Tournament simulated!', 'matches' => $tournament]);
    }

    public function tournaments()
    {
        $all_tournaments = Tournament::all();
        return response()->json(['message' => 'Tournaments', 'tournaments' => $all_tournaments]);
    }

    public function tournament($id)
    {
        $tournament = Tournament::with('matches')->findOrFail($id)->append(['standings']);
        return response()->json(['message' => 'Tournament', 'tournament' => $tournament]);
    }

    public function globalRanking(){
        
    }
}
