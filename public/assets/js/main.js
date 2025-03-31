var { createApp, ref, reactive, computed, watch, onMounted } = Vue

const app = createApp({
    setup() {
        
        const tournaments = ref([])

        const teamDetails = computed(()=>{
            return tournaments.value.reduce((acc, curr) => {return {...acc, ...curr.teams}}, {})
        })

        const globalStandings = computed(()=>{
            let globalRankings = {}
            let allStandings = Array.from(tournaments.value, tournament => tournament.standings).flat()
            for(let standing of allStandings){
                if(globalRankings[standing.team_id]){
                    let prev = globalRankings[standing.team_id]
                    let curr = standing
                    globalRankings[standing.team_id] = {
                        "team_id": prev.team_id,
                        "played": prev.played + curr.played,
                        "wins": prev.wins + curr.wins,
                        "draws": prev.draws + curr.draws,
                        "losses": prev.losses + curr.losses,
                        "goals_for": prev.goals_for + curr.goals_for,
                        "goals_against": prev.goals_against + curr.goals_against,
                        "goal_difference": prev.goal_difference + curr.goal_difference,
                        "points": prev.points + curr.points
                    }
                }
                else{
                    globalRankings[standing.team_id] = standing
                }
            }
            return Object.values(globalRankings).sort((a,b) => a.wins - b.wins)
        })

        onMounted(async () => {
            let tournamentsResponse = await (await fetch("/tournaments")).json()

            console.log(tournamentsResponse);
            
            tournaments.value = tournamentsResponse.tournaments
        })

        return {
            tournaments,
            teamDetails,
            globalStandings
        }
    },
})
app.config.compilerOptions.delimiters = ['{v', 'v}']
const appContext = app.mount('#app')