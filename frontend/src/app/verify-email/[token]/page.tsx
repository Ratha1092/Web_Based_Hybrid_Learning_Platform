"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import Link from "next/link";
import { apiFetch } from "@/src/lib/api";

export default function VerifyEmailPage() {
  const router = useRouter();
  const { token } = useParams();

  const [status, setStatus] = useState<"verifying" | "success" | "error">("verifying");
  const [message, setMessage] = useState("Verifying your email...");

  useEffect(() => {
    const verifyEmail = async () => {
      if (!token) {
        setStatus("error");
        setMessage("Invalid verification link");
        return;
      }

      try {
        await apiFetch(`/verify-email/${token}`, {
          method: "POST",
        });

        setStatus("success");
        setMessage("Email verified successfully!");
        setTimeout(() => router.push("/login"), 3000);
      } catch (err: any) {
        setStatus("error");
        setMessage(err.message || "Email verification failed. Link may have expired.");
        console.error("Email verification error:", err);
      }
    };

    verifyEmail();
  }, [token, router]);

  return (
    <div className="flex h-screen items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
      <div className="bg-white p-8 rounded-lg shadow-lg w-96 text-center space-y-4">
        <h2 className="text-2xl font-bold text-gray-800">Email Verification</h2>

        {status === "verifying" && (
          <>
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            <p className="text-gray-600">{message}</p>
          </>
        )}

        {status === "success" && (
          <>
            <div className="text-5xl">✅</div>
            <p className="text-green-700 font-semibold">{message}</p>
            <p className="text-sm text-gray-600">Redirecting to login...</p>
          </>
        )}

        {status === "error" && (
          <>
            <div className="text-5xl">❌</div>
            <p className="text-red-700 font-semibold">{message}</p>
            <div className="space-y-2 text-sm">
              <p className="text-gray-600">
                <Link href="/login" className="text-indigo-600 font-semibold hover:underline">
                  Back to Login
                </Link>
              </p>
              <p className="text-gray-600">
                <Link href="/register" className="text-indigo-600 font-semibold hover:underline">
                  Register Again
                </Link>
              </p>
            </div>
          </>
        )}
      </div>
    </div>
  );
}
