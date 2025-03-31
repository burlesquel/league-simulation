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
        $tournament = $this->tournamentService->createTournament("New Tournament");
        // We generate teams if they don't exist
        $teams = $this->tournamentService->generateTeams();
        // We select 4 random teams among all teams
        $tournament_teams = [];
        $random_keys = array_rand($teams, 4);
        foreach ($random_keys as $key) {
            $tournament_teams[] = $teams[$key];
        }
        // We generate matches. Each team will make 2 matches with every other team
        $this->tournamentService->generateMatches($tournament, $tournament_teams);
        // We refetch the tournament data with matches and standings
        $tournament = Tournament::with('matches')->findOrFail($tournament->id)->append(['standings']);
        return response()->json(['message' => 'Tournament created!', 'tournament' => $tournament]);
    }

    public function simulate($id)
    {
        $tournament = Tournament::findOrFail($id);
        $this->tournamentService->simulateMatches($tournament);

        return response()->json(['message' => 'Tournament simulated!', 'matches' => $tournament->g]);
    }

    public function tournaments(){
        $all_tournaments = Tournament::all();
        return response()->json(['message' => 'Tournaments', 'tournaments' => $all_tournaments]);
    }

    public function tournament($id){
        $tournament = Tournament::with('matches')->findOrFail($id)->append(['standings']);
        return response()->json(['message' => 'Tournament', 'tournament' => $tournament]);
    }

    public function standings($id)
    {
        $tournament = Tournament::findOrFail($id);

    }
}
