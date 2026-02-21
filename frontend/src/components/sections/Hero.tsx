"use client";

import { useEffect, useRef, useState } from "react";
import { ChevronRight, Star, Zap, Dumbbell, Flame, TrendingUp, BarChart3, Apple, Activity } from "lucide-react";
import Link from "next/link";


function useScrollTilt(maxTilt = 18, maxLift = 60) {
  const [style, setStyle] = useState({
    rotateX: maxTilt,
    rotateY: -6,
    translateY: maxLift,
    opacity: 0.6,
    scale: 0.92,
  });

  useEffect(() => {
    const onScroll = () => {
      const scrollY  = window.scrollY;
      const winH     = window.innerHeight;
      
      const progress = Math.min(scrollY / (winH * 0.7), 1);

      setStyle({
        rotateX:    maxTilt  * (1 - progress),
        rotateY:    -6       * (1 - progress),
        translateY: maxLift  * (1 - progress),
        opacity:    0.6 + 0.4 * progress,
        scale:      0.92 + 0.08 * progress,
      });
    };

    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll(); // init
    return () => window.removeEventListener("scroll", onScroll);
  }, [maxTilt, maxLift]);

  return style;
}



function AppMockup() {
  return (
    <div className="w-full rounded-2xl overflow-hidden bg-zinc-900 border border-zinc-700/80 shadow-2xl shadow-black/60">

      
      <div className="flex items-center gap-2 px-4 py-3 bg-zinc-800 border-b border-zinc-700/60">
        <div className="flex gap-1.5">
          <div className="w-3 h-3 rounded-full bg-red-500/80" />
          <div className="w-3 h-3 rounded-full bg-amber-500/80" />
          <div className="w-3 h-3 rounded-full bg-emerald-500/80" />
        </div>
        <div className="flex-1 flex justify-center">
          <div className="flex items-center gap-2 px-3 py-1 rounded-lg bg-zinc-700/60 border border-zinc-600/40">
            <div className="w-3 h-3 rounded bg-lime-400/80 flex items-center justify-center">
              <Dumbbell className="w-2 h-2 text-zinc-900" />
            </div>
            <span className="text-[10px] text-zinc-400 font-mono">GymGenius — Dashboard</span>
          </div>
        </div>
      </div>

      
      <div className="flex" style={{ fontFamily: "'DM Mono', monospace" }}>

        
        <div className="w-14 bg-zinc-950 border-r border-zinc-800 flex flex-col items-center py-4 gap-4 shrink-0">
          <div className="w-8 h-8 rounded-lg bg-lime-400 flex items-center justify-center">
            <Dumbbell className="w-4 h-4 text-zinc-900" />
          </div>
          <div className="flex flex-col gap-3 mt-2">
            {[BarChart3, Activity, Apple, Flame].map((Icon, i) => (
              <div key={i} className={`w-8 h-8 rounded-lg flex items-center justify-center ${i === 0 ? "bg-lime-400/10 text-lime-400" : "text-zinc-600 hover:text-zinc-400"}`}>
                <Icon className="w-4 h-4" />
              </div>
            ))}
          </div>
        </div>

        
        <div className="flex-1 p-5 space-y-4 bg-zinc-950">

          
          <div className="flex items-center justify-between">
            <div>
              <p className="text-[11px] text-zinc-500 uppercase tracking-widest">Dashboard</p>
              <p className="text-xs text-zinc-400 mt-0.5">Saturday, Feb 21</p>
            </div>
            <div className="flex items-center gap-1 bg-zinc-900 border border-zinc-800 rounded-lg p-0.5">
              {["Week", "Month", "Year"].map((p, i) => (
                <span key={p} className={`px-2 py-1 rounded text-[9px] font-semibold uppercase tracking-widest ${i === 0 ? "bg-lime-400 text-zinc-900" : "text-zinc-600"}`}>{p}</span>
              ))}
            </div>
          </div>

          
          <div className="grid grid-cols-4 gap-2">
            {[
              { label: "Volume",  val: "18.9k", icon: BarChart3, color: "text-lime-400",   bg: "bg-lime-400/10"   },
              { label: "Calories",val: "3,090",  icon: Flame,     color: "text-orange-400", bg: "bg-orange-400/10" },
              { label: "Streak",  val: "12d",    icon: Zap,       color: "text-fuchsia-400",bg: "bg-fuchsia-400/10"},
              { label: "Avg",     val: "65m",    icon: Activity,  color: "text-sky-400",    bg: "bg-sky-400/10"    },
            ].map((s) => (
              <div key={s.label} className="bg-zinc-900 border border-zinc-800 rounded-xl p-3">
                <div className={`w-6 h-6 rounded-lg ${s.bg} ${s.color} flex items-center justify-center mb-2`}>
                  <s.icon className="w-3 h-3" />
                </div>
                <p className={`text-sm font-black font-mono ${s.color}`}>{s.val}</p>
                <p className="text-[9px] text-zinc-600 uppercase tracking-widest">{s.label}</p>
              </div>
            ))}
          </div>

          
          <div className="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
            <p className="text-[10px] text-zinc-500 uppercase tracking-widest mb-3">Workout Progress</p>
            <div className="flex items-end gap-1.5 h-16">
              {[55, 72, 48, 80, 65, 88, 0].map((h, i) => (
                <div key={i} className="flex-1 flex flex-col items-center gap-1">
                  <div
                    className={`w-full rounded-sm transition-all ${i === 5 ? "bg-gradient-to-t from-lime-600 to-lime-400" : h === 0 ? "" : "bg-zinc-700"}`}
                    style={{ height: h > 0 ? `${h}%` : "2px", marginTop: h > 0 ? "auto" : undefined, alignSelf: "flex-end" }}
                  />
                </div>
              ))}
            </div>
            <div className="flex justify-between mt-2">
              {["M", "T", "W", "T", "F", "S", "S"].map((d, i) => (
                <span key={i} className={`flex-1 text-center text-[8px] font-mono ${i === 5 ? "text-lime-400" : "text-zinc-700"}`}>{d}</span>
              ))}
            </div>
          </div>

          
          <div className="space-y-1.5">
            <p className="text-[10px] text-zinc-500 uppercase tracking-widest">Recent</p>
            {["Push Day — 65 min", "Pull Day — 58 min", "Leg Day — 72 min"].map((w, i) => (
              <div key={i} className={`flex items-center gap-2.5 px-3 py-2 rounded-lg border ${i === 0 ? "bg-lime-400/5 border-lime-400/20" : "border-zinc-800/50"}`}>
                <div className={`w-5 h-5 rounded-md flex items-center justify-center ${i === 0 ? "bg-lime-400/20" : "bg-zinc-800"}`}>
                  <Dumbbell className={`w-2.5 h-2.5 ${i === 0 ? "text-lime-400" : "text-zinc-600"}`} />
                </div>
                <span className={`text-[10px] font-mono ${i === 0 ? "text-zinc-200" : "text-zinc-500"}`}>{w}</span>
                {i === 0 && <span className="ml-auto text-[9px] text-lime-400 font-semibold uppercase tracking-widest">Today</span>}
              </div>
            ))}
          </div>

        </div>
      </div>
    </div>
  );
}



export default function Hero() {
  const tilt = useScrollTilt(16, 50);

  const mockupStyle: React.CSSProperties = {
    transform: `
      perspective(1200px)
      rotateX(${tilt.rotateX}deg)
      rotateY(${tilt.rotateY}deg)
      translateY(${tilt.translateY}px)
      scale(${tilt.scale})
    `,
    opacity: tilt.opacity,
    transition: "transform 0.08s ease-out, opacity 0.08s ease-out",
    willChange: "transform, opacity",
  };

  return (
    <section
      className="relative bg-zinc-950 overflow-hidden"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      
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

            
            <h1
              className="text-5xl md:text-6xl xl:text-7xl font-black leading-[0.92] tracking-tighter text-zinc-100 mb-6"
              style={{ fontFamily: "'DM Serif Display', 'Playfair Display', Georgia, serif" }}
            >
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
              <Link
                href="/signup"
                className="group inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-lime-400 hover:bg-lime-300 text-zinc-900 rounded-xl font-bold text-xs uppercase tracking-widest transition-all duration-200 shadow-xl shadow-lime-400/20 hover:shadow-lime-400/30 hover:scale-[1.02]"
              >
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
              <span className="text-xs text-zinc-600 uppercase tracking-widest">
                4.9 / 5 from 10,000+ users
              </span>
            </div>

          </div>

         
          <div className="relative flex items-start justify-center lg:justify-end pt-8 lg:pt-0">

            
            <div className="absolute inset-0 bg-lime-400/[0.05] rounded-3xl blur-3xl scale-90 translate-y-8" />

            
            <div
              className="relative w-full max-w-lg lg:max-w-none"
              style={mockupStyle}
            >
              
              <div className="absolute -inset-px rounded-2xl bg-gradient-to-br from-white/10 via-transparent to-transparent pointer-events-none z-10" />

              
              <div className="absolute inset-0 rounded-2xl shadow-[0_32px_80px_-12px_rgba(0,0,0,0.8)] pointer-events-none" />

              <AppMockup />
            </div>

            
            <div
              className="absolute -left-4 top-1/4 bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 shadow-xl"
              style={{
                transform: `perspective(1200px) rotateX(${tilt.rotateX * 0.5}deg) rotateY(${tilt.rotateY * 0.5}deg) translateY(${tilt.translateY * 0.6}px)`,
                opacity: tilt.opacity,
                transition: "transform 0.08s ease-out, opacity 0.08s ease-out",
              }}
            >
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

            <div
              className="absolute -right-2 bottom-1/3 bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 shadow-xl"
              style={{
                transform: `perspective(1200px) rotateX(${tilt.rotateX * 0.4}deg) rotateY(${tilt.rotateY * 0.4}deg) translateY(${tilt.translateY * 0.5}px)`,
                opacity: tilt.opacity,
                transition: "transform 0.08s ease-out, opacity 0.08s ease-out",
              }}
            >
              <div className="flex items-center gap-2">
                <div className="w-6 h-6 rounded-lg bg-orange-400/10 flex items-center justify-center">
                  <Flame className="w-3 h-3 text-orange-400" />
                </div>
                <div>
                  <p className="text-xs font-black text-orange-400 font-mono">3,090</p>
                  <p className="text-[9px] text-zinc-600 uppercase tracking-widest">kcal/wk</p>
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