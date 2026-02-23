"use client";

import { useEffect, useState } from "react";
import { ChevronRight, Star, Zap, Dumbbell, Flame, TrendingUp, BarChart3, Apple, Activity, UtensilsCrossed, Calendar } from "lucide-react";
import Link from "next/link";



function useScrollTilt(maxTilt = 16, maxLift = 50) {
  const [style, setStyle] = useState({
    rotateX: maxTilt,
    rotateY: -6,
    translateY: maxLift,
    opacity: 0.6,
    scale: 0.92,
  });

  useEffect(() => {
    const onScroll = () => {
      const progress = Math.min(window.scrollY / (window.innerHeight * 0.7), 1);
      setStyle({
        rotateX:    maxTilt * (1 - progress),
        rotateY:    -6      * (1 - progress),
        translateY: maxLift * (1 - progress),
        opacity:    0.6 + 0.4 * progress,
        scale:      0.92 + 0.08 * progress,
      });
    };
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
    return () => window.removeEventListener("scroll", onScroll);
  }, [maxTilt, maxLift]);

  return style;
}



const NAV_ITEMS = [
  { id: "dashboard", icon: BarChart3,      label: "Dashboard" },
  { id: "workouts",  icon: Dumbbell,       label: "Workouts"  },
  { id: "nutrition", icon: UtensilsCrossed,label: "Nutrition" },
  { id: "calendar",  icon: Calendar,       label: "Schedule"  },
];



const CHART_BARS = [
  { day: "M", h: 55, active: false },
  { day: "T", h: 72, active: false },
  { day: "W", h: 48, active: false },
  { day: "T", h: 80, active: false },
  { day: "F", h: 65, active: false },
  { day: "S", h: 88, active: true  },
  { day: "S", h: 0,  active: false },
];



function DashboardContent() {
  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <p className="text-[11px] text-zinc-500 uppercase tracking-widest">Dashboard</p>
          <p className="text-[11px] text-zinc-400 mt-0.5">Saturday, Feb 21</p>
        </div>
        <div className="flex items-center gap-0.5 bg-zinc-900 border border-zinc-800 rounded-lg p-0.5">
          {["Week", "Month", "Year"].map((p, i) => (
            <span key={p} className={`px-2 py-1 rounded text-[9px] font-bold uppercase tracking-widest ${i === 0 ? "bg-lime-400 text-zinc-900" : "text-zinc-600"}`}>{p}</span>
          ))}
        </div>
      </div>

      <div className="grid grid-cols-4 gap-1.5">
        {[
          { label: "Volume",   val: "18.9k", icon: BarChart3, color: "text-lime-400",    bg: "bg-lime-400/10"    },
          { label: "Calories", val: "3,090",  icon: Flame,     color: "text-orange-400",  bg: "bg-orange-400/10"  },
          { label: "Streak",   val: "12d",    icon: Zap,       color: "text-fuchsia-400", bg: "bg-fuchsia-400/10" },
          { label: "Avg",      val: "65m",    icon: Activity,  color: "text-sky-400",     bg: "bg-sky-400/10"     },
        ].map((s) => (
          <div key={s.label} className="bg-zinc-900 border border-zinc-800 rounded-xl p-2.5">
            <div className={`w-5 h-5 rounded-md ${s.bg} ${s.color} flex items-center justify-center mb-1.5`}>
              <s.icon className="w-2.5 h-2.5" />
            </div>
            <p className={`text-xs font-black font-mono leading-none ${s.color}`}>{s.val}</p>
            <p className="text-[8px] text-zinc-600 uppercase tracking-widest mt-0.5">{s.label}</p>
          </div>
        ))}
      </div>

      
      <div className="bg-zinc-900 border border-zinc-800 rounded-xl p-3">
        <p className="text-[10px] text-zinc-500 uppercase tracking-widest mb-2">Workout Progress</p>
        <div className="flex items-end gap-1" style={{ height: 52 }}>
          {CHART_BARS.map((bar, i) => (
            <div key={i} className="flex-1 flex items-end h-full">
              {bar.h > 0 ? (
                <div
                  className={`w-full rounded-sm ${bar.active ? "bg-gradient-to-t from-lime-600 to-lime-400" : "bg-zinc-700"}`}
                  style={{ height: `${bar.h}%` }}
                />
              ) : (
                <div className="w-full h-px bg-zinc-800" />
              )}
            </div>
          ))}
        </div>
        <div className="flex gap-1 mt-1.5">
          {CHART_BARS.map((bar, i) => (
            <span key={i} className={`flex-1 text-center text-[8px] font-mono ${bar.active ? "text-lime-400" : "text-zinc-700"}`}>
              {bar.day}
            </span>
          ))}
        </div>
      </div>

      <div className="space-y-1">
        <p className="text-[9px] text-zinc-600 uppercase tracking-widest">Recent</p>
        {[
          { name: "Push Day", dur: "65 min", today: true  },
          { name: "Pull Day", dur: "58 min", today: false },
          { name: "Leg Day",  dur: "72 min", today: false },
        ].map((w, i) => (
          <div key={i} className={`flex items-center gap-2 px-2.5 py-1.5 rounded-lg border ${w.today ? "bg-lime-400/5 border-lime-400/20" : "border-zinc-800/50"}`}>
            <div className={`w-4 h-4 rounded flex items-center justify-center shrink-0 ${w.today ? "bg-lime-400/20" : "bg-zinc-800"}`}>
              <Dumbbell className={`w-2 h-2 ${w.today ? "text-lime-400" : "text-zinc-600"}`} />
            </div>
            <span className={`text-[10px] font-mono flex-1 ${w.today ? "text-zinc-200" : "text-zinc-500"}`}>{w.name} — {w.dur}</span>
            {w.today && <span className="text-[8px] text-lime-400 font-bold uppercase tracking-widest">Today</span>}
          </div>
        ))}
      </div>
    </div>
  );
}

function WorkoutsContent() {
  return (
    <div className="space-y-3">
      <p className="text-[11px] text-zinc-500 uppercase tracking-widest">Workout Library</p>
      {[
        { name: "Push Day",       cat: "PPL",         vol: "8.4k",  accent: "text-lime-400",    bg: "bg-lime-400/10"    },
        { name: "Pull Day",       cat: "PPL",         vol: "7.6k",  accent: "text-sky-400",     bg: "bg-sky-400/10"     },
        { name: "Leg Day",        cat: "PPL",         vol: "11.2k", accent: "text-fuchsia-400", bg: "bg-fuchsia-400/10" },
        { name: "Upper Body",     cat: "Upper/Lower", vol: "6.8k",  accent: "text-orange-400",  bg: "bg-orange-400/10"  },
        { name: "Morning Cardio", cat: "Cardio",      vol: "—",     accent: "text-red-400",     bg: "bg-red-400/10"     },
      ].map((w, i) => (
        <div key={i} className="flex items-center justify-between px-3 py-2.5 bg-zinc-900 border border-zinc-800 rounded-xl hover:border-zinc-700 transition-colors cursor-default">
          <div className="flex items-center gap-2.5">
            <div className={`w-7 h-7 rounded-lg ${w.bg} ${w.accent} flex items-center justify-center`}>
              <Dumbbell className="w-3 h-3" />
            </div>
            <div>
              <p className="text-[11px] font-bold text-zinc-200">{w.name}</p>
              <p className="text-[9px] text-zinc-600 font-mono">{w.cat}</p>
            </div>
          </div>
          <div className="text-right">
            <p className={`text-xs font-black font-mono ${w.accent}`}>{w.vol}</p>
            <p className="text-[9px] text-zinc-600">kg vol</p>
          </div>
        </div>
      ))}
    </div>
  );
}

function NutritionContent() {
  const macros = [
    { label: "Protein", val: 180, goal: 200, color: "bg-sky-400",   text: "text-sky-400"   },
    { label: "Carbs",   val: 220, goal: 250, color: "bg-amber-400", text: "text-amber-400" },
    { label: "Fats",    val: 65,  goal: 70,  color: "bg-rose-400",  text: "text-rose-400"  },
  ];
  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between">
        <p className="text-[11px] text-zinc-500 uppercase tracking-widest">Nutrition</p>
        <span className="text-[9px] text-zinc-600 font-mono">Feb 21</span>
      </div>
      <div className="bg-zinc-900 border border-zinc-800 rounded-xl p-4 flex items-center gap-4">
        <div className="relative w-16 h-16 shrink-0">
          <svg viewBox="0 0 64 64" className="w-full h-full -rotate-90">
            <circle cx="32" cy="32" r="26" fill="none" stroke="#27272a" strokeWidth="6" />
            <circle cx="32" cy="32" r="26" fill="none" stroke="#a3e635" strokeWidth="6"
              strokeDasharray={`${2 * Math.PI * 26 * 0.78} ${2 * Math.PI * 26}`}
              strokeLinecap="round" />
          </svg>
          <div className="absolute inset-0 flex flex-col items-center justify-center">
            <span className="text-[10px] font-black text-lime-400 font-mono leading-none">1,560</span>
            <span className="text-[7px] text-zinc-600">kcal</span>
          </div>
        </div>
        <div className="flex-1 space-y-2">
          {macros.map((m) => (
            <div key={m.label} className="space-y-0.5">
              <div className="flex justify-between">
                <span className={`text-[9px] font-mono ${m.text}`}>{m.label}</span>
                <span className="text-[9px] text-zinc-600 font-mono">{m.val}g / {m.goal}g</span>
              </div>
              <div className="h-1 bg-zinc-800 rounded-full overflow-hidden">
                <div className={`h-full rounded-full ${m.color}`} style={{ width: `${(m.val / m.goal) * 100}%` }} />
              </div>
            </div>
          ))}
        </div>
      </div>
      {[
        { meal: "Breakfast", foods: "Oats, Banana",   kcal: 420, icon: "☀️" },
        { meal: "Lunch",     foods: "Chicken, Rice",  kcal: 680, icon: "🍽️" },
        { meal: "Snack",     foods: "Greek Yogurt",   kcal: 180, icon: "🍎" },
        { meal: "Dinner",    foods: "Salmon, Quinoa", kcal: 590, icon: "🌙" },
      ].map((m, i) => (
        <div key={i} className="flex items-center gap-2.5 px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-xl">
          <span className="text-sm">{m.icon}</span>
          <div className="flex-1 min-w-0">
            <p className="text-[10px] font-bold text-zinc-300">{m.meal}</p>
            <p className="text-[9px] text-zinc-600 font-mono truncate">{m.foods}</p>
          </div>
          <span className="text-[10px] text-lime-400 font-black font-mono">{m.kcal}</span>
        </div>
      ))}
    </div>
  );
}

function CalendarContent() {
  const days = ["M","T","W","T","F","S","S"];
  const completed = [true, true, false, true, true, true, false];
  return (
    <div className="space-y-3">
      <p className="text-[11px] text-zinc-500 uppercase tracking-widest">Schedule — Feb 2025</p>
      <div className="grid grid-cols-7 gap-1">
        {days.map((d, i) => (
          <div key={i} className="flex flex-col items-center gap-1">
            <span className="text-[8px] text-zinc-600 font-mono">{d}</span>
            <div className={`w-7 h-7 rounded-lg flex items-center justify-center text-[9px] font-bold font-mono border ${
              i === 5 ? "bg-lime-400 text-zinc-900 border-lime-400" :
              completed[i] ? "bg-zinc-800 border-zinc-700 text-zinc-300" :
              "border-zinc-800 text-zinc-700"
            }`}>{i + 17}</div>
            {completed[i] && i !== 6 && <div className={`w-1 h-1 rounded-full ${i === 5 ? "bg-lime-400" : "bg-zinc-600"}`} />}
          </div>
        ))}
      </div>
      <div className="space-y-1.5">
        <p className="text-[9px] text-zinc-600 uppercase tracking-widest">This Week</p>
        {[
          { day: "Mon", name: "Push Day", dur: "65m", color: "border-lime-400/40 bg-lime-400/5"  },
          { day: "Tue", name: "Pull Day", dur: "58m", color: "border-zinc-700 bg-zinc-900"       },
          { day: "Thu", name: "Leg Day",  dur: "72m", color: "border-zinc-700 bg-zinc-900"       },
          { day: "Fri", name: "Upper",    dur: "55m", color: "border-zinc-700 bg-zinc-900"       },
          { day: "Sat", name: "Push Day", dur: "65m", color: "border-sky-400/40 bg-sky-400/5"    },
        ].map((w, i) => (
          <div key={i} className={`flex items-center gap-2.5 px-3 py-2 rounded-xl border ${w.color}`}>
            <div className="flex-1">
              <p className="text-[10px] font-bold text-zinc-200">{w.name}</p>
              <p className="text-[9px] text-zinc-600 font-mono">{w.day} · {w.dur}</p>
            </div>
            <Dumbbell className="w-3 h-3 text-zinc-700" />
          </div>
        ))}
      </div>
    </div>
  );
}



function AppMockup() {
  const [activeTab, setActiveTab] = useState("dashboard");

  const contentMap: Record<string, React.ReactNode> = {
    dashboard: <DashboardContent />,
    workouts:  <WorkoutsContent />,
    nutrition: <NutritionContent />,
    calendar:  <CalendarContent />,
  };

  return (
    <div className="w-full rounded-2xl overflow-hidden bg-zinc-900 border border-zinc-700/80 shadow-2xl shadow-black/60" style={{ fontFamily: "'DM Mono', monospace" }}>

      {/* Titlebar */}
      <div className="flex items-center gap-2 px-4 py-3 bg-zinc-800/80 border-b border-zinc-700/60">
        <div className="flex gap-1.5">
          <div className="w-2.5 h-2.5 rounded-full bg-red-500/80" />
          <div className="w-2.5 h-2.5 rounded-full bg-amber-500/80" />
          <div className="w-2.5 h-2.5 rounded-full bg-emerald-500/80" />
        </div>
        <div className="flex-1 flex justify-center">
          <div className="flex items-center gap-2 px-3 py-1 rounded-lg bg-zinc-700/50 border border-zinc-600/30">
            <div className="w-3 h-3 rounded bg-lime-400 flex items-center justify-center">
              <Dumbbell className="w-1.5 h-1.5 text-zinc-900" />
            </div>
            <span className="text-[10px] text-zinc-400">
              GymGenius — {NAV_ITEMS.find(n => n.id === activeTab)?.label}
            </span>
          </div>
        </div>
      </div>

      
      <div className="flex">

        
        <div className="w-14 bg-zinc-950 border-r border-zinc-800 flex flex-col items-center py-4 gap-1 shrink-0">
          <div className="w-8 h-8 rounded-lg bg-lime-400 flex items-center justify-center mb-3">
            <Dumbbell className="w-4 h-4 text-zinc-900" />
          </div>
          {NAV_ITEMS.map((item) => {
            const isActive = activeTab === item.id;
            return (
              <button
                key={item.id}
                onClick={() => setActiveTab(item.id)}
                title={item.label}
                className={`group relative w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-150 cursor-pointer ${
                  isActive ? "bg-lime-400/15 text-lime-400" : "text-zinc-600 hover:text-zinc-300 hover:bg-zinc-800"
                }`}
              >
                {isActive && (
                  <span className="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 w-0.5 h-4 rounded-full bg-lime-400" />
                )}
                <item.icon className="w-3.5 h-3.5" />
                <span className="absolute left-full ml-2 px-2 py-1 rounded-md bg-zinc-800 border border-zinc-700 text-[9px] text-zinc-300 uppercase tracking-widest whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                  {item.label}
                </span>
              </button>
            );
          })}
        </div>

        
        <div className="flex-1 p-4 bg-zinc-950 overflow-hidden min-h-[340px]">
          {contentMap[activeTab]}
        </div>

      </div>
    </div>
  );
}



export default function Hero() {
  const tilt = useScrollTilt(16, 50);

  const mockupStyle: React.CSSProperties = {
    transform: `perspective(1200px) rotateX(${tilt.rotateX}deg) rotateY(${tilt.rotateY}deg) translateY(${tilt.translateY}px) scale(${tilt.scale})`,
    opacity: tilt.opacity,
    transition: "transform 0.08s ease-out, opacity 0.08s ease-out",
    willChange: "transform, opacity",
  };

  const floatStyle = (xF: number, yF: number): React.CSSProperties => ({
    transform: `perspective(1200px) rotateX(${tilt.rotateX * xF}deg) rotateY(${tilt.rotateY * xF}deg) translateY(${tilt.translateY * yF}px)`,
    opacity: tilt.opacity,
    transition: "transform 0.08s ease-out, opacity 0.08s ease-out",
  });

  return (
    <section className="relative bg-zinc-950 overflow-hidden" style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}>

      
      <div className="absolute inset-0 pointer-events-none">
        <div className="absolute top-0 left-1/4 w-[700px] h-[500px] bg-lime-400/[0.04] rounded-full blur-3xl" />
        <div className="absolute top-1/2 right-0 w-[400px] h-[600px] bg-lime-400/[0.02] rounded-full blur-3xl" />
        <svg className="absolute inset-0 w-full h-full opacity-[0.022]" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="hero-grid" width="48" height="48" patternUnits="userSpaceOnUse">
              <path d="M 48 0 L 0 0 0 48" fill="none" stroke="#a3e635" strokeWidth="0.5" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#hero-grid)" />
        </svg>
        <div className="absolute bottom-0 inset-x-0 h-48 bg-gradient-to-t from-zinc-950 to-transparent" />
      </div>

      <div className="container mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div className="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center min-h-[90vh] py-24">

          
          <div className="flex flex-col justify-center">
            <div className="inline-flex items-center gap-2 mb-8 px-4 py-2 rounded-full bg-zinc-900 border border-zinc-800 text-xs font-semibold text-lime-400 uppercase tracking-widest w-fit">
              <Zap className="h-3.5 w-3.5" />
              AI-Powered Fitness Revolution
            </div>
            <h1 className="text-5xl md:text-6xl xl:text-7xl font-black leading-[0.92] tracking-tighter text-zinc-100 mb-6" style={{ fontFamily: "'DM Serif Display', 'Playfair Display', Georgia, serif" }}>
              Transform<br />
              Your{" "}
              <span className="relative inline-block">
                <span className="relative z-10 text-lime-400">Fitness</span>
                <span className="absolute -bottom-1 left-0 right-0 h-1 bg-lime-400/25 rounded blur-sm" />
              </span>
              <br />
              <span className="text-zinc-500">Journey.</span>
            </h1>
            <p className="text-base text-zinc-400 mb-10 max-w-md leading-relaxed">
              The ultimate AI health and fitness tracker — smart training splits,
              nutrition logging, and advanced progress tracking in one platform.
            </p>
            <div className="flex flex-col sm:flex-row gap-3">
              <Link href="/signup" className="group inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-lime-400 hover:bg-lime-300 text-zinc-900 rounded-xl font-bold text-xs uppercase tracking-widest transition-all duration-200 shadow-xl shadow-lime-400/20 hover:scale-[1.02]">
                Start Your Journey
                <ChevronRight className="h-3.5 w-3.5 group-hover:translate-x-0.5 transition-transform" />
              </Link>
              <button className="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-transparent hover:bg-zinc-800 text-zinc-400 hover:text-zinc-200 rounded-xl font-bold text-xs uppercase tracking-widest border border-zinc-800 hover:border-zinc-700 transition-all duration-200">
                Watch Demo
              </button>
            </div>
            <div className="flex items-center gap-3 mt-8">
              <div className="flex items-center gap-0.5">
                {Array.from({ length: 5 }).map((_, i) => (
                  <Star key={i} className="h-3.5 w-3.5 fill-lime-400 text-lime-400" />
                ))}
              </div>
              <span className="text-xs text-zinc-600 uppercase tracking-widest">4.9 / 5 from 10,000+ users</span>
            </div>
          </div>

         
          <div className="relative flex items-start justify-center lg:justify-end pt-8 lg:pt-0">
            <div className="absolute inset-0 bg-lime-400/[0.05] rounded-3xl blur-3xl scale-90 translate-y-8" />

            <div className="relative w-full max-w-lg lg:max-w-none" style={mockupStyle}>
              <div className="absolute -inset-px rounded-2xl bg-gradient-to-br from-white/10 via-transparent to-transparent pointer-events-none z-10" />
              <div className="absolute inset-0 rounded-2xl shadow-[0_32px_80px_-12px_rgba(0,0,0,0.8)] pointer-events-none" />
              <AppMockup />
            </div>

            <div className="absolute -left-4 top-1/4 bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 shadow-xl pointer-events-none" style={floatStyle(0.5, 0.6)}>
              <div className="flex items-center gap-2">
                <div className="w-6 h-6 rounded-lg bg-lime-400/10 flex items-center justify-center">
                  <TrendingUp className="w-3 h-3 text-lime-400" />
                </div>
                <div>
                  <p className="text-xs font-black text-lime-400 font-mono">+47%</p>
                  <p className="text-[9px] text-zinc-600 uppercase tracking-widest">Strength</p>
                </div>
              </div>
            </div>

            <div className="absolute -right-2 bottom-1/3 bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 shadow-xl pointer-events-none" style={floatStyle(0.4, 0.5)}>
              <div className="flex items-center gap-2">
                <div className="w-6 h-6 rounded-lg bg-orange-400/10 flex items-center justify-center">
                  <Flame className="w-3 h-3 text-orange-400" />
                </div>
                <div>
                  <p className="text-xs font-black text-orange-400 font-mono">3,090</p>
                  <p className="text-[9px] text-zinc-600 uppercase tracking-widest">kcal / wk</p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <div className="h-24" />
    </section>
  );
}