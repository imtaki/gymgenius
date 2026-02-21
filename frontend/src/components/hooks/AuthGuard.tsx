'use client';

import { useEffect, useState } from 'react';
import { useRouter, usePathname } from 'next/navigation';

export default function AuthGuard({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const pathname = usePathname();
  const [authorized, setAuthorized] = useState(false);

  useEffect(() => {
    const token = localStorage.getItem('user');

    if (!token) {
      setAuthorized(false);
      router.push(`/login?callbackUrl=${pathname}`);
    } else {
      setAuthorized(true);
    }
  }, [pathname, router]);
  
  if (!authorized) return null; 

  return <>{children}</>;
}