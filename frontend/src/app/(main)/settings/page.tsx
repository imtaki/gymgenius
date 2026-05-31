"use client";
import Link from "next/link";
import BackButton from "../../../components/ui/backbutton";
import { CreditCard, Loader2, Lock } from "lucide-react";
import { FitnessProfileForm } from "@/components/features/settings/FitnessProfileForm";
import { useProfile } from "@/hooks/useSettingsProfile";


export default function ProfilePage() {
    const { user,
        loading,
        profileData,
        setProfileData,
        handleSaveProfile,
        saving,
        passwordData,
        setPasswordData,
        handlePasswordChange,
        errorMessage,
        successMessage } = useProfile();

    if (loading) {
        return (
            <div className="min-h-screen bg-zinc-950 flex items-center justify-center">
                <Loader2 className="w-8 h-8 text-gray-400 animate-spin" />
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-zinc-950 text-white">
            <div className="max-w-4xl mx-auto px-6 py-12">
                <BackButton />
                
                <div className="mb-12 mt-6">
                    <h1 className="text-4xl font-bold mb-3">Settings</h1>
                    <p className="text-gray-500 text-base">Manage your account and fitness preferences</p>
                </div>

                {successMessage && (
                    <div className="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg px-4 py-3 text-green-400 text-sm">
                        {successMessage}
                    </div>
                )}

                {errorMessage && (
                    <div className="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg px-4 py-3 text-red-400 text-sm">
                        {errorMessage}
                    </div>
                )}

                {/* Account Section */}
                <div className="mb-8">
                    <h2 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Account</h2>

                    <div className="bg-zinc-800/50 border border-zinc-800/50 rounded-xl overflow-hidden">
                        <div className="divide-y divide-zinc-800/50">
                            <div className="px-6 py-4 flex items-center justify-between hover:bg-zinc-800/30 transition-colors">
                                <div>
                                    <label className="block text-sm font-medium text-gray-400 mb-1">
                                        Name
                                    </label>
                                    <div className="text-white">{user?.username}</div>
                                </div>
                            </div>

                            <div className="px-6 py-4 flex items-center justify-between hover:bg-zinc-800/30 transition-colors">
                                <div>
                                    <label className="block text-sm font-medium text-gray-400 mb-1">
                                        Email
                                    </label>
                                    <div className="text-white">{user?.email}</div>
                                </div>
                            </div>

                            <div className="px-6 py-4 flex items-center justify-between hover:bg-zinc-800/30 transition-colors">
                                <div>
                                    <label className="block text-sm font-medium text-gray-400 mb-1">
                                        Role
                                    </label>
                                    <div className="text-white capitalize">{user?.role}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                 <div className="mb-8">
                    <h2 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Fitness Profile</h2>
                        <FitnessProfileForm profileData={profileData} setProfileData={setProfileData} handleSaveProfile={handleSaveProfile} saving={saving} />
                    </div>
                </div>

                <div className="mb-8">
                    <h2 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Security</h2>

                    <div className="bg-zinc-800/50 border border-zinc-800/50 rounded-xl p-6">
                        <div className="space-y-5">
                            <div>
                                <label className="block text-sm font-medium text-gray-400 mb-2">
                                    Current Password
                                </label>
                                <input
                                    type="password"
                                    value={passwordData.currentPassword}
                                    onChange={(e) => setPasswordData({...passwordData, currentPassword: e.target.value})}
                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-zinc-600 transition-colors"
                                    placeholder="Enter current password"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-400 mb-2">
                                    New Password
                                </label>
                                <input
                                    type="password"
                                    value={passwordData.newPassword}
                                    onChange={(e) => setPasswordData({...passwordData, newPassword: e.target.value})}
                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-zinc-600 transition-colors"
                                    placeholder="Enter new password"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-400 mb-2">
                                    Confirm New Password
                                </label>
                                <input
                                    type="password"
                                    value={passwordData.confirmPassword}
                                    onChange={(e) => setPasswordData({...passwordData, confirmPassword: e.target.value})}
                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-zinc-600 transition-colors"
                                    placeholder="Confirm new password"
                                />
                            </div>

                            <button
                                onClick={handlePasswordChange}
                                disabled={saving || !passwordData.currentPassword || !passwordData.newPassword || !passwordData.confirmPassword}
                                className="w-full bg-zinc-800 text-white px-4 py-2.5 rounded-lg font-medium hover:bg-zinc-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                {saving ? (
                                    <>
                                        <Loader2 className="w-4 h-4 animate-spin" />
                                        Updating...
                                    </>
                                ) : (
                                    <>
                                        <Lock className="w-4 h-4" />
                                        Update Password
                                    </>
                                )}
                            </button>
                        </div>
                    </div>
                </div>

                <div className="mb-8">
                    <h2 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Subscription</h2>

                    <div className="bg-zinc-800/50 border border-zinc-800/50 rounded-xl overflow-hidden">
                        <Link href="/settings/subscription" className="block">
                            <div className="px-6 py-4 flex items-center justify-between hover:bg-zinc-800/30 transition-colors cursor-pointer">
                                <div className="flex items-center gap-3">
                                    <CreditCard className="w-5 h-5 text-gray-400" />
                                    <div>
                                        <label className="block text-sm font-medium text-gray-400 mb-1">
                                            Manage Subscription
                                        </label>
                                        <p className="text-xs text-gray-600">View and upgrade your subscription plan</p>
                                    </div>
                                </div>
                                <span className="text-gray-600">→</span>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
    );
}