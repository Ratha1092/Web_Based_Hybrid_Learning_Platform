"use client";

import { useAuth } from "@/src/context/AuthContext";
import { useRouter } from "next/navigation";
import { useEffect } from "react";
import Link from "next/link";
import { ProtectedRoute } from "@/src/components/ProtectedRoute";

export default function DashboardPage() {
  const { user, logout, isAuthenticated, loading } = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (!loading && !isAuthenticated) {
      router.push("/login");
    }
  }, [isAuthenticated, loading, router]);

  const handleLogout = async () => {
    if (confirm("Are you sure you want to logout?")) {
      await logout();
      router.push("/login");
    }
  };

  if (loading) {
    return (
      <div className="flex h-screen items-center justify-center">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
          <p className="mt-4 text-gray-600">Loading...</p>
        </div>
      </div>
    );
  }

  return (
    <ProtectedRoute>
      <div className="min-h-screen bg-gray-50">
        {/* Navigation */}
        <nav className="bg-white shadow">
          <div className="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 className="text-2xl font-bold text-indigo-600">Dashboard</h1>
            <div className="flex items-center gap-4">
              <span className="text-gray-700">Welcome, <strong>{user?.name}</strong>!</span>
              <button
                onClick={handleLogout}
                className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition"
              >
                Logout
              </button>
            </div>
          </div>
        </nav>

        {/* Main Content */}
        <div className="max-w-7xl mx-auto px-4 py-8">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {/* User Info Card */}
            <div className="bg-white rounded-lg shadow p-6">
              <h2 className="text-lg font-semibold text-gray-800 mb-4">Profile</h2>
              <div className="space-y-2 text-gray-600">
                <p><strong>Name:</strong> {user?.name}</p>
                <p><strong>Email:</strong> {user?.email}</p>
                <p><strong>Role:</strong> <span className="capitalize bg-indigo-100 text-indigo-800 px-2 py-1 rounded">{user?.role}</span></p>
              </div>
            </div>

            {/* Quick Stats */}
            <div className="bg-white rounded-lg shadow p-6">
              <h2 className="text-lg font-semibold text-gray-800 mb-4">Quick Stats</h2>
              <div className="space-y-2">
                <p className="text-sm text-gray-600">Courses Enrolled: <strong>0</strong></p>
                <p className="text-sm text-gray-600">Completed: <strong>0</strong></p>
                <p className="text-sm text-gray-600">In Progress: <strong>0</strong></p>
              </div>
            </div>

            {/* Settings Card */}
            <div className="bg-white rounded-lg shadow p-6">
              <h2 className="text-lg font-semibold text-gray-800 mb-4">Account</h2>
              <Link
                href="/security-settings"
                className="block w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition mb-2 text-center font-semibold"
              >
                🔒 Security Settings
              </Link>
              <Link
                href="/change-password"
                className="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded transition text-center font-semibold"
              >
                🔑 Change Password
              </Link>
            </div>
          </div>

          {/* Additional Features Grid */}
          <div className="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {/* Email Verification */}
            <Link
              href="/security-settings"
              className="bg-white rounded-lg shadow p-6 hover:shadow-lg transition transform hover:-translate-y-1"
            >
              <div className="text-3xl mb-2">📧</div>
              <h3 className="font-semibold text-gray-800 mb-1">Email Verification</h3>
              <p className="text-sm text-gray-600">Verify and manage your email</p>
            </Link>

            {/* 2FA Setup */}
            <Link
              href="/2fa-setup"
              className="bg-white rounded-lg shadow p-6 hover:shadow-lg transition transform hover:-translate-y-1"
            >
              <div className="text-3xl mb-2">🔐</div>
              <h3 className="font-semibold text-gray-800 mb-1">2FA Setup</h3>
              <p className="text-sm text-gray-600">Enable two-factor authentication</p>
            </Link>

            {/* Password Reset */}
            <Link
              href="/forgot-password"
              className="bg-white rounded-lg shadow p-6 hover:shadow-lg transition transform hover:-translate-y-1"
            >
              <div className="text-3xl mb-2">🔄</div>
              <h3 className="font-semibold text-gray-800 mb-1">Password Reset</h3>
              <p className="text-sm text-gray-600">Reset your password securely</p>
            </Link>

            {/* Activity History */}
            <Link
              href="/activity-history"
              className="bg-white rounded-lg shadow p-6 hover:shadow-lg transition transform hover:-translate-y-1"
            >
              <div className="text-3xl mb-2">📊</div>
              <h3 className="font-semibold text-gray-800 mb-1">Activity History</h3>
              <p className="text-sm text-gray-600">View your login activity & logs</p>
            </Link>
          </div>
        </div>
      </div>
    </ProtectedRoute>
  );
}
