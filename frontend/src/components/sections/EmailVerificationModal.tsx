"use client";

import { useState, useEffect } from "react";
import { X } from "lucide-react";
import { Input } from "../ui/input";
import axiosInstance from "../../app/api/axios";
import { RefreshCw } from "lucide-react";
import { verifyEmailCode } from "../../app/api/authService";

interface VerificationModalProps {
  isOpen: boolean;
  onClose: () => void;
  email: string;
  onSuccess: () => void;
}

function VerificationCodeInputs({
  value,
  onChange,
  disabled,
}: {
  value: string;
  onChange: (val: string) => void;
  disabled?: boolean;
}) {
  const handleInput = (e: React.ChangeEvent<HTMLInputElement>, idx: number) => {
    const val = e.target.value.replace(/[^0-9]/g, "");
    const newValue = value.split("");
    newValue[idx] = val;
    
    if (val && idx < 4) {
      const next = document.getElementById(`code-input-${idx + 1}`);
      next?.focus();
    }
    onChange(newValue.join(""));
  };

  const handlePaste = (e: React.ClipboardEvent<HTMLInputElement>) => {
    const paste = e.clipboardData.getData("text").replace(/[^0-9]/g, "").slice(0, 5);
    onChange(paste.padEnd(5, ""));
  };

  return (
    <div className="flex justify-center space-x-2 mb-4">
      {[0, 1, 2, 3, 4].map((idx) => (
        <Input
          key={idx}
          id={`code-input-${idx}`}
          type="text"
          inputMode="numeric"
          maxLength={1}
          value={value[idx] || ""}
          onChange={(e) => handleInput(e, idx)}
          onPaste={handlePaste}
          disabled={disabled}
          className="text-center text-xl tracking-widest w-12 h-12 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500"
        />
      ))}
    </div>
  );
}

export default function EmailVerificationModal({ isOpen, onClose, email, onSuccess }: VerificationModalProps) {
  const [verificationCode, setVerificationCode] = useState("");
  const [isVerifying, setIsVerifying] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState(false);

  const [timer, setTimer] = useState(60);
  const [canResend, setCanResend] = useState(false);
  const [isResending, setIsResending] = useState(false);

  useEffect(() => {
    let interval: NodeJS.Timeout;
    if (isOpen && timer > 0) {
      interval = setInterval(() => setTimer((prev) => prev - 1), 1000);
    } else if (timer === 0) {
      setCanResend(true);
    }
    return () => clearInterval(interval);
  }, [isOpen, timer]);

  useEffect(() => {
  if (success) {
    const timer = setTimeout(() => {
      onSuccess();
    }, 1500);
    return () => clearTimeout(timer);
  }
}, [success]);

  if (!isOpen) return null;

  const handleVerifyCode = async () => {
    setIsVerifying(true);
    setError("");
    try {
      const response = await verifyEmailCode(email, verificationCode);
      if (response.status === 200) {
        setSuccess(true);
        setTimeout(() => {
          onSuccess(); 
      }, 1500);
      }
    } catch (err: any) {
      setError(err.response?.data?.message || "Verification failed. Please try again.");
    } finally {
      setIsVerifying(false); 
    }
};



  const handleResend = async () => {
    return false; // Temporarily disable resend functionality
  };

  return (
    <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div className="bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl w-full max-w-md p-6 text-center relative">
        <button onClick={onClose} className="absolute top-3 right-3 text-slate-400 hover:text-white transition-colors">
          <X className="h-5 w-5" />
        </button>

        <h2 className="text-2xl font-semibold text-white mb-2">Verify Your Email</h2>
        <p className="text-slate-300 mb-6 text-sm">
          A 5-digit code was sent to <span className="text-blue-400">{email}</span>.
        </p>

        <VerificationCodeInputs value={verificationCode} onChange={setVerificationCode} disabled={isVerifying} />

        {error && <p className="text-red-400 text-xs mb-3">{error}</p>}
        {success && <p className="text-green-400 text-xs mb-3">Verified! Redirecting...</p>}

        <button
          onClick={handleVerifyCode}
          disabled={isVerifying || verificationCode.length !== 5 || success}
          className="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-slate-700 text-white font-medium py-3 rounded-lg mb-4"
        >
          {isVerifying ? "Verifying..." : "Verify"}
        </button>

        <div className="text-sm">
          {canResend ? (
            <button 
              onClick={handleResend} 
              disabled={isResending}
              className="text-blue-400 hover:underline flex items-center justify-center gap-2 mx-auto"
            >
              <RefreshCw className={`h-3 w-3 ${isResending ? 'animate-spin' : ''}`} />
              Resend Code
            </button>
          ) : (
            <p className="text-slate-500">Resend code in <span className="font-mono">{timer}s</span></p>
          )}
        </div>
      </div>
    </div>
  );
}