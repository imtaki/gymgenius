import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '../../../components/ui/card';
import { Dumbbell, TrendingUp, Target, Flame, Calendar, Award } from 'lucide-react';
import DashboardCharts from '../../../components/sections/DashboardCharts';
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
      { day: 'Sun', volume: 0, calories: 0 },
    ],
    trainingSplit: [
      { name: 'Push', value: 35, color: '#3b82f6' },
      { name: 'Pull', value: 30, color: '#8b5cf6' },
      { name: 'Legs', value: 25, color: '#ec4899' },
      { name: 'Upper', value: 10, color: '#f54e0b' },
    ],
    nutrition: [
      { name: 'Protein', value: 180, target: 200 },
      { name: 'Carbs', value: 220, target: 250 },
      { name: 'Fats', value: 65, target: 70 },
    ],
    recentWorkouts: [
      { id: 1, name: 'Push Day', exercises: 8, duration: '65 min', date: 'Today' },
      { id: 2, name: 'Pull Day', exercises: 7, duration: '58 min', date: 'Yesterday' },
      { id: 3, name: 'Leg Day', exercises: 9, duration: '72 min', date: '2 days ago' },
    ],
    stats: [
      { label: 'Weekly Volume', value: '18.9k', unit: 'kg', icon: 'Dumbbell', trend: '+12%', color: 'text-blue-600' },
      { label: 'Calories Burned', value: '3,090', unit: 'kcal', icon: 'Flame', trend: '+8%', color: 'text-orange-600' },
      { label: 'Workout Streak', value: '12', unit: 'days', icon: 'Award', trend: 'Personal Best!', color: 'text-purple-600' },
      { label: 'Avg Duration', value: '65', unit: 'min', icon: 'Calendar', trend: '+5 min', color: 'text-green-600' },
    ],
  };
}

const iconMap = {
  Dumbbell,
  Flame,
  Award,
  Calendar,
};

export default async function Dashboard() {
  const data = await getDashboardData();
  return (
    <div className="min-h-screen p-6">
      <div className="max-w-7xl mx-auto space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold">Dashboard</h1>
            <p className=" mt-1">Track your fitness journey</p>
          </div>
          <DashPeriodSelector />
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {data.stats.map((stat, idx) => {
            const Icon = iconMap[stat.icon as keyof typeof iconMap];
            return (
              <Card key={idx} className="border-0 shadow-lg hover:shadow-xl transition-shadow">
                <CardContent className="p-6">
                  <div className="flex items-start justify-between">
                    <div>
                      <p className="text-sm  font-medium">{stat.label}</p>
                      <div className="flex items-baseline gap-2 mt-2">
                        <h3 className="text-3xl font-bold ">{stat.value}</h3>
                        <span className="text-sm ">{stat.unit}</span>
                      </div>
                      <p className="text-xs text-green-600 font-medium mt-2 flex items-center gap-1">
                        <TrendingUp className="w-3 h-3" />
                        {stat.trend}
                      </p>
                    </div>
                    <div className={`p-3 rounded-xl bg-slate-50 ${stat.color}`}>
                      <Icon className="w-6 h-6" />
                    </div>
                  </div>
                </CardContent>
              </Card>
            );
          })}
        </div>

        <DashboardCharts
          workoutProgress={data.workoutProgress}
          trainingSplit={data.trainingSplit}
          nutrition={data.nutrition}
        />

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card className="border-0 shadow-lg lg:col-span-2">
            <CardHeader>
              <CardTitle className="">Recent Workouts</CardTitle>
              <CardDescription>Your latest training sessions</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {data.recentWorkouts.map((workout) => (
                  <div 
                    key={workout.id} 
                    className="flex items-center justify-between p-4 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer border border-slate-100"
                  >
                    <div className="flex items-center gap-4">
                      <div className="w-12 h-12 rounded-lg  flex items-center justify-center">
                        <Dumbbell className="w-6 h-6 text-blue-600" />
                      </div>
                      <div>
                        <h4 className="font-semibold ">{workout.name}</h4>
                        <p className="text-sm ">
                          {workout.exercises} exercises • {workout.duration}
                        </p>
                      </div>
                    </div>
                    <span className="text-sm font-medium ">{workout.date}</span>
                  </div>
                ))}
              </div>
              <button className="w-full mt-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                View All Workouts
              </button>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}