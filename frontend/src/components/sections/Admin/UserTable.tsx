import { CheckCircle, XCircle, Clock, Eye, Edit2, Trash2 } from "lucide-react";
import { SectionHeader, SearchBar, UserAvatar } from "./ui-elements";
import { PLAN_COLOR, STATUS_COLOR } from "../../../lib/admin/adminDataConstants";;
import { USERS } from "../../../lib/admin/adminMockData";;

const STATUS_ICON = {
  active:    <CheckCircle size={13} className="text-emerald-400" />,
  inactive:  <Clock       size={13} className="text-slate-400"   />,
  suspended: <XCircle     size={13} className="text-red-400"     />,
};

const TABLE_HEADERS = ["User", "Plan", "Status", "Workouts", "Joined", "Actions"];

export function UsersTable() {
  return (
    <div className="rounded-2xl bg-[#111318] border border-white/[0.06] p-5">
      <SectionHeader title="Registered Users" action="Add User" />
      <SearchBar placeholder="Search users by name or email…" />

      <div className="overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-white/[0.05]">
              {TABLE_HEADERS.map((h) => (
                <th key={h} className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider pb-3 pr-4">
                  {h}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-white/[0.03]">
            {USERS.map((user, i) => (
              <UserRow key={user.id} user={user} index={i} />
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function UserRow({ user, index }) {
  return (
    <tr className="group hover:bg-white/[0.02] transition-colors">

      {/* Name + email */}
      <td className="py-3.5 pr-4">
        <div className="flex items-center gap-3">
          <UserAvatar initials={user.avatar} index={index} />
          <div>
            <p className="font-medium text-white text-sm">{user.name}</p>
            <p className="text-xs text-slate-500">{user.email}</p>
          </div>
        </div>
      </td>

      {/* Plan */}
      <td className="py-3.5 pr-4">
        <span className={`text-xs font-semibold px-2.5 py-1 rounded-full border ${PLAN_COLOR[user.plan]}`}>
          {user.plan}
        </span>
      </td>

      {/* Status */}
      <td className="py-3.5 pr-4">
        <span className={`flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full w-fit ${STATUS_COLOR[user.status]}`}>
          {STATUS_ICON[user.status]}
          {user.status}
        </span>
      </td>

      {/* Workouts */}
      <td className="py-3.5 pr-4">
        <span className="text-white font-mono font-medium">{user.workouts}</span>
      </td>

      {/* Joined */}
      <td className="py-3.5 pr-4">
        <span className="text-slate-400 text-xs">{user.joined}</span>
      </td>

      {/* Actions */}
      <td className="py-3.5">
        <div className="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
          <ActionBtn icon={Eye}    hoverColor="hover:text-sky-400    hover:bg-sky-400/10"    />
          <ActionBtn icon={Edit2}  hoverColor="hover:text-violet-400 hover:bg-violet-400/10" />
          <ActionBtn icon={Trash2} hoverColor="hover:text-red-400    hover:bg-red-400/10"    />
        </div>
      </td>

    </tr>
  );
}

function ActionBtn({ icon: Icon, hoverColor }) {
  return (
    <button className={`p-1.5 text-slate-500 ${hoverColor} rounded-lg transition-all`}>
      <Icon size={14} />
    </button>
  );
}