<x-layout>
    <div class="container-fluid" id="app">
        <div class="row">
            <div class="col-lg-6 col-12  text-center">
                <h1>Tournaments</h1>
                <div class="d-flex flex-column">
                    <a :href="`/league/${tournament.id}`" v-for="tournament of tournaments" :set="leaderTeam = teamDetails[tournament.standings[0].team_id]" class="m-2 mx-5 text-decoration-none text-reset">
                        <div class="p-2 d-flex flex-row justify-content-between align-items-center border" :class="{'tournament-passive': tournament.current_week === 0, 'tournament-active': tournament.current_week > 0 && tournament.current_week < 6, 'tournament-finished': tournament.current_week === 6}">
                            <div>
                                <span><i class="bi bi-calendar-week"></i> {v tournament.current_week v}</span>
                            </div>
                            <div class="d-flex flex-column">
                                <span>{v tournament.name v}</span>
                                <span v-if="tournament.current_week === 0">Starts soon</span>
                                <span v-else-if="tournament.current_week === 6">Completed</span>
                                <span v-else-if="tournament.current_week > 0">Ongoing</span>
                            </div>
                            <div>
                                <span><i class="bi bi-1-circle"></i> <img :title="leaderTeam.name" style="width:30px" :src="`/logos/${leaderTeam.logo}`" alt=""></span>
                            </div>
                        </div>
                    </a>
                    <div @click="createTournament" class="m-2 mx-5 p-4 bg-success text-white" style="cursor: pointer">
                        <i class="bi bi-plus-circle"></i> Create New Tournament
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12 text-center">
                <h1>Global Rank</h1>
                <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Rank</th>
                        <th style="text-align: left" scope="col">Team</th>
                        <th scope="col">Pts</th>
                        <th scope="col">P</th>
                        <th scope="col">W</th>
                        <th scope="col">L</th>
                        <th scope="col">D</th>
                        <th scope="col">GD</th>
                        <th scope="col">GA</th>
                        <th scope="col">GF</th>
                      </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(standing, index) of globalStandings" :set="teamInfo = teamDetails[standing.team_id]">
                            <th scope="row">{v index + 1 v}</th>
                            <td style="text-align: left"><img style="width:30px" :src="`/logos/${teamInfo.logo}`" alt="">{v teamInfo.name v}</td>
                            <td>{v standing.wins * 3 + standing.draws v}</td>
                            <td>{v standing.played v}</td>
                            <td>{v standing.wins v}</td>
                            <td>{v standing.losses v}</td>
                            <td>{v standing.draws v}</td>
                            <td>{v standing.goal_difference v}</td>
                            <td>{v standing.goal_against v}</td>
                            <td>{v standing.goal_from v}</td>
                        </tr>
                    </tbody>
                  </table>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="{{asset('assets/js/main.js')}}"></script>
    @endpush
</x-layout>
