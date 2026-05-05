"use client";

import { useAuth } from "@/src/context/AuthContext";
import { useRouter } from "next/navigation";
import { useState } from "react";
import Link from "next/link";
import { ProtectedRoute } from "@/src/components/ProtectedRoute";

export default function AdminSettingsPage() {
  const { user, logout } = useAuth();
  const router = useRouter();
  const [activeTab, setActiveTab] = useState("general");
  const [saved, setSaved] = useState(false);

  const handleLogout = async () => {
    if (confirm("Are you sure you want to logout?")) {
      await logout();
      router.push("/login");
    }
  };

  const handleSave = () => {
    setSaved(true);
    setTimeout(() => setSaved(false), 3000);
  };

  return (
    <ProtectedRoute requiredRole="admin">
      <div className="min-h-screen bg-gradient-to-br from-gray-900 to-gray-800">
        {/* Admin Navigation */}
        <nav className="bg-gray-900 border-b border-gray-700 sticky top-0 z-50 shadow-lg">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex justify-between items-center h-16">
              <div className="flex items-center gap-3">
                <span className="text-2xl">⚙️</span>
                <h1 className="text-xl font-bold text-red-500">Admin Console</h1>
              </div>
              <div className="hidden md:flex items-center gap-6">
                <Link href="/admin" className="text-gray-300 hover:text-white font-medium transition">
                  Dashboard
                </Link>
                <Link href="/admin/users" className="text-gray-300 hover:text-white font-medium transition">
                  Users
                </Link>
                <Link href="/admin/courses" className="text-gray-300 hover:text-white font-medium transition">
                  Courses
                </Link>
                <Link href="/admin/analytics" className="text-gray-300 hover:text-white font-medium transition">
                  Analytics
                </Link>
                <Link
                  href="/admin/settings"
                  className="text-white font-medium transition border-b-2 border-red-500"
                >
                  Settings
                </Link>
              </div>
              <div className="flex items-center gap-4">
                <div className="flex flex-col items-end">
                  <span className="text-sm font-semibold text-white">{user?.name}</span>
                  <span className="text-xs text-red-400 capitalize font-bold">{user?.role}</span>
                </div>
                <button
                  onClick={handleLogout}
                  className="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition"
                >
                  Logout
                </button>
              </div>
            </div>
          </div>
        </nav>

        {/* Hero Section */}
        <div className="bg-gradient-to-r from-red-600 to-red-700 text-white border-b border-red-800">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h2 className="text-4xl font-bold mb-2">⚙️ Admin Settings</h2>
            <p className="text-red-100">Configure platform settings and preferences</p>
          </div>
        </div>

        {/* Saved Notification */}
        {saved && (
          <div className="bg-green-900 border border-green-600 text-green-200 px-4 py-3 text-center sticky top-16 z-40">
            ✓ Settings saved successfully!
          </div>
        )}

        {/* Main Content */}
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {/* Sidebar - Tabs */}
            <div className="lg:col-span-1">
              <div className="bg-gray-800 border border-gray-700 rounded-lg overflow-hidden">
                {[
                  { id: "general", label: "General", icon: "⚙️" },
                  { id: "email", label: "Email Settings", icon: "📧" },
                  { id: "payment", label: "Payment", icon: "💳" },
                  { id: "security", label: "Security", icon: "🔒" },
                  { id: "notifications", label: "Notifications", icon: "🔔" },
                  { id: "backup", label: "Backup & Restore", icon: "💾" },
                ].map((tab) => (
                  <button
                    key={tab.id}
                    onClick={() => setActiveTab(tab.id)}
                    className={`w-full text-left px-4 py-3 border-b border-gray-700 transition ${
                      activeTab === tab.id
                        ? "bg-red-600 text-white font-semibold"
                        : "text-gray-300 hover:bg-gray-700"
                    }`}
                  >
                    <span>{tab.icon}</span> {tab.label}
                  </button>
                ))}
              </div>
            </div>

            {/* Content Area */}
            <div className="lg:col-span-3">
              {/* General Settings */}
              {activeTab === "general" && (
                <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
                  <h3 className="text-2xl font-bold text-white mb-6">General Settings</h3>
                  <div className="space-y-6">
                    <div>
                      <label className="block text-gray-300 text-sm font-semibold mb-2">
                        Platform Name
                      </label>
                      <input
                        type="text"
                        defaultValue="Learning Hub"
                        className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                      />
                    </div>
                    <div>
                      <label className="block text-gray-300 text-sm font-semibold mb-2">
                        Support Email
                      </label>
                      <input
                        type="email"
                        defaultValue="support@learninghub.com"
                        className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                      />
                    </div>
                    <div>
                      <label className="block text-gray-300 text-sm font-semibold mb-2">
                        Currency
                      </label>
                      <select className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500">
                        <option>USD ($)</option>
                        <option>EUR (€)</option>
                        <option>GBP (£)</option>
                      </select>
                    </div>
                    <div>
                      <label className="block text-gray-300 text-sm font-semibold mb-2">
                        Timezone
                      </label>
                      <select className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500">
                        <option>UTC</option>
                        <option>EST</option>
                        <option>PST</option>
                      </select>
                    </div>
                    <button
                      onClick={handleSave}
                      className="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded font-semibold transition"
                    >
                      Save Changes
                    </button>
                  </div>
                </div>
              )}

              {/* Email Settings */}
              {activeTab === "email" && (
                <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
                  <h3 className="text-2xl font-bold text-white mb-6">Email Settings</h3>
                  <div className="space-y-6">
                    <div>
                      <label className="block text-gray-300 text-sm font-semibold mb-2">
                        SMTP Host
                      </label>
                      <input
                        type="text"
                        defaultValue="smtp.gmail.com"
                        className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                      />
                    </div>
                    <div>
                      <label className="block text-gray-300 text-sm font-semibold mb-2">
                        SMTP Port
                      </label>
                      <input
                        type="number"
                        defaultValue="587"
                        className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                      />
                    </div>
                    <div>
                      <label className="block text-gray-300 text-sm font-semibold mb-2">
                        Email Address
                      </label>
                      <input
                        type="email"
                        defaultValue="noreply@learninghub.com"
                        className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                      />
                    </div>
                    <div>
                      <label className="block text-gray-300 text-sm font-semibold mb-2">
                        Password
                      </label>
                      <input
                        type="password"
                        placeholder="Enter password"
                        className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                      />
                    </div>
                    <button
                      onClick={handleSave}
                      className="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded font-semibold transition"
                    >
                      Save Changes
                    </button>
                  </div>
                </div>
              )}

              {/* Payment Settings */}
              {activeTab === "payment" && (
                <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
                  <h3 className="text-2xl font-bold text-white mb-6">Payment Settings</h3>
                  <div className="space-y-6">
                    <div>
                      <label className="block text-gray-300 text-sm font-semibold mb-2">
                        Stripe API Key
                      </label>
                      <input
                        type="password"
                        placeholder="sk_live_..."
                        className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                      />
                    </div>
                    <div>
                      <label className="block text-gray-300 text-sm font-semibold mb-2">
                        Stripe Public Key
                      </label>
                      <input
                        type="text"
                        placeholder="pk_live_..."
                        className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                      />
                    </div>
                    <div>
                      <label className="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" defaultChecked className="w-4 h-4" />
                        <span className="text-gray-300">Enable payment processing</span>
                      </label>
                    </div>
                    <button
                      onClick={handleSave}
                      className="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded font-semibold transition"
                    >
                      Save Changes
                    </button>
                  </div>
                </div>
              )}

              {/* Security Settings */}
              {activeTab === "security" && (
                <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
                  <h3 className="text-2xl font-bold text-white mb-6">Security Settings</h3>
                  <div className="space-y-6">
                    <div>
                      <label className="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" defaultChecked className="w-4 h-4" />
                        <span className="text-gray-300">Enable 2FA for admins</span>
                      </label>
                    </div>
                    <div>
                      <label className="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" defaultChecked className="w-4 h-4" />
                        <span className="text-gray-300">Require strong passwords</span>
                      </label>
                    </div>
                    <div>
                      <label className="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" defaultChecked className="w-4 h-4" />
                        <span className="text-gray-300">Enable activity logging</span>
                      </label>
                    </div>
                    <div>
                      <label className="block text-gray-300 text-sm font-semibold mb-2">
                        Session Timeout (minutes)
                      </label>
                      <input
                        type="number"
                        defaultValue="30"
                        className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                      />
                    </div>
                    <button
                      onClick={handleSave}
                      className="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded font-semibold transition"
                    >
                      Save Changes
                    </button>
                  </div>
                </div>
              )}

              {/* Notifications Settings */}
              {activeTab === "notifications" && (
                <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
                  <h3 className="text-2xl font-bold text-white mb-6">Notification Settings</h3>
                  <div className="space-y-4">
                    {[
                      "Send email on new user registration",
                      "Send email on new course creation",
                      "Send email on payment received",
                      "Send daily admin summary",
                      "Alert on system errors",
                      "Alert on unusual activity",
                    ].map((notification, idx) => (
                      <label key={idx} className="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" defaultChecked className="w-4 h-4" />
                        <span className="text-gray-300">{notification}</span>
                      </label>
                    ))}
                    <button
                      onClick={handleSave}
                      className="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded font-semibold transition mt-6"
                    >
                      Save Changes
                    </button>
                  </div>
                </div>
              )}

              {/* Backup & Restore */}
              {activeTab === "backup" && (
                <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
                  <h3 className="text-2xl font-bold text-white mb-6">Backup & Restore</h3>
                  <div className="space-y-6">
                    <div className="bg-gray-700 p-4 rounded">
                      <p className="text-gray-300 font-semibold mb-2">Last Backup</p>
                      <p className="text-gray-400">May 2, 2026 at 02:30 AM</p>
                    </div>
                    <div className="space-y-2">
                      <button className="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold transition">
                        Create Backup Now
                      </button>
                      <button className="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded font-semibold transition">
                        Download Backup
                      </button>
                      <button className="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded font-semibold transition">
                        Restore from Backup
                      </button>
                    </div>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </ProtectedRoute>
  );
}
