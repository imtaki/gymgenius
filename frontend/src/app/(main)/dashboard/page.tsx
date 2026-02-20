import { Dumbbell, TrendingUp, Target, Flame, Calendar, Award } from 'lucide-react';
import DashboardCharts   from '../../../components/sections/DashboardCharts';
import DashPeriodSelector from '../../../components/sections/DashPeriodSelector';

async function getDashboardData() {
  return {
    workoutProgress: [
      { day: 'Mon', volume: 2400, calories: 450 },
      { day: 'Tue', volume: 3200, calories: 520 },
      { day: 'Wed', volume: 2800, calories: 380 },
      { day: 'Thu', volume: 3600, calories: 610 },
      { day: 'Fri', volume: 3100, calories: 480 },
      { day: 'Sat', volume: 3800, calories: 650 },
      { day: 'Sun', volume: 0,    calories: 0   },
    ],
    trainingSplit: [
      { name: 'Push',  value: 35, color: '#a3e635' },
      { name: 'Pull',  value: 30, color: '#f97316' },
      { name: 'Legs',  value: 25, color: '#38bdf8' },
      { name: 'Upper', value: 10, color: '#e879f9' },
    ],
    nutrition: [
      { name: 'Protein', value: 180, target: 200 },
      { name: 'Carbs',   value: 220, target: 250 },
      { name: 'Fats',    value: 65,  target: 70  },
    ],
    recentWorkouts: [
      { id: 1, name: 'Push Day', exercises: 8, duration: '65 min', date: 'Today'     },
      { id: 2, name: 'Pull Day', exercises: 7, duration: '58 min', date: 'Yesterday' },
      { id: 3, name: 'Leg Day',  exercises: 9, duration: '72 min', date: '2 days ago'},
    ],
    stats: [
      { label: 'Weekly Volume',   value: '18.9k', unit: 'kg',   icon: 'Dumbbell', trend: '+12%',         accent: 'text-lime-400',   glow: 'shadow-lime-400/10'   },
      { label: 'Calories Burned', value: '3,090', unit: 'kcal', icon: 'Flame',    trend: '+8%',          accent: 'text-orange-400', glow: 'shadow-orange-400/10' },
      { label: 'Workout Streak',  value: '12',    unit: 'days', icon: 'Award',    trend: 'Personal Best', accent: 'text-fuchsia-400',glow: 'shadow-fuchsia-400/10'},
      { label: 'Avg Duration',    value: '65',    unit: 'min',  icon: 'Calendar', trend: '+5 min',       accent: 'text-sky-400',    glow: 'shadow-sky-400/10'    },
    ],
  };
}

const iconMap = { Dumbbell, Flame, Award, Calendar };

function StatCard({ stat }: { stat: ReturnType<typeof getDashboardData> extends Promise<infer T> ? T['stats'][0] : never }) {
  const Icon = iconMap[stat.icon as keyof typeof iconMap];
  return (
    <div className={`group relative overflow-hidden bg-zinc-900 border border-zinc-800 rounded-2xl p-5 hover:border-zinc-700 transition-all duration-300 shadow-lg ${stat.glow}`}>
      {/* subtle top-edge glow line */}
      <div className={`absolute top-0 left-6 right-6 h-px bg-gradient-to-r from-transparent via-current to-transparent opacity-0 group-hover:opacity-30 transition-opacity duration-500 ${stat.accent}`} />

      <div className="flex items-start justify-between">
        <div className="space-y-3">
          <p className="text-xs font-semibold text-zinc-500 uppercase tracking-widest">{stat.label}</p>
          <div className="flex items-baseline gap-1.5">
            <span className="text-3xl font-bold text-zinc-100 font-mono tracking-tight">{stat.value}</span>
            <span className="text-sm text-zinc-500 font-mono">{stat.unit}</span>
          </div>
          <div className={`flex items-center gap-1 text-xs font-semibold ${stat.accent}`}>
            <TrendingUp className="w-3 h-3" />
            {stat.trend}
          </div>
        </div>
        <div className={`p-2.5 rounded-xl bg-zinc-800 ${stat.accent} opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300`}>
          <Icon className="w-5 h-5" />
        </div>
      </div>
    </div>
  );
}

function RecentWorkoutsCard({ workouts }: { workouts: Awaited<ReturnType<typeof getDashboardData>>['recentWorkouts'] }) {
  return (
    <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 lg:col-span-2">
      <div className="mb-5">
        <h3 className="text-sm font-semibold text-zinc-100 uppercase tracking-widest">Recent Workouts</h3>
        <p className="text-xs text-zinc-500 mt-1">Your latest training sessions</p>
      </div>

      <div className="space-y-2">
        {workouts.map((workout) => (
          <div
            key={workout.id}
            className="group flex items-center justify-between p-4 rounded-xl bg-zinc-800/50 hover:bg-zinc-800 border border-transparent hover:border-zinc-700 transition-all duration-200 cursor-pointer"
          >
            <div className="flex items-center gap-4">
              <div className="w-10 h-10 rounded-xl bg-zinc-700 flex items-center justify-center group-hover:bg-lime-400/10 transition-colors">
                <Dumbbell className="w-5 h-5 text-zinc-400 group-hover:text-lime-400 transition-colors" />
              </div>
              <div>
                <h4 className="font-semibold text-zinc-100 text-sm">{workout.name}</h4>
                <p className="text-xs text-zinc-500 font-mono mt-0.5">
                  {workout.exercises} exercises · {workout.duration}
                </p>
              </div>
            </div>
            <span className={`text-xs font-semibold font-mono px-2.5 py-1 rounded-full ${
              workout.date === 'Today'
                ? 'bg-lime-400/10 text-lime-400'
                : 'bg-zinc-800 text-zinc-500'
            }`}>
              {workout.date}
            </span>
          </div>
        ))}
      </div>

      <button className="w-full mt-4 py-3 bg-lime-400 hover:bg-lime-300 text-zinc-900 rounded-xl text-sm font-bold uppercase tracking-widest transition-colors duration-200 shadow-lg shadow-lime-400/10">
        View All Workouts
      </button>
    </div>
  );
}

export default async function Dashboard() {
  const data = await getDashboardData();

  return (
    <div
      className="min-h-screen bg-zinc-950 p-6"
      style={{ fontFamily: "'DM Mono', 'Fira Code', ui-monospace, monospace" }}
    >
      <div className="max-w-7xl mx-auto space-y-6">

       
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-zinc-100 tracking-tight">Dashboard</h1>
            <p className="text-xs text-zinc-500 mt-1 uppercase tracking-widest">Track your fitness journey</p>
          </div>
          <DashPeriodSelector />
        </div>

        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {data.stats.map((stat, i) => (
            <StatCard key={i} stat={stat} />
          ))}
        </div>

       
        <DashboardCharts
          workoutProgress={data.workoutProgress}
          trainingSplit={data.trainingSplit}
          nutrition={data.nutrition}
        />

       
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <RecentWorkoutsCard workouts={data.recentWorkouts} />
        </div>

      </div>
    </div>
  );
}