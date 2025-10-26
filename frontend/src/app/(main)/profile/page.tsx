"use client"
import { jwtDecode } from "jwt-decode";
import { useEffect, useState} from "react";
import getCookie from "../../../lib/getCookie";

interface User {
    id: string;
    username: string;
    profileImage?: string;
}

export default function ProfilePage() {
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState("");
    const [error, setError] = useState<string | null>(null);
    const [user, setUser] = useState<User | null>(null);

    useEffect(() => {
        const storedToken = getCookie("jwt_token");
        console.log(storedToken);
        if (storedToken) {
            try {
                const decoded: any = jwtDecode(storedToken);
                setUser({
                    id: decoded.id,
                    username: decoded.username || "Guest",
                    profileImage: decoded.profileImage || null,
                });
            } catch (error) {
                console.error("Error decoding token:", error);
                setUser(null);
            }
        }
    }, []);

    return (
        <div>{user?.username || "Unknown"}</div>
     );
}