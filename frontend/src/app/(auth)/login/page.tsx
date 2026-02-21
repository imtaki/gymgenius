"use client";

import { useState } from "react";
import { Dumbbell, Eye, EyeOff, Mail, Lock } from "lucide-react";
import Link from "next/link";
import { loginUser } from "../../api/authService";
import { useRouter } from "next/navigation";


function Field({
  label, error, children,
}: {
  label: string;
  error?: string;
  children: React.ReactNode;
}) {
  return (
    <div className="space-y-1.5">
      <label className="block text-xs font-semibold text-zinc-500 uppercase tracking-widest">
        {label}
      </label>
      {children}
      {error && <p className="text-xs text-red-400 font-mono">{error}</p>}
    </div>
  );
}


function TextInput({
  icon: Icon,
  right,
  error,
  ...props
}: React.InputHTMLAttributes<HTMLInputElement> & {
  icon: React.ElementType;
  right?: React.ReactNode;
  error?: boolean;
}) {
  return (
    <div className="relative">
      <Icon className="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-600" />
      <input
        {...props}
        className={`w-full bg-zinc-900 border ${
          error ? "border-red-500/60" : "border-zinc-800"
        } rounded-xl pl-10 pr-10 py-3 text-sm text-zinc-100 placeholder:text-zinc-700 font-mono
        focus:outline-none focus:border-lime-500/50 focus:ring-1 focus:ring-lime-500/20 transition-all duration-200`}
      />
      {right && (
        <div className="absolute right-3.5 top-1/2 -translate-y-1/2">{right}</div>
      )}
    </div>
  );
}


export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail]       = useState("");
  const [password, setPassword] = useState("");
  const [error, setError]       = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading]       = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    if (!email || !password) { setError("Email and password are required"); return; }
    setIsLoading(true);
    try {
      const data = await loginUser(email, password);
      if (data) {
        router.push("/dashboard");
      } else {
        setError("Login failed — no token received");
      }
    } catch (err: any) {
      setError(err?.response?.data?.message || "Login failed");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div
      className="min-h-screen bg-zinc-950 flex items-center justify-center p-4"
      style={{ fontFamily: "'DM Mono', 'Fira Code', monospace" }}
    >
      
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[400px] bg-lime-400/[0.03] rounded-full blur-3xl" />
        <svg className="absolute inset-0 w-full h-full opacity-[0.02]" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="login-grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#a3e635" strokeWidth="0.5" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#login-grid)" />
        </svg>
      </div>

      <div className="relative w-full max-w-sm">

        
        <div className="text-center mb-8">
          <Link href="/" className="inline-flex flex-col items-center gap-3 group">
            <div className="w-12 h-12 rounded-2xl bg-lime-400 flex items-center justify-center shadow-xl shadow-lime-400/20 group-hover:bg-lime-300 transition-colors">
              <Dumbbell className="h-6 w-6 text-zinc-900" />
            </div>
            <span className="text-lg font-black text-zinc-100 uppercase tracking-widest">GymGenius</span>
          </Link>
          <p className="text-xs text-zinc-300 mt-2 uppercase tracking-widest">Sign in to your account</p>
        </div>

        
        <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 shadow-2xl">

          
          {error && (
            <div className="mb-5 px-4 py-3 bg-red-500/10 border border-red-500/30 rounded-xl">
              <p className="text-xs text-red-400 font-mono">{error}</p>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">

            <Field label="Email">
              <TextInput
                icon={Mail}
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="you@example.com"
                autoComplete="email"
              />
            </Field>

            <Field label="Password">
              <TextInput
                icon={Lock}
                type={showPassword ? "text" : "password"}
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="••••••••"
                autoComplete="current-password"
                right={
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="text-zinc-600 hover:text-zinc-300 transition-colors"
                  >
                    {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                  </button>
                }
              />
            </Field>

            <button
              type="submit"
              disabled={isLoading}
              className="w-full mt-2 py-3 bg-lime-400 hover:bg-lime-300 disabled:opacity-50 disabled:cursor-not-allowed text-zinc-900 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 shadow-lg shadow-lime-400/10 flex items-center justify-center"
            >
              {isLoading ? (
                <div className="w-4 h-4 border-2 border-zinc-900/30 border-t-zinc-900 rounded-full animate-spin" />
              ) : (
                "Sign In"
              )}
            </button>
          </form>

          <p className="mt-6 text-center text-xs text-zinc-600">
            No account?{" "}
            <Link href="/signup" className="text-lime-400 hover:text-lime-300 font-semibold transition-colors">
              Sign up free
            </Link>
          </p>
        </div>

      </div>
    </div>
  );
}