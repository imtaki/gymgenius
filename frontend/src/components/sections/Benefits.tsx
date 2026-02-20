import { TrendingUp, Zap, Target, BarChart3, Calendar, Apple } from "lucide-react";

const BENEFITS = [
  {
    icon: Zap,
    text: "AI-powered workout recommendations",
    description: "Smart algorithms create personalized routines",
    accent: "text-lime-400",
    bg: "bg-lime-400/10",
  },
  {
    icon: BarChart3,
    text: "Comprehensive progress tracking",
    description: "Monitor every aspect of your fitness journey",
    accent: "text-orange-400",
    bg: "bg-orange-400/10",
  },
  {
    icon: Target,
    text: "Customizable training splits",
    description: "Flexible programs that adapt to your schedule",
    accent: "text-sky-400",
    bg: "bg-sky-400/10",
  },
  {
    icon: Apple,
    text: "Nutrition and macro tracking",
    description: "Complete dietary monitoring and guidance",
    accent: "text-fuchsia-400",
    bg: "bg-fuchsia-400/10",
  },
  {
    icon: TrendingUp,
    text: "Personal record monitoring",
    description: "Celebrate achievements and break barriers",
    accent: "text-lime-400",
    bg: "bg-lime-400/10",
  },
  {
    icon: Calendar,
    text: "Calendar workout views",
    description: "Visual planning for consistent training",
    accent: "text-orange-400",
    bg: "bg-orange-400/10",
  },
];


function StatBubble({
  value, label, accent, className = "",
}: {
  value: string; label: string; accent: string; className?: string;
}) {
  return (
    <div className={`absolute bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-3 shadow-xl ${className}`}>
      <p className={`text-2xl font-black font-mono ${accent}`}>{value}</p>
      <p className="text-xs text-zinc-500 uppercase tracking-widest mt-0.5">{label}</p>
    </div>
  );
}

export default function Benefits() {
  return (
    <section
      id="benefits"
      className="py-24 bg-zinc-950"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid lg:grid-cols-2 gap-16 items-center">

          
          <div>
            <div className="inline-flex items-center gap-2 mb-5 px-4 py-2 rounded-full bg-zinc-900 border border-zinc-800 text-xs font-semibold text-zinc-400 uppercase tracking-widest">
              Why Choose GymGenius
            </div>

            <h2
              className="text-4xl md:text-5xl font-black text-zinc-100 mb-5 tracking-tight leading-tight"
              style={{ fontFamily: "'DM Serif Display', 'Playfair Display', Georgia, serif" }}
            >
              Achieve Your Goals{" "}
              <span className="text-lime-400">10×</span>{" "}
              <span className="text-zinc-500">Faster.</span>
            </h2>

            <p className="text-sm text-zinc-400 mb-10 leading-relaxed max-w-lg">
              Our AI-powered platform adapts to your unique fitness profile, providing
              personalized recommendations that evolve with your progress.
            </p>

            <div className="space-y-4">
              {BENEFITS.map((b, i) => (
                <div key={i} className="group flex items-start gap-4">
                  <div className={`shrink-0 w-9 h-9 rounded-xl ${b.bg} flex items-center justify-center ${b.accent} group-hover:scale-110 transition-transform duration-200`}>
                    <b.icon className="w-4 h-4" />
                  </div>
                  <div>
                    <p className="text-sm font-semibold text-zinc-200">{b.text}</p>
                    <p className="text-xs text-zinc-600 mt-0.5">{b.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

         
          <div className="relative flex items-center justify-center">

           
            <div className="absolute inset-0 bg-lime-400/[0.03] rounded-3xl blur-3xl" />

           
            <div className="relative w-full aspect-square max-w-md rounded-3xl bg-zinc-900 border border-zinc-800 flex items-center justify-center overflow-hidden">

              
              <svg className="absolute inset-0 w-full h-full opacity-[0.03]" xmlns="http://www.w3.org/2000/svg">
                <defs>
                  <pattern id="benefits-grid" width="32" height="32" patternUnits="userSpaceOnUse">
                    <path d="M 32 0 L 0 0 0 32" fill="none" stroke="#a3e635" strokeWidth="0.5" />
                  </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#benefits-grid)" />
              </svg>

              
              <div className="relative text-center px-10">
                <div className="relative mb-6 inline-block">
                  <div className="absolute inset-0 bg-lime-400/20 rounded-full blur-2xl scale-150" />
                  <TrendingUp className="h-20 w-20 text-lime-400 relative z-10" />
                </div>
                <h3
                  className="text-2xl font-black text-zinc-100 mb-3 tracking-tight"
                  style={{ fontFamily: "'DM Serif Display', Georgia, serif" }}
                >
                  Track Everything
                </h3>
                <p className="text-xs text-zinc-500 leading-relaxed">
                  Monitor your progress with detailed analytics and insights
                  that help you stay motivated and on track.
                </p>
              </div>
            </div>

            
            <StatBubble
              value="+47%"
              label="Strength gain"
              accent="text-lime-400"
              className="-top-4 -right-4 rotate-2"
            />
            <StatBubble
              value="12d"
              label="Streak"
              accent="text-orange-400"
              className="-bottom-4 -left-4 -rotate-1"
            />
            <StatBubble
              value="10k+"
              label="Users"
              accent="text-sky-400"
              className="top-1/2 -right-8 -translate-y-1/2 rotate-1"
            />

          </div>

        </div>
      </div>
    </section>
  );
}