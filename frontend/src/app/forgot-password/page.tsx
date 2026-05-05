"use client";

import { useState } from "react";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { apiFetch } from "@/src/lib/api";

export default function ForgotPasswordPage() {
  const router = useRouter();
  const [email, setEmail] = useState("");
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState("");

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");

    if (!email.trim()) {
      setError("Email is required");
      return;
    }

    try {
      setLoading(true);

      await apiFetch("/auth/forgot-password", {
        method: "POST",
        body: JSON.stringify({ email }),
      });

      setSuccess(true);
      setEmail("");
    } catch (err: any) {
      setError(err.message || "Failed to send reset link");
      console.error("Forgot password error:", err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex h-screen items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
      <form
        onSubmit={handleSubmit}
        className="bg-white p-8 rounded-lg shadow-lg w-96 space-y-4"
      >
        <h2 className="text-2xl font-bold text-gray-800 text-center">Forgot Password?</h2>
        <p className="text-sm text-gray-600 text-center">
          Enter your email address and we'll send you a link to reset your password.
        </p>

        {success && (
          <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
            ✅ Password reset link sent to your email. Check your inbox!
          </div>
        )}

        {error && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            {error}
          </div>
        )}

        <input
          type="email"
          placeholder="Email Address"
          className="w-full border border-gray-300 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500"
          value={email}
          onChange={(e) => {
            setEmail(e.target.value);
            setError("");
          }}
          disabled={loading || success}
        />

        <button
          type="submit"
          className="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded transition disabled:opacity-50 disabled:cursor-not-allowed"
          disabled={loading || success}
        >
          {loading ? "Sending..." : "Send Reset Link"}
        </button>

        <div className="text-center space-y-2 text-sm">
          <p className="text-gray-600">
            Remember your password?{" "}
            <Link href="/login" className="text-indigo-600 font-semibold hover:underline">
              Login
            </Link>
          </p>
          <p className="text-gray-600">
            Don't have an account?{" "}
            <Link href="/register" className="text-indigo-600 font-semibold hover:underline">
              Register
            </Link>
          </p>
        </div>
      </form>
    </div>
  );
}
