import { Loader2, Save } from "lucide-react";

interface FitnessFormProps {
    profileData: Record<string, any>;
    setProfileData: (data: any) => void;
    handleSaveProfile: () => void;
    saving: boolean;
}

const FIELDS = [
    { key: "current_weight", label: "Current Weight (kg)", placeholder: "75.0",  type: "number", step: "0.1", parse: parseFloat },
    { key: "height",         label: "Height (cm)",         placeholder: "175.0", type: "number", step: "0.1", parse: parseFloat },
    { key: "age",            label: "Age",                 placeholder: "25",    type: "number",              parse: parseInt   },
    { key: "target_weight",  label: "Goal Weight (kg)",    placeholder: "70.0",  type: "number", step: "0.1", parse: parseFloat },
] as const;

const GOAL_OPTIONS = ["cutting", "bulking", "maintaining"];

const inputCls = "w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-zinc-600 transition-colors";

function Field({ label, hint, children }: { label: string; hint?: string; children: React.ReactNode }) {
    return (
        <div>
            <label className="block text-sm font-medium text-gray-400 mb-2">{label}</label>
            {children}
            {hint && <p className="mt-2 text-xs text-gray-500">{hint}</p>}
        </div>
    );
}

export function FitnessProfileForm({ profileData, setProfileData, handleSaveProfile, saving }: FitnessFormProps) {
    const update = (key: string, value: any) => setProfileData({ ...profileData, [key]: value });

    return (
        <div className="bg-zinc-800/50 border border-zinc-800/50 rounded-xl p-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {FIELDS.map(({ key, label, placeholder, type, step, parse }) => (
                    <Field key={key} label={label}>
                        <input
                            type={type}
                            step={step}
                            value={profileData[key]}
                            onChange={(e) => update(key, parse(e.target.value) || 0)}
                            placeholder={placeholder}
                            className={inputCls}
                        />
                    </Field>
                ))}

                <Field label="Current Goal">
                    <select
                        value={profileData.goal_type}
                        onChange={(e) => update("goal_type", e.target.value)}
                        className={`${inputCls} appearance-none cursor-pointer`}
                    >
                        {GOAL_OPTIONS.map((o) => (
                            <option key={o} value={o}>
                                {o.charAt(0).toUpperCase() + o.slice(1)}
                            </option>
                        ))}
                    </select>
                </Field>
            </div>

            <Field
                label="Daily Caloric Goal (kcal)"
                hint="Set your daily caloric intake target based on your fitness goal"
            >
                <input
                    type="number"
                    value={profileData.caloric_goal}
                    onChange={(e) => update("caloric_goal", parseInt(e.target.value) || 0)}
                    placeholder="2000"
                    className={inputCls}
                />
            </Field>

            <button
                onClick={handleSaveProfile}
                disabled={saving}
                className="mt-6 w-full bg-white text-black px-4 py-2.5 rounded-lg font-medium hover:bg-gray-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
                {saving ? (
                    <>
                        <Loader2 className="w-4 h-4 animate-spin" />
                        Saving...
                    </>
                ) : (
                    <>
                        <Save className="w-4 h-4" />
                        Save Changes
                    </>
                )}
            </button>
        </div>
    );
}