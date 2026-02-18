import { SectionHeader } from "./ui-elements";
import { AVATAR_COLORS } from "../../../lib/admin/adminDataConstants";;
import { SUBSCRIPTIONS, RECENT_TRANSACTIONS } from "../../../lib/admin/adminMockData";;

function getInitials(name) {
  return name.split(" ").map((n) => n[0]).join("");
}

export function SubscriptionsSection() {
  return (
    <div className="rounded-2xl bg-[#111318] border border-white/[0.06] p-5 space-y-6">
      <SectionHeader title="Subscriptions" />
      <PlanCards />
      <RecentTransactions />
    </div>
  );
}

function PlanCards() {
  return (
    <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
      {SUBSCRIPTIONS.map((sub) => (
        <div
          key={sub.plan}
          className="p-4 rounded-xl bg-[#0d0f13] border border-white/[0.05] hover:border-white/[0.1] transition-all"
        >
          <div className="flex items-center justify-between mb-3">
            <div className="flex items-center gap-2">
              <div className={`w-2.5 h-2.5 rounded-full ${sub.color}`} />
              <span className="font-semibold text-white">{sub.plan}</span>
            </div>
            <span className="text-sm font-mono text-slate-400">{sub.price}</span>
          </div>

          <p className="text-2xl font-bold text-white font-mono">{sub.users.toLocaleString()}</p>
          <p className="text-xs text-slate-500 mb-3">users · {sub.pct}% of total</p>

          <div className="h-1.5 bg-white/[0.04] rounded-full overflow-hidden">
            <div
              className={`h-full rounded-full ${sub.color} transition-all duration-700`}
              style={{ width: `${sub.pct}%` }}
            />
          </div>
        </div>
      ))}
    </div>
  );
}

function RecentTransactions() {
  return (
    <div>
      <h3 className="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-3">
        Recent Transactions
      </h3>
      <div className="space-y-1">
        {RECENT_TRANSACTIONS.map((tx, i) => (
          <div
            key={i}
            className="flex items-center justify-between py-2.5 px-3.5 rounded-xl hover:bg-white/[0.02] transition-colors"
          >
            <div className="flex items-center gap-3">
              <div
                className={`w-7 h-7 rounded-full ${AVATAR_COLORS[i % AVATAR_COLORS.length]} flex items-center justify-center text-xs font-bold text-white`}
              >
                {getInitials(tx.user)}
              </div>
              <div>
                <p className="text-sm font-medium text-white">{tx.user}</p>
                <p className="text-xs text-slate-500">{tx.plan}</p>
              </div>
            </div>
            <div className="text-right">
              <p className={`font-mono font-bold text-sm ${tx.positive ? "text-emerald-400" : "text-red-400"}`}>
                {tx.amount}
              </p>
              <p className="text-xs text-slate-600">{tx.date}</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}