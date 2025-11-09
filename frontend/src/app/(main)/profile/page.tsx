import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';

interface User {
    name: string;
    email: string;
    role: string;
}

export default async function ProfilePage() {
    const cookieStore = await cookies();
    const userToken = cookieStore.get('jwt_token');

    if (!userToken) {
        redirect('/login');
    }

    try {
        const response = await fetch("http://localhost:8000/api/user", {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${userToken.value}`,
            },
            cache: 'no-store' // Always get fresh user data
        });

        if (!response.ok) {
            if (response.status === 401) {
                redirect('/login');
            }
            throw new Error('Failed to fetch user data');
        }

        const user: User = await response.json();

        return (
            <div className="min-h-screen p-8 ">
                <div className="max-w-2xl mx-auto">
                    <h1 className="text-3xl font-bold mb-6 text-gray-800">Profile</h1>

                    <div className="rounded-lg shadow-md p-6 space-y-4">
                        <div className="grid gap-4">
                            <div className="flex">
                                <span className="font-medium text-gray-600 w-32">Username:</span>
                                <span className="text-gray-900 font-semibold">{user.name}</span>
                            </div>

                            <div className="flex">
                                <span className="font-medium text-gray-600 w-32">Email:</span>
                                <span className="text-gray-900">{user.email}</span>
                            </div>

                            <div className="flex">
                                <span className="font-medium text-gray-600 w-32">Role:</span>
                                <span className="text-gray-900 capitalize bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                    {user.role}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );

    } catch (error) {
        console.error('Error fetching user:', error);
        return (
            <div className="min-h-screen p-8">
                <div className="max-w-2xl mx-auto">
                    <div className="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg">
                        <p className="font-bold text-lg mb-2">Error</p>
                        <p>Failed to load user data. Please try again later.</p>
                    </div>
                </div>
            </div>
        );
    }
}