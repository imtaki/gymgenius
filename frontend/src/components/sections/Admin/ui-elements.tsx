import { TrendingUp, TrendingDown, Search, Filter, Plus } from "lucide-react";
import { AVATAR_COLORS } from "../../../lib/admin/adminDataConstants";;

// ── StatCard ──────────────────────────────────────────────────────────────────

export function StatCard({ stat }) {
  return (
    <div className="relative overflow-hidden rounded-2xl bg-[#111318] border border-white/[0.06] p-5 group hover:border-white/[0.12] transition-all duration-300">
      <div className={`absolute inset-0 opacity-0 group-hover:opacity-5 bg-gradient-to-br ${stat.color} transition-opacity duration-500`} />
      <div className="flex items-start justify-between mb-4">
        <div className={`p-2.5 rounded-xl bg-gradient-to-br ${stat.color} shadow-lg`}>
          <stat.icon size={18} className="text-white" />
        </div>
        <span className={`flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full ${
          stat.up ? "text-emerald-400 bg-emerald-400/10" : "text-red-400 bg-red-400/10"
        }`}>
          {stat.up ? <TrendingUp size={11} /> : <TrendingDown size={11} />}
          {stat.change}
        </span>
      </div>
      <p className="text-2xl font-bold text-white tracking-tight font-mono">{stat.value}</p>
      <p className="text-xs text-slate-500 mt-1 font-medium">{stat.label}</p>
    </div>
  );
}

// ── SectionHeader ─────────────────────────────────────────────────────────────

export function SectionHeader({ title, action }) {
  return (
    <div className="flex items-center justify-between mb-4">
      <h2 className="text-sm font-semibold text-white uppercase tracking-widest">{title}</h2>
      {action && (
        <button className="flex items-center gap-1.5 text-xs font-medium text-violet-400 hover:text-violet-300 transition-colors bg-violet-400/10 hover:bg-violet-400/20 px-3 py-1.5 rounded-lg border border-violet-400/20">
          <Plus size={12} />
          {action}
        </button>
      )}
    </div>
  );
}

// ── SearchBar ─────────────────────────────────────────────────────────────────

export function SearchBar({ placeholder }) {
  return (
    <div className="relative mb-4">
      <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" />
      <input
        type="text"
        placeholder={placeholder}
        className="w-full bg-[#0d0f13] border border-white/[0.06] rounded-xl pl-9 pr-10 py-2.5 text-sm text-slate-300 placeholder:text-slate-600 focus:outline-none focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all"
      />
      <button className="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-slate-500 hover:text-slate-300 transition-colors">
        <Filter size={14} />
      </button>
    </div>
  );
}

// ── UserAvatar ────────────────────────────────────────────────────────────────

export function UserAvatar({ initials, index, size = "md" }) {
  const bg = AVATAR_COLORS[index % AVATAR_COLORS.length];
  const sz = size === "sm" ? "w-7 h-7 text-xs" : "w-8 h-8 text-xs";
  return (
    <div className={`${sz} ${bg} rounded-full flex items-center justify-center font-bold text-white shrink-0`}>
      {initials}
    </div>
  );
}