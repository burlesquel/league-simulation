<x-layout>
    <div class="container-fluid" id="app">
        <div class="row align-items-center" v-if="tournament">
            <div class="col-lg-4 col-12  text-center">
                <h1>Leauge Table</h1>
                <h4>Current week: {v currentWeek v}</h4>
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
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(standing, index) of standings" :set="teamInfo = teamDetails[standing.team_id]">
                            <th scope="row">{v index v}</th>
                            <td style="text-align: left"><img style="width:30px" :src="`/logos/${teamInfo.logo}`"
                                    alt="">{v teamInfo.name v}</td>
                            <td>{v standing.wins * 3 + standing.draws v}</td>
                            <td>{v standing.played v}</td>
                            <td>{v standing.wins v}</td>
                            <td>{v standing.losses v}</td>
                            <td>{v standing.draws v}</td>
                            <td>{v standing.goal_difference v}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4 col-12 text-center">
                <template v-if="currentWeek > 0">
                    <h2>This Week's Matches</h2>
                    <table class="table table-bordered">
                        <tbody>
                            <tr v-for="match of thisWeeksMatches">
                                <td class="text-end">{v teamDetails[match.team1_id].name v} <img style="width:30px"
                                        :src="`/logos/${teamDetails[match.team1_id].logo}`" alt=""></td>
                                <td style="font-weight: 600">{v match.team1_goals v} - {v match.team2_goals v}</td>
                                <td class="text-start"><img style="width:30px"
                                        :src="`/logos/${teamDetails[match.team2_id].logo}`" alt=""> {v teamDetails[match.team2_id].name v}</td>
                            </tr>
                        </tbody>
                    </table>
                </template>
                <template v-if="currentWeek < maxWeeks">
                    <h2>Next Week's Matches</h2>
                    <table class="table table-bordered">
                        <tbody>
                            <tr v-for="match of nextWeeksMatches">
                                <td class="text-end">{v teamDetails[match.team1_id].name v} <img style="width:30px"
                                        :src="`/logos/${teamDetails[match.team1_id].logo}`" alt=""></td>
                                <td style="font-weight: 600">{v match.team1_goals v} - {v match.team2_goals v}</td>
                                <td class="text-start"><img style="width:30px"
                                        :src="`/logos/${teamDetails[match.team2_id].logo}`" alt=""> {v teamDetails[match.team2_id].name v}</td>
                            </tr>
                        </tbody>
                    </table>
                </template>
                <div class="d-flex flex-row justify-content-around mt-2">
                    <button :disabled="maxWeeks === currentWeek" @click="simulate()" class="btn btn-primary">Play Next
                        Game</button>
                    <button :disabled="maxWeeks === currentWeek" @click="simulate(true)" class="btn btn-success">Play
                        All Games</button>
                </div>
            </div>
            <div class="col-lg-4 col-12 text-center">
                <h1>Predictions</h1>
                <table class="table table-bordered">
                    <tbody>
                        <tr v-for="team of teamDetails">
                            <td class="text-end">{v team.name v} <img style="width:30px"
                                    :src="`/logos/${team.logo}`" alt=""></td>
                            <td>{v predictions[team.id].toFixed(1) v}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="tournament_id" hidden>{{ $id }}</div>
    </div>
    @push('scripts')
        <script src="{{ asset('assets/js/tournament.js') }}"></script>
    @endpush
</x-layout>
