export default function SkeletonLoader() {
  return (
    <div className="space-y-6 animate-pulse">
      <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 h-40" />
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {Array.from({ length: 4 }).map((_, i) => (
          <div key={i} className="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 h-48" />
        ))}
      </div>
    </div>
  );
}