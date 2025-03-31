var { createApp, ref, reactive, computed, watch, onMounted } = Vue

const app = createApp({
    setup() {
        const tournamentId = ref(null)
        const tournament = ref({})

        const maxWeeks = computed(() => {
            return Math.max(...Array.from(tournament.value.matches ?? [], match => match.week)) ?? 0
        })

        const currentWeek = computed(() => {
            return tournament.value.current_week ?? 0
        })

        const teamDetails = computed(() => {
            return tournament.value.teams ?? []
        })

        const thisWeeksMatches = computed(() => {
            if (tournament.value.matches) {
                return tournament.value.matches.filter(match => match.week === tournament.value.current_week)
            }
        })

        const nextWeeksMatches = computed(() => {
            if (tournament.value.matches) {
                return tournament.value.matches.filter(match => match.week === tournament.value.current_week + 1)
            }
        })

        const standings = computed(() => {
            if (tournament.value.standings) {
                return tournament.value.standings.sort((a, b) => b.points - a.points)
            }
            else {
                return []
            }
        })

        const predictions = computed(() => {
            const { standings, teams, matches, current_week } = tournament.value;

            // 1. Calculate each team's effective strength (including recent boosts)
            const teamEffectiveStrengths = {};
            const teamStandings = {};

            // Initialize with base strength and process standings
            standings.forEach(teamStanding => {
                const team = teams[teamStanding.team_id];
                teamEffectiveStrengths[teamStanding.team_id] = team.strength;
                teamStandings[teamStanding.team_id] = teamStanding;
            });

            // 2. Process recent matches to account for strength changes
            matches.slice().reverse().forEach(match => {
                if (match.finished && match.week >= current_week - 3) { // Only consider recent matches
                    // Apply home boost
                    const homeBoost = 10;

                    let team1Strength = teams[match.team1_id].strength + homeBoost
                    let team2Strength = teams[match.team1_id].strength

                    // Apply winner/loser strength changes
                    if (match.team1_goals > match.team2_goals) {
                        team1Strength += 10;
                        team2Strength = Math.max(1, team2Strength - 10);
                    } else if (match.team2_goals > match.team1_goals) {
                        team2Strength += 10;
                        team1Strength = Math.max(1, team1Strength - 10);
                    }

                    // Update effective strengths (average with existing value)
                    teamEffectiveStrengths[match.team1_id] =
                        (teamEffectiveStrengths[match.team1_id] + team1Strength) / 2;
                    teamEffectiveStrengths[match.team2_id] =
                        (teamEffectiveStrengths[match.team2_id] + team2Strength) / 2;
                }
            });

            // 3. Calculate prediction scores based on multiple factors
            const teamScores = {};
            let totalScore = 0;

            standings.forEach(teamStanding => {
                const teamId = teamStanding.team_id;
                const standing = teamStandings[teamId];

                // Current points (40% weight)
                const pointsScore = standing.points * 0.4;

                // Effective strength (30% weight)
                const strengthScore = teamEffectiveStrengths[teamId] * 0.3;

                // Goal difference (20% weight)
                const goalDiffScore = standing.goal_difference * 0.2;

                // Form - points per match (10% weight)
                const formScore = (standing.points / standing.played) * 10 * 0.1;

                // Combined score
                const total = pointsScore + strengthScore + goalDiffScore + formScore;

                teamScores[teamId] = total;
                totalScore += total;
            });

            // 4. Convert to probabilities
            const predictions = {};
            standings.forEach(teamStanding => {
                const teamId = teamStanding.team_id;
                predictions[teamId] = (teamScores[teamId] / totalScore) * 100;
            });

            return predictions;
        })

        async function fetchTournament() {
            let tournamentResponse = await $.get("/tournament/" + tournamentId.value)
            tournament.value = tournamentResponse.tournament
        }

        async function simulate(simulate_all = false) {
            let tournamentResponse = await $.get(`/tournament/${tournamentId.value}/simulate${simulate_all ? '?simulate_all=true' : ''}`)
            tournament.value = tournamentResponse.tournament
        }

        onMounted(async () => {
            tournamentId.value = document.getElementById("tournament_id").innerText
            await fetchTournament()
        })

        return {
            tournamentId,
            tournament,
            standings,
            maxWeeks,
            currentWeek,
            teamDetails,
            thisWeeksMatches,
            nextWeeksMatches,
            fetchTournament,
            simulate,
            predictions
        }
    },
})
app.config.compilerOptions.delimiters = ['{v', 'v}']
const appContext = app.mount('#app')