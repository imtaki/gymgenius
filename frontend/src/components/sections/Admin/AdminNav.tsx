
import { TABS } from "../../../lib/admin/adminDataConstants";;

export function AdminNav({ activeTab, onTabChange }) {
  return (
    <div className="sticky top-0 z-20 bg-[#0a0c10]/80 backdrop-blur-xl px-6 py-3">
      <div className="flex items-center justify-center">
        <div className="flex items-center gap-1 bg-[#111318] border border-white/[0.06] rounded-xl p-1">
          {TABS.map((t) => (
            <button
              key={t.id}
              onClick={() => onTabChange(t.id)}
              className={`flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 ${
                activeTab === t.id
                  ? "bg-violet-600 text-white shadow-lg shadow-violet-900/50"
                  : "text-slate-500 hover:text-slate-300 hover:bg-white/[0.04]"
              }`}
            >
              <t.icon size={13} />
              {t.label}
            </button>
          ))}
        </div>

      </div>
    </div>
  );
}