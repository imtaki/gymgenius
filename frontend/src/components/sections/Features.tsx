import { Dumbbell, Target, BarChart3, Apple, Settings } from "lucide-react";

const FEATURES = [
  {
    icon: Dumbbell,
    title: "Smart Training Splits",
    description:
      "AI-powered workout plans with Push/Pull/Legs, Upper/Lower splits, and custom routines tailored to your goals.",
    accent: "text-lime-400",
    glow: "group-hover:shadow-lime-400/10",
    bg: "group-hover:bg-lime-400/5",
  },
  {
    icon: Target,
    title: "Progress Tracking",
    description:
      "Track your lifts, monitor personal records, and visualize your fitness journey with detailed analytics.",
    accent: "text-orange-400",
    glow: "group-hover:shadow-orange-400/10",
    bg: "group-hover:bg-orange-400/5",
  },
  {
    icon: Apple,
    title: "Nutrition Logging",
    description:
      "Log meals, track macros, and hit your daily calorie goals with our comprehensive nutrition system.",
    accent: "text-sky-400",
    glow: "group-hover:shadow-sky-400/10",
    bg: "group-hover:bg-sky-400/5",
  },
  {
    icon: BarChart3,
    title: "Advanced Analytics",
    description:
      "Detailed insights into your performance with weight tracking graphs and workout calendar views.",
    accent: "text-fuchsia-400",
    glow: "group-hover:shadow-fuchsia-400/10",
    bg: "group-hover:bg-fuchsia-400/5",
  },
];

export default function Features() {
  return (
    <section
      id="features"
      className="py-24 bg-zinc-950"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">

        
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 mb-4 px-4 py-2 rounded-full bg-zinc-900 border border-zinc-800 text-xs font-semibold text-zinc-400 uppercase tracking-widest">
            <Settings className="h-3.5 w-3.5" />
            Core Features
          </div>
          <h2
            className="text-4xl md:text-5xl font-black text-zinc-100 mb-4 tracking-tight"
            style={{ fontFamily: "'DM Serif Display', 'Playfair Display', Georgia, serif" }}
          >
            Everything You Need<br />
            <span className="text-zinc-500">to Succeed.</span>
          </h2>
          <p className="text-sm text-zinc-500 max-w-xl mx-auto leading-relaxed">
            Comprehensive tools designed to help you achieve your fitness goals faster and more efficiently.
          </p>
        </div>

        
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
          {FEATURES.map((feature, i) => (
            <div
              key={i}
              className={`group relative overflow-hidden bg-zinc-900 border border-zinc-800 rounded-2xl p-6 hover:border-zinc-700 transition-all duration-300 shadow-lg hover:shadow-xl ${feature.glow} ${feature.bg} cursor-default`}
            >
              
              <div className={`absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-current to-transparent opacity-0 group-hover:opacity-40 transition-opacity duration-500 ${feature.accent}`} />

              <div className={`inline-flex items-center justify-center w-12 h-12 rounded-xl bg-zinc-800 mb-5 ${feature.accent} group-hover:scale-110 transition-transform duration-300`}>
                <feature.icon className="w-5 h-5" />
              </div>

              <h3 className="text-sm font-bold text-zinc-100 uppercase tracking-widest mb-3">
                {feature.title}
              </h3>
              <p className="text-xs text-zinc-500 leading-relaxed">
                {feature.description}
              </p>

              
              <span className="absolute bottom-4 right-5 text-5xl font-black text-zinc-800 select-none leading-none">
                {String(i + 1).padStart(2, "0")}
              </span>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
}