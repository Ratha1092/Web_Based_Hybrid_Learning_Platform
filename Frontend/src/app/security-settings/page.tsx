"use client";

import { useState, useEffect } from "react";
import { useAuth } from "@/src/context/AuthContext";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { ProtectedRoute } from "@/src/components/ProtectedRoute";

interface SecurityStatus {
  is_verified: boolean;
  verified_at?: string;
}

interface TwoFAStatus {
  two_factor_enabled: boolean;
}

interface OAuthAccount {
  id: number;
  provider: string;
  email: string;
  avatar?: string;
  created_at: string;
}

export default function SecuritySettingsPage() {
  const { user, token } = useAuth();
  const router = useRouter();

  const [emailStatus, setEmailStatus] = useState<SecurityStatus | null>(null);
  const [twoFAStatus, setTwoFAStatus] = useState<TwoFAStatus | null>(null);
  const [oauthAccounts, setOAuthAccounts] = useState<OAuthAccount[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [passwordInput, setPasswordInput] = useState("");
  const [disabling2FA, setDisabling2FA] = useState(false);

  useEffect(() => {
    const fetchSecurityStatus = async () => {
      if (!token) return;

      try {
        const [emailRes, twoFARes, oauthRes] = await Promise.all([
          fetch("http://localhost:8000/api/v1/auth/email/status", {
            headers: { Authorization: `Bearer ${token}` },
          }),
          fetch("http://localhost:8000/api/v1/auth/2fa/status", {
            headers: { Authorization: `Bearer ${token}` },
          }),
          fetch("http://localhost:8000/api/v1/auth/oauth/accounts", {
            headers: { Authorization: `Bearer ${token}` },
          }),
        ]);

        if (emailRes.ok) setEmailStatus(await emailRes.json().then((d) => d.data));
        if (twoFARes.ok) setTwoFAStatus(await twoFARes.json().then((d) => d.data));
        if (oauthRes.ok) setOAuthAccounts(await oauthRes.json().then((d) => d.data));
      } catch (err) {
        console.error("Error fetching security status:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchSecurityStatus();
  }, [token]);

  const handleDisable2FA = async () => {
    if (!passwordInput.trim()) {
      setError("Password is required");
      return;
    }

    setDisabling2FA(true);
    setError("");

    try {
      const res = await fetch("http://localhost:8000/api/v1/auth/2fa/disable", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ password: passwordInput }),
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.message || "Failed to disable 2FA");
      }

      setTwoFAStatus({ two_factor_enabled: false });
      setPasswordInput("");
    } catch (err: any) {
      setError(err.message || "Failed to disable 2FA");
    } finally {
      setDisabling2FA(false);
    }
  };

  const handleUnlinkOAuth = async (provider: string) => {
    if (!confirm(`Are you sure you want to unlink ${provider}?`)) return;

    try {
      const res = await fetch(`http://localhost:8000/api/v1/auth/oauth/${provider}`, {
        method: "DELETE",
        headers: { Authorization: `Bearer ${token}` },
      });

      if (!res.ok) throw new Error("Failed to unlink account");

      setOAuthAccounts(oauthAccounts.filter((a) => a.provider !== provider));
    } catch (err: any) {
      setError(err.message || "Failed to unlink account");
    }
  };

  const handleSendVerificationEmail = async () => {
    try {
      const res = await fetch("http://localhost:8000/api/v1/auth/email/send-verification", {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
      });

      const data = await res.json();

      if (!res.ok) throw new Error(data.message || "Failed to send verification email");

      alert("Verification email sent! Check your inbox.");
    } catch (err: any) {
      setError(err.message);
    }
  };

  if (loading) {
    return (
      <ProtectedRoute>
        <div className="flex h-screen items-center justify-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>
      </ProtectedRoute>
    );
  }

  return (
    <ProtectedRoute>
      <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-8">
        <div className="max-w-2xl mx-auto">
          <div className="bg-white rounded-lg shadow-lg p-8">
            <div className="flex items-center justify-between mb-8">
              <h1 className="text-3xl font-bold text-gray-800">Security Settings</h1>
              <Link href="/dashboard" className="text-indigo-600 hover:underline font-semibold">
                ← Back
              </Link>
            </div>

            {error && (
              <div className="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                {error}
              </div>
            )}

            {/* Email Verification */}
            <div className="border-b pb-6 mb-6">
              <div className="flex items-center justify-between">
                <div>
                  <h2 className="text-xl font-bold text-gray-800 flex items-center gap-2">
                    {emailStatus?.is_verified ? "✅" : "⚠️"} Email Verification
                  </h2>
                  <p className="text-gray-600 mt-1">
                    {emailStatus?.is_verified
                      ? `Verified on ${new Date(emailStatus.verified_at || "").toLocaleDateString()}`
                      : "Your email is not verified"}
                  </p>
                </div>
                {!emailStatus?.is_verified && (
                  <button
                    onClick={handleSendVerificationEmail}
                    className="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded transition"
                  >
                    Send Verification Email
                  </button>
                )}
              </div>
            </div>

            {/* Two-Factor Authentication */}
            <div className="border-b pb-6 mb-6">
              <div className="flex items-center justify-between mb-4">
                <div>
                  <h2 className="text-xl font-bold text-gray-800 flex items-center gap-2">
                    {twoFAStatus?.two_factor_enabled ? "✅" : "🔒"} Two-Factor Authentication
                  </h2>
                  <p className="text-gray-600 mt-1">
                    {twoFAStatus?.two_factor_enabled
                      ? "2FA is enabled on your account"
                      : "Add 2FA to secure your account"}
                  </p>
                </div>
              </div>

              {!twoFAStatus?.two_factor_enabled ? (
                <button
                  onClick={() => router.push("/2fa-setup")}
                  className="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded transition"
                >
                  Enable 2FA
                </button>
              ) : (
                <div className="space-y-3">
                  <p className="text-sm text-gray-600">
                    To disable 2FA, enter your password for verification:
                  </p>
                  <div className="flex gap-2">
                    <input
                      type="password"
                      placeholder="Enter password"
                      value={passwordInput}
                      onChange={(e) => {
                        setPasswordInput(e.target.value);
                        setError("");
                      }}
                      className="flex-1 border border-gray-300 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      disabled={disabling2FA}
                    />
                    <button
                      onClick={handleDisable2FA}
                      disabled={disabling2FA || !passwordInput.trim()}
                      className="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      {disabling2FA ? "Disabling..." : "Disable 2FA"}
                    </button>
                  </div>
                </div>
              )}
            </div>

            {/* OAuth Accounts */}
            <div>
              <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                🔗 Connected Accounts
              </h2>

              {oauthAccounts.length === 0 ? (
                <p className="text-gray-600 mb-4">No connected OAuth accounts yet</p>
              ) : (
                <div className="space-y-3 mb-4">
                  {oauthAccounts.map((account) => (
                    <div key={account.id} className="flex items-center justify-between bg-gray-50 p-4 rounded border border-gray-200">
                      <div className="flex items-center gap-3">
                        {account.avatar && (
                          <img src={account.avatar} alt="" className="w-10 h-10 rounded-full" />
                        )}
                        <div>
                          <p className="font-semibold text-gray-800 capitalize">{account.provider}</p>
                          <p className="text-sm text-gray-600">{account.email}</p>
                        </div>
                      </div>
                      <button
                        onClick={() => handleUnlinkOAuth(account.provider)}
                        className="text-red-600 hover:text-red-700 font-semibold text-sm"
                      >
                        Unlink
                      </button>
                    </div>
                  ))}
                </div>
              )}

              <div className="space-y-2">
                <p className="text-sm text-gray-600 mb-2">Link more providers:</p>
                <div className="grid grid-cols-2 gap-2">
                  <button className="bg-gray-800 hover:bg-gray-900 text-white font-semibold py-2 rounded transition">
                    Connect GitHub
                  </button>
                  <button className="bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-semibold py-2 rounded transition">
                    Connect Google
                  </button>
                </div>
              </div>
            </div>

            {/* Activity Monitoring */}
            <div className="mt-8 pt-6 border-t">
              <h2 className="text-xl font-bold text-gray-800 mb-3">📊 Activity Monitoring</h2>
              <p className="text-gray-600 mb-4">View your login history and activity logs</p>
              <Link
                href="/activity-history"
                className="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded transition inline-block"
              >
                View Activity History →
              </Link>
            </div>

            {/* Password Change */}
            <div className="mt-6 pt-6 border-t">
              <Link href="/change-password" className="text-indigo-600 hover:underline font-semibold">
                Change Password →
              </Link>
            </div>
          </div>
        </div>
      </div>
    </ProtectedRoute>
  );
}
