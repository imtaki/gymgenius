
export default function Field({ label, hint, children }: { label: string; hint?: string; children: React.ReactNode }) {
    return (
        <div>
            <label className="block text-sm font-medium text-gray-400 mb-2">{label}</label>
            {children}
            {hint && <p className="mt-2 text-xs text-gray-500">{hint}</p>}
        </div>
    );
}

// Taildwind classes for consistent input styling across the form

export const inputCls = "w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-zinc-600 transition-colors";