'use client';

import {
  LineChart, Line, BarChart, Bar,
  XAxis, YAxis, CartesianGrid, Tooltip,
  ResponsiveContainer, PieChart, Pie, Cell,
} from 'recharts';
import { Target } from 'lucide-react';

interface DashboardChartsProps {
  workoutProgress: Array<{ day: string; volume: number; calories: number }>;
  trainingSplit:   Array<{ name: string; value: number; color: string }>;
  nutrition:       Array<{ name: string; value: number; target: number }>;
}

const tooltipStyle = {
  contentStyle: {
    backgroundColor: '#18181b',
    border: '1px solid rgba(255,255,255,0.08)',
    borderRadius: '10px',
    boxShadow: '0 8px 32px rgba(0,0,0,0.4)',
    color: '#e4e4e7',
    fontSize: '12px',
    fontFamily: "'DM Mono', monospace",
  },
  labelStyle: { color: '#a1a1aa', marginBottom: 4 },
  cursor: { stroke: 'rgba(163,230,53,0.15)', strokeWidth: 1 },
};

function ChartCard({ children, className = '' }: { children: React.ReactNode; className?: string }) {
  return (
    <div className={`bg-zinc-900 border border-zinc-800 rounded-2xl p-5 ${className}`}>
      {children}
    </div>
  );
}

function ChartLabel({ title, subtitle }: { title: React.ReactNode; subtitle: string }) {
  return (
    <div className="mb-5">
      <h3 className="text-sm font-semibold text-zinc-100 uppercase tracking-widest flex items-center gap-2">{title}</h3>
      <p className="text-xs text-zinc-500 mt-1">{subtitle}</p>
    </div>
  );
}

function WorkoutProgressChart({ data }: { data: DashboardChartsProps['workoutProgress'] }) {
  return (
    <ChartCard className="lg:col-span-2">
      <ChartLabel
        title={<><Target className="w-4 h-4 text-lime-400" /> Workout Progress</>}
        subtitle="Volume lifted and calories burned this week"
      />
      <ResponsiveContainer width="100%" height={280}>
        <LineChart data={data}>
          <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.04)" vertical={false} />
          <XAxis
            dataKey="day"
            stroke="#52525b"
            tick={{ fill: '#71717a', fontSize: 11, fontFamily: 'DM Mono, monospace' }}
            axisLine={false}
            tickLine={false}
          />
          <YAxis
            yAxisId="left"
            stroke="#52525b"
            tick={{ fill: '#71717a', fontSize: 11, fontFamily: 'DM Mono, monospace' }}
            axisLine={false}
            tickLine={false}
            width={40}
          />
          <YAxis
            yAxisId="right"
            orientation="right"
            stroke="#52525b"
            tick={{ fill: '#71717a', fontSize: 11, fontFamily: 'DM Mono, monospace' }}
            axisLine={false}
            tickLine={false}
            width={40}
          />
          <Tooltip {...tooltipStyle} />
          <Line
            yAxisId="left"
            type="monotone"
            dataKey="volume"
            stroke="#a3e635"
            strokeWidth={2.5}
            dot={{ fill: '#a3e635', r: 3, strokeWidth: 0 }}
            activeDot={{ r: 5, fill: '#a3e635', strokeWidth: 0 }}
            name="Volume (kg)"
          />
          <Line
            yAxisId="right"
            type="monotone"
            dataKey="calories"
            stroke="#f97316"
            strokeWidth={2.5}
            dot={{ fill: '#f97316', r: 3, strokeWidth: 0 }}
            activeDot={{ r: 5, fill: '#f97316', strokeWidth: 0 }}
            name="Calories"
            strokeDasharray="5 3"
          />
        </LineChart>
      </ResponsiveContainer>
      {/* Legend */}
      <div className="flex items-center gap-5 mt-3 pl-1">
        <div className="flex items-center gap-2">
          <div className="w-4 h-0.5 bg-lime-400 rounded" />
          <span className="text-xs text-zinc-500 font-mono">Volume</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-4 h-px border-t-2 border-dashed border-orange-500" />
          <span className="text-xs text-zinc-500 font-mono">Calories</span>
        </div>
      </div>
    </ChartCard>
  );
}

function TrainingSplitChart({ data }: { data: DashboardChartsProps['trainingSplit'] }) {
  return (
    <ChartCard>
      <ChartLabel title="Training Split" subtitle="Workout distribution this month" />
      <ResponsiveContainer width="100%" height={200}>
        <PieChart>
          <Pie
            data={data}
            cx="50%"
            cy="50%"
            innerRadius={55}
            outerRadius={85}
            paddingAngle={4}
            dataKey="value"
          >
            {data.map((entry, i) => (
              <Cell key={i} fill={entry.color} stroke="transparent" />
            ))}
          </Pie>
          <Tooltip
            contentStyle={tooltipStyle.contentStyle}
            cursor={false}
            formatter={(value: number) => [`${value}%`, '']}
          />
        </PieChart>
      </ResponsiveContainer>
      <div className="grid grid-cols-2 gap-2 mt-2">
        {data.map((item, i) => (
          <div key={i} className="flex items-center gap-2 py-1">
            <div className="w-2 h-2 rounded-full shrink-0" style={{ backgroundColor: item.color }} />
            <span className="text-xs text-zinc-400">{item.name}</span>
            <span className="text-xs font-mono font-semibold text-zinc-200 ml-auto">{item.value}%</span>
          </div>
        ))}
      </div>
    </ChartCard>
  );
}

function NutritionChart({ data }: { data: DashboardChartsProps['nutrition'] }) {
  return (
    <ChartCard>
      <ChartLabel title="Today's Nutrition" subtitle="Macronutrient intake vs daily targets" />
      <ResponsiveContainer width="100%" height={200}>
        <BarChart data={data} layout="vertical" barGap={4}>
          <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.04)" horizontal={false} />
          <XAxis
            type="number"
            stroke="#52525b"
            tick={{ fill: '#71717a', fontSize: 11, fontFamily: 'DM Mono, monospace' }}
            axisLine={false}
            tickLine={false}
          />
          <YAxis
            dataKey="name"
            type="category"
            stroke="#52525b"
            tick={{ fill: '#a1a1aa', fontSize: 11, fontFamily: 'DM Mono, monospace' }}
            axisLine={false}
            tickLine={false}
            width={52}
          />
          <Tooltip {...tooltipStyle} />
          <Bar dataKey="target" fill="rgba(255,255,255,0.06)" radius={[0, 4, 4, 0]} name="Target" />
          <Bar dataKey="value"  fill="#a3e635"               radius={[0, 4, 4, 0]} name="Current" />
        </BarChart>
      </ResponsiveContainer>
    </ChartCard>
  );
}

export default function DashboardCharts({ workoutProgress, trainingSplit, nutrition }: DashboardChartsProps) {
  return (
    <div className="space-y-4">
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <WorkoutProgressChart data={workoutProgress} />
        <TrainingSplitChart   data={trainingSplit}   />
      </div>
      <NutritionChart data={nutrition} />
    </div>
  );
}