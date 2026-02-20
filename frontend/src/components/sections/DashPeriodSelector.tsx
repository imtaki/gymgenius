'use client';

import { useState } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';

const PERIODS = ['week', 'month', 'year'] as const;

export default function DashPeriodSelector() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const [selected, setSelected] = useState(searchParams.get('period') || 'week');

  const handleChange = (period: string) => {
    setSelected(period);
    router.push(`/dashboard?period=${period}`);
  };

  return (
    <div className="flex items-center gap-1 bg-zinc-900 border border-zinc-800 rounded-xl p-1">
      {PERIODS.map((period) => (
        <button
          key={period}
          onClick={() => handleChange(period)}
          className={`relative px-4 py-1.5 rounded-lg text-xs font-semibold tracking-widest uppercase transition-all duration-200 ${
            selected === period
              ? 'bg-lime-400 text-zinc-900 shadow-lg shadow-lime-400/20'
              : 'text-zinc-500 hover:text-zinc-300'
          }`}
        >
          {period}
        </button>
      ))}
    </div>
  );
}