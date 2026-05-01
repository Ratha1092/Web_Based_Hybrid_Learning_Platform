"use client";

import { useState, useEffect } from "react";
import { useAuth } from "@/src/context/AuthContext";
import Link from "next/link";
import { ProtectedRoute } from "@/src/components/ProtectedRoute";

interface ActivityLog {
  id: number;
  user_id: number;
  action: string;
  ip_address: string;
  user_agent: string;
  data?: Record<string, any>;
  created_at: string;
}

export default function ActivityHistoryPage() {
  const { token } = useAuth();

  const [logs, setLogs] = useState<ActivityLog[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [filter, setFilter] = useState<"all" | "logins">("all");

  useEffect(() => {
    const fetchActivity = async () => {
      if (!token) return;

      try {
        const endpoint =
          filter === "logins"
            ? "http://localhost:8000/api/v1/activity/logins"
            : "http://localhost:8000/api/v1/activity/history";

        const res = await fetch(endpoint, {
          headers: { Authorization: `Bearer ${token}` },
        });

        const data = await res.json();

        if (!res.ok) throw new Error(data.message || "Failed to fetch activity");

        setLogs(data.data.logs || []);
      } catch (err: any) {
        setError(err.message || "Failed to load activity history");
      } finally {
        setLoading(false);
      }
    };

    fetchActivity();
  }, [token, filter]);

  const getActionBadge = (action: string) => {
    const badges: Record<string, { color: string; icon: string }> = {
      login: { color: "green", icon: "✅" },
      logout: { color: "gray", icon: "🚪" },
      failed_login: { color: "red", icon: "❌" },
      registration: { color: "blue", icon: "📝" },
      password_changed: { color: "orange", icon: "🔐" },
      password_reset_requested: { color: "orange", icon: "🔄" },
      email_verified: { color: "green", icon: "📧" },
      "2fa_enabled": { color: "purple", icon: "🔒" },
      "2fa_disabled": { color: "purple", icon: "🔓" },
      oauth_signup: { color: "blue", icon: "🔗" },
      oauth_linked: { color: "blue", icon: "🔗" },
      oauth_unlinked: { color: "gray", icon: "✂️" },
    };

    const badge = badges[action] || { color: "gray", icon: "ℹ️" };
    const colorClass =
      badge.color === "green"
        ? "bg-green-100 text-green-800"
        : badge.color === "red"
          ? "bg-red-100 text-red-800"
          : badge.color === "blue"
            ? "bg-blue-100 text-blue-800"
            : badge.color === "orange"
              ? "bg-orange-100 text-orange-800"
              : badge.color === "purple"
                ? "bg-purple-100 text-purple-800"
                : "bg-gray-100 text-gray-800";

    return (
      <span className={`${colorClass} px-3 py-1 rounded-full text-sm font-semibold`}>
        {badge.icon} {action.replace(/_/g, " ").toUpperCase()}
      </span>
    );
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
        <div className="max-w-4xl mx-auto">
          <div className="bg-white rounded-lg shadow-lg overflow-hidden">
            <div className="p-8 border-b">
              <div className="flex items-center justify-between mb-6">
                <h1 className="text-3xl font-bold text-gray-800">Activity History</h1>
                <Link href="/security-settings" className="text-indigo-600 hover:underline font-semibold">
                  ← Back
                </Link>
              </div>

              <div className="flex gap-2">
                <button
                  onClick={() => setFilter("all")}
                  className={`px-4 py-2 rounded font-semibold transition ${
                    filter === "all"
                      ? "bg-indigo-600 text-white"
                      : "bg-gray-200 text-gray-800 hover:bg-gray-300"
                  }`}
                >
                  All Activity
                </button>
                <button
                  onClick={() => setFilter("logins")}
                  className={`px-4 py-2 rounded font-semibold transition ${
                    filter === "logins"
                      ? "bg-indigo-600 text-white"
                      : "bg-gray-200 text-gray-800 hover:bg-gray-300"
                  }`}
                >
                  Login History
                </button>
              </div>
            </div>

            {error && (
              <div className="m-8 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                {error}
              </div>
            )}

            {logs.length === 0 ? (
              <div className="p-8 text-center text-gray-600">
                <p className="text-lg">No activity found</p>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-gray-50 border-b">
                    <tr>
                      <th className="px-6 py-3 text-left text-sm font-semibold text-gray-800">Action</th>
                      <th className="px-6 py-3 text-left text-sm font-semibold text-gray-800">Date & Time</th>
                      <th className="px-6 py-3 text-left text-sm font-semibold text-gray-800">IP Address</th>
                      <th className="px-6 py-3 text-left text-sm font-semibold text-gray-800">Device</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y">
                    {logs.map((log) => (
                      <tr key={log.id} className="hover:bg-gray-50 transition">
                        <td className="px-6 py-4">{getActionBadge(log.action)}</td>
                        <td className="px-6 py-4 text-sm text-gray-700">
                          {new Date(log.created_at).toLocaleString()}
                        </td>
                        <td className="px-6 py-4 text-sm text-gray-600 font-mono">
                          {log.ip_address || "Unknown"}
                        </td>
                        <td className="px-6 py-4 text-sm text-gray-600">
                          <span className="text-xs truncate">
                            {log.user_agent
                              ? log.user_agent.split("/")[0].split(" ")[0]
                              : "Unknown"}
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>
      </div>
    </ProtectedRoute>
  );
}
