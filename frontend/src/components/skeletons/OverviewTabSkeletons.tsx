export function RecentUserSkeleton() {
  return (
    <div className="flex items-center justify-between py-1.5 animate-pulse">
      <div className="flex items-center gap-3">
        <div className="w-8 h-8 rounded-full bg-white/5" />
        <div className="space-y-1.5">
          <div className="h-3 w-24 bg-white/10 rounded" />
          <div className="h-2 w-16 bg-white/5 rounded" />
        </div>
      </div>
      <div className="h-5 w-12 bg-white/10 rounded-full" />
    </div>
  );
}