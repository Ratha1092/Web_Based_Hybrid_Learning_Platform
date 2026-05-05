"use client";

import { useState } from "react";
import { useAuth } from "@/src/context/AuthContext";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { ProtectedRoute } from "@/src/components/ProtectedRoute";
import { apiFetch } from "@/src/lib/api";

export default function TwoFactorSetupPage() {
  const router = useRouter();
  const { user, token } = useAuth();

  const [step, setStep] = useState<"request" | "verify">("request");
  const [code, setCode] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState(false);
  const [displayCode, setDisplayCode] = useState(""); // For testing

  const handleRequestCode = async () => {
    setError("");
    setLoading(true);

    try {
      const data = await apiFetch("/auth/2fa/enable", {
        method: "POST",
      });

      // For testing, show the code (remove in production)
      setDisplayCode(data.data.code);
      setStep("verify");
    } catch (err: any) {
      setError(err.message || "Failed to request code");
      console.error("2FA enable error:", err);
    } finally {
      setLoading(false);
    }
  };

  const handleVerifyCode = async () => {
    setError("");

    if (code.length !== 6) {
      setError("Code must be 6 digits");
      return;
    }

    setLoading(true);

    try {
      await apiFetch("/auth/2fa/verify-enable", {
        method: "POST",
        body: JSON.stringify({ code }),
      });

      setSuccess(true);
      setTimeout(() => router.push("/security-settings"), 2000);
    } catch (err: any) {
      setError(err.message || "Verification failed");
      console.error("2FA verify error:", err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <ProtectedRoute>
      <div className="flex h-screen items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
        <div className="bg-white p-8 rounded-lg shadow-lg w-96 space-y-4">
          <h2 className="text-2xl font-bold text-gray-800 text-center">Enable 2FA</h2>
          <p className="text-sm text-gray-600 text-center">
            Add an extra layer of security to your account
          </p>

          {success && (
            <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-center">
              ✅ 2FA enabled successfully! Redirecting...
            </div>
          )}

          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded text-sm">
              {error}
            </div>
          )}

          {step === "request" ? (
            <>
              <p className="text-gray-700 text-center">
                You'll receive a 6-digit code via email to verify your identity.
              </p>

              <button
                onClick={handleRequestCode}
                disabled={loading || success}
                className="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {loading ? "Sending Code..." : "Send Verification Code"}
              </button>
            </>
          ) : (
            <>
              <div className="bg-blue-50 border border-blue-200 p-4 rounded text-sm text-blue-700">
                📧 Check your email ({user?.email}) for the 6-digit code
              </div>

              {displayCode && (
                <div className="bg-yellow-50 border border-yellow-200 p-4 rounded text-center">
                  <p className="text-xs text-yellow-700 mb-2">Testing Code (remove in production):</p>
                  <p className="text-2xl font-bold text-yellow-900 font-mono">{displayCode}</p>
                </div>
              )}

              <input
                type="text"
                maxLength={6}
                placeholder="Enter 6-digit code"
                className="w-full border border-gray-300 px-4 py-2 rounded text-center text-2xl font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value={code}
                onChange={(e) => {
                  setCode(e.target.value.replace(/\D/g, "").slice(0, 6));
                  setError("");
                }}
                disabled={loading}
              />

              <button
                onClick={handleVerifyCode}
                disabled={code.length !== 6 || loading}
                className="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {loading ? "Verifying..." : "Verify Code"}
              </button>

              <button
                onClick={() => {
                  setStep("request");
                  setCode("");
                  setError("");
                  setDisplayCode("");
                }}
                className="w-full text-indigo-600 font-semibold py-2 rounded hover:underline disabled:opacity-50"
                disabled={loading}
              >
                Request New Code
              </button>
            </>
          )}

          <p className="text-center text-sm text-gray-600">
            <Link href="/security-settings" className="text-indigo-600 font-semibold hover:underline">
              Back to Security Settings
            </Link>
          </p>
        </div>
      </div>
    </ProtectedRoute>
  );
}
