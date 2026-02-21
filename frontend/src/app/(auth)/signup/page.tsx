"use client";

import { useState } from "react";
import { Dumbbell, Eye, EyeOff, Mail, Lock, User } from "lucide-react";
import Link from "next/link";
import { z } from "zod";
import { useRouter } from "next/navigation";
import EmailVerificationModal from "../../../components/sections/EmailVerificationModal";
import { signUpSchema } from "../../../lib/authSchemas";
import { registerUser } from "../../api/authService";

type SignUpFormData = z.infer<typeof signUpSchema>;


function Field({
  label,
  error,
  children,
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
      {error && <p className="text-xs text-red-400 font-mono mt-1">{error}</p>}
    </div>
  );
}



function TextInput({
  icon: Icon,
  right,
  hasError,
  ...props
}: React.InputHTMLAttributes<HTMLInputElement> & {
  icon: React.ElementType;
  right?: React.ReactNode;
  hasError?: boolean;
}) {
  return (
    <div className="relative">
      <Icon className="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-600" />
      <input
        {...props}
        className={`w-full bg-zinc-900 border ${
          hasError ? "border-red-500/60" : "border-zinc-800"
        } rounded-xl pl-10 ${right ? "pr-10" : "pr-4"} py-3 text-sm text-zinc-100
        placeholder:text-zinc-700 font-mono focus:outline-none focus:border-lime-500/50
        focus:ring-1 focus:ring-lime-500/20 transition-all duration-200`}
      />
      {right && (
        <div className="absolute right-3.5 top-1/2 -translate-y-1/2">{right}</div>
      )}
    </div>
  );
}



function PasswordToggle({ show, onToggle }: { show: boolean; onToggle: () => void }) {
  return (
    <button
      type="button"
      onClick={onToggle}
      className="text-zinc-600 hover:text-zinc-300 transition-colors"
    >
      {show ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
    </button>
  );
}



export default function SignUpPage() {
  const router = useRouter();
  const [formData, setFormData] = useState<SignUpFormData>({
    userName: "",
    email: "",
    password: "",
    confirmPassword: "",
    termsAccepted: false,
  });
  const [errors, setErrors]           = useState<Partial<Record<keyof SignUpFormData, string>>>({});
  const [showPassword, setShowPassword]               = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [isLoading, setIsLoading]                     = useState(false);
  const [apiError, setApiError]                       = useState("");
  const [showVerificationModal, setShowVerificationModal] = useState(false);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    setFormData((prev) => ({ ...prev, [name]: type === "checkbox" ? checked : value }));
    if (errors[name as keyof SignUpFormData]) {
      setErrors((prev) => ({ ...prev, [name]: undefined }));
    }
    setApiError("");
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setApiError("");
    setErrors({});
    try {
      const validated = signUpSchema.parse(formData);
      const response  = await registerUser(validated.userName, validated.email, validated.password);
      if (response.status === 201 || response.status === 200) {
        setShowVerificationModal(true);
      }
    } catch (error: any) {
      if (error instanceof z.ZodError) {
        const fieldErrors: Partial<Record<keyof SignUpFormData, string>> = {};
        error.errors.forEach((err) => {
          if (err.path[0]) fieldErrors[err.path[0] as keyof SignUpFormData] = err.message;
        });
        setErrors(fieldErrors);
      } else if (error.response) {
        setApiError(error.response.data?.message || "Registration failed. Please try again.");
      } else {
        setApiError("Network error. Please check your connection and try again.");
      }
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
            <pattern id="signup-grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#a3e635" strokeWidth="0.5" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#signup-grid)" />
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
          <p className="text-xs text-zinc-300 mt-2 uppercase tracking-widest">Start your fitness journey</p>
        </div>

        
        <div className="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 shadow-2xl">

         
          {apiError && (
            <div className="mb-5 px-4 py-3 bg-red-500/10 border border-red-500/30 rounded-xl">
              <p className="text-xs text-red-400 font-mono">{apiError}</p>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">

           
            <Field label="Username" error={errors.userName}>
              <TextInput
                icon={User}
                type="text"
                name="userName"
                value={formData.userName}
                onChange={handleInputChange}
                placeholder="john_doe"
                hasError={!!errors.userName}
                autoComplete="username"
              />
            </Field>

            
            <Field label="Email" error={errors.email}>
              <TextInput
                icon={Mail}
                type="email"
                name="email"
                value={formData.email}
                onChange={handleInputChange}
                placeholder="you@example.com"
                hasError={!!errors.email}
                autoComplete="email"
              />
            </Field>

            
            <Field label="Password" error={errors.password}>
              <TextInput
                icon={Lock}
                type={showPassword ? "text" : "password"}
                name="password"
                value={formData.password}
                onChange={handleInputChange}
                placeholder="Create a strong password"
                hasError={!!errors.password}
                autoComplete="new-password"
                right={<PasswordToggle show={showPassword} onToggle={() => setShowPassword(!showPassword)} />}
              />
            </Field>

            
            <Field label="Confirm Password" error={errors.confirmPassword}>
              <TextInput
                icon={Lock}
                type={showConfirmPassword ? "text" : "password"}
                name="confirmPassword"
                value={formData.confirmPassword}
                onChange={handleInputChange}
                placeholder="Confirm your password"
                hasError={!!errors.confirmPassword}
                autoComplete="new-password"
                right={<PasswordToggle show={showConfirmPassword} onToggle={() => setShowConfirmPassword(!showConfirmPassword)} />}
              />
            </Field>

            
            <div className="space-y-1">
              <label className="flex items-start gap-3 cursor-pointer group">
                <div className="relative mt-0.5 shrink-0">
                  <input
                    type="checkbox"
                    name="termsAccepted"
                    checked={formData.termsAccepted}
                    onChange={handleInputChange}
                    className="peer sr-only"
                  />
                  <div className="w-4 h-4 rounded border border-zinc-700 bg-zinc-800 peer-checked:bg-lime-400 peer-checked:border-lime-400 transition-all duration-200 flex items-center justify-center">
                    <svg className="w-2.5 h-2.5 text-zinc-900 opacity-0 peer-checked:opacity-100 hidden peer-checked:block" fill="none" viewBox="0 0 10 8">
                      <path d="M1 4l2.5 2.5L9 1" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                    </svg>
                  </div>
                </div>
                <span className="text-xs text-zinc-500 leading-relaxed">
                  I agree to the{" "}
                  <Link href="/terms" className="text-lime-400 hover:text-lime-300 transition-colors">Terms of Service</Link>
                  {" "}and{" "}
                  <Link href="/privacy" className="text-lime-400 hover:text-lime-300 transition-colors">Privacy Policy</Link>
                </span>
              </label>
              {errors.termsAccepted && (
                <p className="text-xs text-red-400 font-mono">{errors.termsAccepted}</p>
              )}
            </div>

            
            <button
              type="submit"
              disabled={isLoading}
              className="w-full mt-2 py-3 bg-lime-400 hover:bg-lime-300 disabled:opacity-50 disabled:cursor-not-allowed text-zinc-900 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 shadow-lg shadow-lime-400/10 flex items-center justify-center"
            >
              {isLoading ? (
                <div className="w-4 h-4 border-2 border-zinc-900/30 border-t-zinc-900 rounded-full animate-spin" />
              ) : (
                "Create Account"
              )}
            </button>

          </form>

          <p className="mt-6 text-center text-xs text-zinc-600">
            Already have an account?{" "}
            <Link href="/login" className="text-lime-400 hover:text-lime-300 font-semibold transition-colors">
              Sign in
            </Link>
          </p>
        </div>

      </div>

      
      {showVerificationModal && (
        <EmailVerificationModal
          isOpen={showVerificationModal}
          onClose={() => setShowVerificationModal(false)}
          email={formData.email}
          onSuccess={() => router.push("/login?verified=true")}
        />
      )}
    </div>
  );
}