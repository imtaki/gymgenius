"use client"; // for now, as we have interactivity
import React, { useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../../../components/ui/card';
import { LineChart, Line, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { Dumbbell, TrendingUp, Target, Flame, Calendar, Award } from 'lucide-react';

export default function Dashboard() {
  const [selectedPeriod, setSelectedPeriod] = useState('week');

  // API calls here (mocked data for now)
  const workoutProgress = [
    { day: 'Mon', volume: 2400, calories: 450 },
    { day: 'Tue', volume: 3200, calories: 520 },
    { day: 'Wed', volume: 2800, calories: 380 },
    { day: 'Thu', volume: 3600, calories: 610 },
    { day: 'Fri', volume: 3100, calories: 480 },
    { day: 'Sat', volume: 3800, calories: 650 },
    { day: 'Sun', volume: 0, calories: 0 },
  ];

  const trainingSplitData = [
    { name: 'Push', value: 35, color: '#3b82f6' },
    { name: 'Pull', value: 30, color: '#8b5cf6' },
    { name: 'Legs', value: 25, color: '#ec4899' },
    { name: 'Upper', value: 10, color: '#f54e0b' },
  ];

  const nutritionData = [
    { name: 'Protein', value: 180, target: 200 },
    { name: 'Carbs', value: 220, target: 250 },
    { name: 'Fats', value: 65, target: 70 },
  ];

  const recentWorkouts = [
    { id: 1, name: 'Push Day', exercises: 8, duration: '65 min', date: 'Today' },
    { id: 2, name: 'Pull Day', exercises: 7, duration: '58 min', date: 'Yesterday' },
    { id: 3, name: 'Leg Day', exercises: 9, duration: '72 min', date: '2 days ago' },
  ];

  const stats = [
    { label: 'Weekly Volume', value: '18.9k', unit: 'kg', icon: Dumbbell, trend: '+12%', color: 'text-blue-600' },
    { label: 'Calories Burned', value: '3,090', unit: 'kcal', icon: Flame, trend: '+8%', color: 'text-orange-600' },
    { label: 'Workout Streak', value: '12', unit: 'days', icon: Award, trend: 'Personal Best!', color: 'text-purple-600' },
    { label: 'Avg Duration', value: '65', unit: 'min', icon: Calendar, trend: '+5 min', color: 'text-green-600' },
  ];

  return (
    <div className="min-h-screen p-6">
      <div className="max-w-7xl mx-auto space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold">Dashboard</h1>
            <p className="text-slate-600 mt-1">Track your fitness journey</p>
          </div>
          <div className="flex gap-2">
            {['week', 'month', 'year'].map((period) => (
              <button
                key={period}
                onClick={() => setSelectedPeriod(period)}
                className={`px-4 py-2 rounded-lg font-medium transition-all ${
                  selectedPeriod === period
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-200'
                    : ' bg-white text-black hover:bg-slate-50'
                }`}
              >
                {period.charAt(0).toUpperCase() + period.slice(1)}
              </button>
            ))}
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {stats.map((stat, idx) => {
            const Icon = stat.icon;
            return (
              <Card key={idx} className="border-0 shadow-lg hover:shadow-xl transition-shadow">
                <CardContent className="p-6">
                  <div className="flex items-start justify-between">
                    <div>
                      <p className="text-sm  font-medium">{stat.label}</p>
                      <div className="flex items-baseline gap-2 mt-2">
                        <h3 className="text-3xl font-bold 0">{stat.value}</h3>
                        <span className="text-sm ">{stat.unit}</span>
                      </div>
                      <p className="text-xs  font-medium mt-2 flex items-center gap-1">
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

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <Card className="lg:col-span-2 border-0 shadow-lg">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Target className="w-5 h-5 " />
                Workout Progress
              </CardTitle>
              <CardDescription>Total volume lifted and calories burned this week</CardDescription>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={300}>
                <LineChart data={workoutProgress}>
                  <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" />
                  <XAxis dataKey="day" stroke="#64748b" />
                  <YAxis yAxisId="left" stroke="#3b82f6" />
                  <YAxis yAxisId="right" orientation="right" stroke="#f59e0b" />
                  <Tooltip 
                    contentStyle={{ 
                      backgroundColor: 'white', 
                      border: 'none', 
                      borderRadius: '8px', 
                      boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' 
                    }} 
                  />
                  <Line 
                    yAxisId="left" 
                    type="monotone" 
                    dataKey="volume" 
                    stroke="#3b82f6" 
                    strokeWidth={3} 
                    dot={{ fill: '#3b82f6', r: 4 }} 
                    name="Volume (kg)" 
                  />
                  <Line 
                    yAxisId="right" 
                    type="monotone" 
                    dataKey="calories" 
                    stroke="#f59e0b" 
                    strokeWidth={3} 
                    dot={{ fill: '#f59e0b', r: 4 }} 
                    name="Calories" 
                  />
                </LineChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>

          <Card className="border-0 shadow-lg">
            <CardHeader>
              <CardTitle>Training Split</CardTitle>
              <CardDescription>Workout distribution this month</CardDescription>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={300}>
                <PieChart>
                  <Pie
                    data={trainingSplitData}
                    cx="50%"
                    cy="50%"
                    innerRadius={60}
                    outerRadius={100}
                    paddingAngle={5}
                    dataKey="value"
                  >
                    {trainingSplitData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={entry.color} />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>
              <div className="grid grid-cols-2 gap-3 mt-4">
                {trainingSplitData.map((item, idx) => (
                  <div key={idx} className="flex items-center gap-2">
                    <div className="w-3 h-3 rounded-full" style={{ backgroundColor: item.color }} />
                    <span className="text-sm ">{item.name}</span>
                    <span className="text-sm font-semibold ml-auto">{item.value}%</span>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card className="border-0 shadow-lg">
            <CardHeader>
              <CardTitle>Today&apos;s Nutrition</CardTitle>
              <CardDescription>Macronutrient intake vs daily targets</CardDescription>
            </CardHeader>
            <CardContent>
              <ResponsiveContainer width="100%" height={250}>
                <BarChart data={nutritionData} layout="vertical">
                  <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" />
                  <XAxis type="number" stroke="#64748b" />
                  <YAxis dataKey="name" type="category" stroke="#64748b" width={60} />
                  <Tooltip 
                    contentStyle={{ 
                      backgroundColor: 'white', 
                      border: 'none', 
                      borderRadius: '8px', 
                      boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' 
                    }} 
                  />
                  <Bar dataKey="target" fill="#e2e8f0" radius={[0, 4, 4, 0]} name="Target" />
                  <Bar dataKey="value" fill="#8b5cf6" radius={[0, 4, 4, 0]} name="Current" />
                </BarChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>

          
          <Card className="border-0 shadow-lg">
            <CardHeader>
              <CardTitle>Recent Workouts</CardTitle>
              <CardDescription>Your latest training sessions</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {recentWorkouts.map((workout) => (
                  <div 
                    key={workout.id} 
                    className="flex items-center justify-between p-4  rounded-lg  transition-colors cursor-pointer"
                  >
                    <div className="flex items-center gap-4">
                      <div className="w-12 h-12 rounded-lg flex items-center justify-center">
                        <Dumbbell className="w-6 h-6" />
                      </div>
                      <div>
                        <h4 className="font-semibold ">{workout.name}</h4>
                        <p className="text-sm ">
                          {workout.exercises} exercises • {workout.duration}
                        </p>
                      </div>
                    </div>
                    <span className="text-sm font-medium">{workout.date}</span>
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