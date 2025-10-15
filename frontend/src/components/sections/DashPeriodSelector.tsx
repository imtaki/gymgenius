'use client';

import { useState } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';

export default function DashPeriodSelector() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const [selectedPeriod, setSelectedPeriod] = useState(searchParams.get('period') || 'week');
  


  const handlePeriodChange = (period: string) => {
    setSelectedPeriod(period);
    router.push(`/dashboard?period=${period}`);
  };

  return (
    <div className="flex gap-2">
      {['week', 'month', 'year'].map((period) => (
        <button
          key={period}
          onClick={() => handlePeriodChange(period)}
          className={`px-4 py-2 rounded-lg font-medium transition-all ${
            selectedPeriod === period
              ? 'bg-blue-600 text-white shadow-lg shadow-blue-200'
              : 'bg-white text-slate-900 hover:bg-slate-50 border border-slate-200'
          }`}
        >
          {period.charAt(0).toUpperCase() + period.slice(1)}
        </button>
      ))}
    </div>
  );
}