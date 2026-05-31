import { useState, useEffect } from "react";
import { jwtDecode } from "jwt-decode";
import { getUser } from "@/app/api/authService";
import { getUserSettingsById, updateUserSettings } from "@/app/api/userSettingsService";
import { DecodedToken } from "@/types/types";

export function useProfile() {
    const [user, setUser] = useState<DecodedToken | null>(null);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');
    const [errorMessage, setErrorMessage] = useState('');
    
    const [profileData, setProfileData] = useState({
        age: 0,
        height: 0,
        caloric_goal: 0,
        goal_type: 'maintaining',
        current_weight: 0,
        target_weight: 0
    });

    const [passwordData, setPasswordData] = useState({
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
    });

    useEffect(() => {
        const currentUser = getUser();
        const token = currentUser?.token;
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
        const currentUser = getUser();
        const id = currentUser?.id;
        
        async function fetchUserSettings() {
            if (!id) return;
            try {
                setLoading(true);
                const settings = await getUserSettingsById(id);
                setProfileData({
                    current_weight: parseFloat(settings.current_weight) || 0,
                    age: parseInt(settings.age) || 0,
                    height: parseFloat(settings.height) || 0,
                    caloric_goal: parseInt(settings.caloric_goal) || 0,
                    target_weight: parseFloat(settings.target_weight) || 0,
                    goal_type: settings.goal_type || 'maintaining',
                });
            } catch (error) {
                console.error("Failed to fetch user settings:", error);
            } finally {
                setLoading(false);
            }
        }
        fetchUserSettings();
    }, [user]);

    // 3. Mutate Profile Data
    const handleSaveProfile = async () => {
        try {
            setSaving(true);
            const currentUser = getUser();
            const id = currentUser?.id;
            const res = await updateUserSettings(id, profileData);

            if (res) {
                setSuccessMessage('Profile updated successfully.');
                setErrorMessage('');
            }
        } catch (error) {
            console.error('Error response:', error.response?.data);
            setErrorMessage('Failed to update profile. Please try again.');
            setSuccessMessage('');
        } finally {
            setSaving(false);
        }
    };

    const handlePasswordChange = async () => {
        // Implement password change logic here
    };

    return {
        user,
        loading,
        saving,
        profileData,
        setProfileData,
        passwordData,
        setPasswordData,
        successMessage,
        errorMessage,
        handleSaveProfile,
        handlePasswordChange
    };
}