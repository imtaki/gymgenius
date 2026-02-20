import { Badge } from "../ui/badge";
import { Button } from "../ui/button";
import { ChevronRight, Star, Zap } from "lucide-react";

export default function Hero() {
  return (
    <section className="relative py-24 lg:py-36 overflow-hidden bg-zinc-950">

      
      <div className="absolute inset-0 pointer-events-none">
        
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-lime-400/[0.04] rounded-full blur-3xl" />
        
        <svg className="absolute inset-0 w-full h-full opacity-[0.025]" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="grid" width="48" height="48" patternUnits="userSpaceOnUse">
              <path d="M 48 0 L 0 0 0 48" fill="none" stroke="#a3e635" strokeWidth="0.5" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
       
        <div className="absolute bottom-0 inset-x-0 h-32 bg-gradient-to-t from-zinc-950 to-transparent" />
      </div>

      <div className="container mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div className="text-center max-w-4xl mx-auto">

          <div className="inline-flex items-center gap-2 mb-8 px-4 py-2 rounded-full bg-zinc-900 border border-zinc-800 text-xs font-semibold text-lime-400 uppercase tracking-widest">
            <Zap className="h-3.5 w-3.5" />
            AI-Powered Fitness Revolution
          </div>

          
          <h1
            className="text-5xl md:text-7xl lg:text-8xl font-black mb-6 leading-[0.9] tracking-tighter text-zinc-100"
            style={{ fontFamily: "'DM Serif Display', 'Playfair Display', Georgia, serif" }}
          >
            Transform Your{" "}
            <span className="relative inline-block">
              <span className="relative z-10 text-lime-400">Fitness</span>
              
              <span className="absolute -bottom-1 left-0 right-0 h-1 bg-lime-400/30 rounded blur-sm" />
            </span>
            <br />
            <span className="text-zinc-400">Journey.</span>
          </h1>

          
          <p
            className="text-lg md:text-xl text-zinc-400 mb-10 max-w-2xl mx-auto leading-relaxed"
            style={{ fontFamily: "'DM Mono', monospace" }}
          >
            The ultimate AI health and fitness tracker — smart training splits,
            nutrition logging, and advanced progress tracking in one platform.
          </p>

          
          <div className="flex flex-col sm:flex-row gap-3 justify-center items-center">
            <button className="group inline-flex items-center gap-2 px-8 py-4 bg-lime-400 hover:bg-lime-300 text-zinc-900 rounded-xl font-bold text-sm uppercase tracking-widest transition-all duration-200 shadow-xl shadow-lime-400/20 hover:shadow-lime-400/30 hover:scale-[1.02]">
              Start Your Journey
              <ChevronRight className="h-4 w-4 group-hover:translate-x-0.5 transition-transform" />
            </button>
            <button className="inline-flex items-center gap-2 px-8 py-4 bg-transparent hover:bg-zinc-800 text-zinc-300 rounded-xl font-bold text-sm uppercase tracking-widest border border-zinc-700 hover:border-zinc-600 transition-all duration-200">
              Watch Demo
            </button>
          </div>

          
          <div className="flex items-center justify-center gap-3 mt-10">
            <div className="flex items-center gap-0.5">
              {Array.from({ length: 5 }).map((_, i) => (
                <Star key={i} className="h-3.5 w-3.5 fill-lime-400 text-lime-400" />
              ))}
            </div>
            <span
              className="text-xs text-zinc-500 uppercase tracking-widest"
              style={{ fontFamily: "'DM Mono', monospace" }}
            >
              4.9 / 5 from 10,000+ users
            </span>
          </div>

        </div>
      </div>
    </section>
  );
}