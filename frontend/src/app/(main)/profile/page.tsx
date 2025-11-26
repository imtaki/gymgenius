"use client";
import { useEffect, useState } from "react";
import BackButton from "../../../components/ui/backbutton";
import { jwtDecode } from "jwt-decode";
import { User, Target, Lock, Save, Loader2 } from 'lucide-react';
import { getUser } from "../../api/authService";
import { getUserSettingsById } from "../../api/userSettingsService";
import { DecodedToken } from "../../../types/types";


export default function ProfilePage() {
     const [user, setUser] = useState<DecodedToken | null>(null);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [profileData, setProfileData] = useState({
        weight: '',
        height: '',
        caloricGoal: '',
        goalWeight: '',
        currentGoal: 'maintaining'
    });
    const [passwordData, setPasswordData] = useState({
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
    });
    const [successMessage, setSuccessMessage] = useState('');
    const [errorMessage, setErrorMessage] = useState('');

    useEffect(() => {
        const user = getUser();
        const token = user?.token;
        if (token) {
            try {
                const decoded = jwtDecode<DecodedToken>(token);
                setUser(decoded);
            } catch (error) {
                console.error("Error decoding token:", error);
            }
        }
        setLoading(false);
    }, []);

    useEffect(() => {
        const user = getUser();
        const id = user?.id;
        async function fetchUserSettings() {
            if(!id) {
                setLoading(false);
                return;
            }

            try {
                setLoading(true);
                const settings = await getUserSettingsById(id);
                console.log(settings);
                setProfileData({
                    weight: settings.current_weight,
                    height: settings.height,
                    caloricGoal: settings.caloric_goal,
                    goalWeight: settings.target_weight,
                    currentGoal: settings.current_goal,
                });
            } catch (error) {
                console.error("Failed to fetch user settings:", error);
            } finally {
                setLoading(false);
            }

        }
                           
        fetchUserSettings();
    }, [user]);

    const handleSaveCaloricGoal = async () => {
    };

    const handlePasswordChange = async () => {
    };

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

                {/* Fitness Profile Section */}
                <div className="mb-8">
                    <h2 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Fitness Profile</h2>

                    <div className="bg-zinc-800/50 border border-zinc-800/50 rounded-xl p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label className="block text-sm font-medium text-gray-400 mb-2">
                                    Current Weight (kg)
                                </label>
                                <input
                                    type="number"
                                    step="0.1"
                                    value={profileData.weight}
                                    onChange={(e) => setProfileData({...profileData, weight: e.target.value})}
                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-zinc-600 transition-colors"
                                    placeholder="75.0"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-400 mb-2">
                                    Height (cm)
                                </label>
                                <input
                                    type="number"
                                    step="0.1"
                                    value={profileData.height}
                                    onChange={(e) => setProfileData({...profileData, height: e.target.value})}
                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-zinc-600 transition-colors"
                                    placeholder="175.0"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-400 mb-2">
                                    Goal Weight (kg)
                                </label>
                                <input
                                    type="number"
                                    step="0.1"
                                    value={profileData.goalWeight}
                                    onChange={(e) => setProfileData({...profileData, goalWeight: e.target.value})}
                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-zinc-600 transition-colors"
                                    placeholder="70.0"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-400 mb-2">
                                    Current Goal
                                </label>
                                <select
                                    value={profileData.currentGoal}
                                    onChange={(e) => setProfileData({...profileData, currentGoal: e.target.value})}
                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-zinc-600 transition-colors appearance-none cursor-pointer"
                                >
                                    <option value="cutting">Cutting</option>
                                    <option value="bulking">Bulking</option>
                                    <option value="maintaining">Maintaining</option>
                                </select>
                            </div>
                        </div>

                        <div className="mb-6">
                            <label className="block text-sm font-medium text-gray-400 mb-2">
                                Daily Caloric Goal (kcal)
                            </label>
                            <input
                                type="number"
                                value={profileData.caloricGoal}
                                onChange={(e) => setProfileData({...profileData, caloricGoal: e.target.value})}
                                className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-zinc-600 transition-colors"
                                placeholder="2000"
                            />
                            <p className="mt-2 text-xs text-gray-500">
                                Set your daily caloric intake target based on your fitness goal
                            </p>
                        </div>

                        <button
                             // onClick={handleSaveProfile}
                            disabled={saving}
                            className="w-full bg-white text-black px-4 py-2.5 rounded-lg font-medium hover:bg-gray-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
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
                </div>

                {/* Security Section */}
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
            </div>
        </div>
    );
}