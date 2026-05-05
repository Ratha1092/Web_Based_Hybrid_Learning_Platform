"use client";

import { useAuth } from "@/src/context/AuthContext";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import Link from "next/link";
import { ProtectedRoute } from "@/src/components/ProtectedRoute";

interface DashboardStats {
  totalUsers: number;
  totalCourses: number;
  totalRevenue: number;
  activeSessions: number;
  recentUserGrowth: number;
  courseCompletion: number;
}

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  status: "active" | "inactive" | "suspended";
  joinedDate: string;
}

interface RecentActivity {
  id: number;
  action: string;
  user: string;
  timestamp: string;
  details: string;
}

export default function AdminDashboard() {
  const { user, logout } = useAuth();
  const router = useRouter();
  const [stats, setStats] = useState<DashboardStats>({
    totalUsers: 1247,
    totalCourses: 45,
    totalRevenue: 125750,
    activeSessions: 284,
    recentUserGrowth: 12.5,
    courseCompletion: 68.3,
  });

  const recentUsers: User[] = [
    {
      id: 1,
      name: "Alice Johnson",
      email: "alice@example.com",
      role: "student",
      status: "active",
      joinedDate: "May 1, 2026",
    },
    {
      id: 2,
      name: "Bob Smith",
      email: "bob@example.com",
      role: "instructor",
      status: "active",
      joinedDate: "April 30, 2026",
    },
    {
      id: 3,
      name: "Carol White",
      email: "carol@example.com",
      role: "student",
      status: "active",
      joinedDate: "April 29, 2026",
    },
    {
      id: 4,
      name: "David Brown",
      email: "david@example.com",
      role: "student",
      status: "inactive",
      joinedDate: "April 28, 2026",
    },
  ];

  const recentActivities: RecentActivity[] = [
    {
      id: 1,
      action: "User Registration",
      user: "Alice Johnson",
      timestamp: "2 hours ago",
      details: "New student account created",
    },
    {
      id: 2,
      action: "Course Created",
      user: "Bob Smith",
      timestamp: "4 hours ago",
      details: "New course 'Advanced React' published",
    },
    {
      id: 3,
      action: "Payment Received",
      user: "Carol White",
      timestamp: "6 hours ago",
      details: "$99.99 payment for 'Web Development Course'",
    },
    {
      id: 4,
      action: "User Suspended",
      user: "System",
      timestamp: "8 hours ago",
      details: "Account suspended due to violation",
    },
    {
      id: 5,
      action: "Course Completed",
      user: "David Brown",
      timestamp: "10 hours ago",
      details: "Completed 'Python Basics' course",
    },
  ];

  const handleLogout = async () => {
    if (confirm("Are you sure you want to logout?")) {
      await logout();
      router.push("/login");
    }
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
                <Link
                  href="/admin"
                  className="text-gray-300 hover:text-white font-medium transition"
                >
                  Dashboard
                </Link>
                <Link
                  href="/admin/users"
                  className="text-gray-300 hover:text-white font-medium transition"
                >
                  Users
                </Link>
                <Link
                  href="/admin/courses"
                  className="text-gray-300 hover:text-white font-medium transition"
                >
                  Courses
                </Link>
                <Link
                  href="/admin/analytics"
                  className="text-gray-300 hover:text-white font-medium transition"
                >
                  Analytics
                </Link>
                <Link
                  href="/admin/settings"
                  className="text-gray-300 hover:text-white font-medium transition"
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
            <h2 className="text-4xl font-bold mb-2">📊 Admin Dashboard</h2>
            <p className="text-red-100">
              Welcome back, <strong>{user?.name}</strong>! Here's your platform overview.
            </p>
          </div>
        </div>

        {/* Main Content */}
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* Key Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {/* Total Users */}
            <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 hover:border-blue-500 transition">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-400 text-sm font-medium">Total Users</p>
                  <p className="text-4xl font-bold text-white mt-2">{stats.totalUsers.toLocaleString()}</p>
                  <p className="text-xs text-green-400 mt-2">
                    ↑ {stats.recentUserGrowth}% this month
                  </p>
                </div>
                <div className="text-5xl opacity-20">👥</div>
              </div>
            </div>

            {/* Total Courses */}
            <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 hover:border-green-500 transition">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-400 text-sm font-medium">Total Courses</p>
                  <p className="text-4xl font-bold text-white mt-2">{stats.totalCourses}</p>
                  <p className="text-xs text-green-400 mt-2">Completion: {stats.courseCompletion}%</p>
                </div>
                <div className="text-5xl opacity-20">📚</div>
              </div>
            </div>

            {/* Total Revenue */}
            <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 hover:border-yellow-500 transition">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-400 text-sm font-medium">Total Revenue</p>
                  <p className="text-4xl font-bold text-white mt-2">
                    ${(stats.totalRevenue / 1000).toFixed(1)}K
                  </p>
                  <p className="text-xs text-green-400 mt-2">↑ 8.2% increase</p>
                </div>
                <div className="text-5xl opacity-20">💰</div>
              </div>
            </div>

            {/* Active Sessions */}
            <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 hover:border-purple-500 transition">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-400 text-sm font-medium">Active Sessions</p>
                  <p className="text-4xl font-bold text-white mt-2">{stats.activeSessions}</p>
                  <p className="text-xs text-blue-400 mt-2">Online now</p>
                </div>
                <div className="text-5xl opacity-20">🔴</div>
              </div>
            </div>

            {/* System Health */}
            <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 hover:border-green-500 transition">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-400 text-sm font-medium">System Health</p>
                  <p className="text-4xl font-bold text-green-400 mt-2">99.9%</p>
                  <p className="text-xs text-green-400 mt-2">Uptime</p>
                </div>
                <div className="text-5xl opacity-20">✅</div>
              </div>
            </div>

            {/* Server Load */}
            <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 hover:border-orange-500 transition">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-400 text-sm font-medium">Server Load</p>
                  <p className="text-4xl font-bold text-orange-400 mt-2">42%</p>
                  <p className="text-xs text-orange-400 mt-2">CPU usage</p>
                </div>
                <div className="text-5xl opacity-20">⚡</div>
              </div>
            </div>
          </div>

          {/* Content Grid */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Main Content */}
            <div className="lg:col-span-2 space-y-8">
              {/* Recent Users */}
              <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
                <div className="flex justify-between items-center mb-6">
                  <h3 className="text-2xl font-bold text-white">👥 Recent Users</h3>
                  <Link
                    href="/admin/users"
                    className="text-red-500 hover:text-red-400 font-semibold text-sm"
                  >
                    View All →
                  </Link>
                </div>

                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead>
                      <tr className="border-b border-gray-700">
                        <th className="text-left py-3 px-4 text-gray-400 font-semibold">Name</th>
                        <th className="text-left py-3 px-4 text-gray-400 font-semibold">Email</th>
                        <th className="text-left py-3 px-4 text-gray-400 font-semibold">Role</th>
                        <th className="text-left py-3 px-4 text-gray-400 font-semibold">Status</th>
                        <th className="text-left py-3 px-4 text-gray-400 font-semibold">Joined</th>
                        <th className="text-left py-3 px-4 text-gray-400 font-semibold">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      {recentUsers.map((u) => (
                        <tr key={u.id} className="border-b border-gray-700 hover:bg-gray-700 transition">
                          <td className="py-3 px-4 text-white font-medium">{u.name}</td>
                          <td className="py-3 px-4 text-gray-400 text-sm">{u.email}</td>
                          <td className="py-3 px-4">
                            <span
                              className={`px-2 py-1 rounded text-xs font-semibold ${
                                u.role === "admin"
                                  ? "bg-red-900 text-red-200"
                                  : u.role === "instructor"
                                  ? "bg-blue-900 text-blue-200"
                                  : "bg-gray-700 text-gray-200"
                              }`}
                            >
                              {u.role.charAt(0).toUpperCase() + u.role.slice(1)}
                            </span>
                          </td>
                          <td className="py-3 px-4">
                            <span
                              className={`px-2 py-1 rounded text-xs font-semibold ${
                                u.status === "active"
                                  ? "bg-green-900 text-green-200"
                                  : u.status === "inactive"
                                  ? "bg-yellow-900 text-yellow-200"
                                  : "bg-red-900 text-red-200"
                              }`}
                            >
                              {u.status.charAt(0).toUpperCase() + u.status.slice(1)}
                            </span>
                          </td>
                          <td className="py-3 px-4 text-gray-400 text-sm">{u.joinedDate}</td>
                          <td className="py-3 px-4">
                            <button className="text-blue-400 hover:text-blue-300 text-sm font-semibold transition">
                              View
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>

              {/* Recent Activities */}
              <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
                <div className="flex justify-between items-center mb-6">
                  <h3 className="text-2xl font-bold text-white">📝 Recent Activities</h3>
                  <Link
                    href="/admin/analytics"
                    className="text-red-500 hover:text-red-400 font-semibold text-sm"
                  >
                    View All →
                  </Link>
                </div>

                <div className="space-y-3">
                  {recentActivities.map((activity) => (
                    <div
                      key={activity.id}
                      className="bg-gray-700 p-4 rounded-lg hover:bg-gray-600 transition border-l-4 border-red-500"
                    >
                      <div className="flex justify-between items-start">
                        <div>
                          <p className="font-semibold text-white">{activity.action}</p>
                          <p className="text-sm text-gray-300 mt-1">{activity.details}</p>
                          <p className="text-xs text-gray-500 mt-2">By: {activity.user}</p>
                        </div>
                        <span className="text-xs text-gray-400">{activity.timestamp}</span>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* Sidebar */}
            <div className="space-y-8">
              {/* Quick Actions */}
              <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
                <h3 className="text-xl font-bold text-white mb-4">⚡ Quick Actions</h3>
                <div className="space-y-2">
                  <Link
                    href="/admin/users"
                    className="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold text-center transition"
                  >
                    Manage Users
                  </Link>
                  <Link
                    href="/admin/courses"
                    className="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold text-center transition"
                  >
                    Manage Courses
                  </Link>
                  <Link
                    href="/admin/analytics"
                    className="block w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded font-semibold text-center transition"
                  >
                    View Analytics
                  </Link>
                  <Link
                    href="/admin/settings"
                    className="block w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded font-semibold text-center transition"
                  >
                    Settings
                  </Link>
                </div>
              </div>

              {/* System Status */}
              <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6">
                <h3 className="text-xl font-bold text-white mb-4">🟢 System Status</h3>
                <div className="space-y-3">
                  <div className="flex justify-between items-center">
                    <span className="text-gray-400">Database</span>
                    <span className="text-green-400 font-semibold">Connected ✓</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span className="text-gray-400">Cache</span>
                    <span className="text-green-400 font-semibold">Active ✓</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span className="text-gray-400">Email Service</span>
                    <span className="text-green-400 font-semibold">Ready ✓</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span className="text-gray-400">Storage</span>
                    <span className="text-yellow-400 font-semibold">78% Full</span>
                  </div>
                </div>
              </div>

              {/* Alerts */}
              <div className="bg-gray-800 border border-yellow-600 rounded-lg shadow-lg p-6">
                <h3 className="text-xl font-bold text-white mb-4">⚠️ Alerts</h3>
                <div className="space-y-2">
                  <div className="bg-yellow-900 bg-opacity-30 border border-yellow-600 p-3 rounded">
                    <p className="text-yellow-300 text-sm font-semibold">Storage Alert</p>
                    <p className="text-yellow-200 text-xs mt-1">Storage is 78% full</p>
                  </div>
                  <div className="bg-blue-900 bg-opacity-30 border border-blue-600 p-3 rounded">
                    <p className="text-blue-300 text-sm font-semibold">Maintenance Due</p>
                    <p className="text-blue-200 text-xs mt-1">Database optimization recommended</p>
                  </div>
                </div>
              </div>

              {/* Admin Info */}
              <div className="bg-gray-800 border border-red-700 rounded-lg shadow-lg p-6">
                <h3 className="text-xl font-bold text-white mb-4">👤 Admin Info</h3>
                <div className="space-y-3 text-sm">
                  <div>
                    <p className="text-gray-400">Name</p>
                    <p className="font-semibold text-white">{user?.name}</p>
                  </div>
                  <div>
                    <p className="text-gray-400">Email</p>
                    <p className="font-semibold text-white">{user?.email}</p>
                  </div>
                  <div>
                    <p className="text-gray-400">Role</p>
                    <p className="font-semibold text-red-400 uppercase">{user?.role}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Footer */}
        <footer className="bg-gray-900 border-t border-gray-700 mt-12 text-gray-400">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
              <div>
                <h4 className="text-white font-bold mb-4">Admin</h4>
                <ul className="space-y-2 text-sm">
                  <li>
                    <Link href="/admin" className="hover:text-white transition">
                      Dashboard
                    </Link>
                  </li>
                  <li>
                    <Link href="/admin/users" className="hover:text-white transition">
                      Users
                    </Link>
                  </li>
                  <li>
                    <Link href="/admin/courses" className="hover:text-white transition">
                      Courses
                    </Link>
                  </li>
                </ul>
              </div>
              <div>
                <h4 className="text-white font-bold mb-4">Management</h4>
                <ul className="space-y-2 text-sm">
                  <li>
                    <Link href="/admin/analytics" className="hover:text-white transition">
                      Analytics
                    </Link>
                  </li>
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Reports
                    </Link>
                  </li>
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Billing
                    </Link>
                  </li>
                </ul>
              </div>
              <div>
                <h4 className="text-white font-bold mb-4">Settings</h4>
                <ul className="space-y-2 text-sm">
                  <li>
                    <Link href="/admin/settings" className="hover:text-white transition">
                      Admin Settings
                    </Link>
                  </li>
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      System Config
                    </Link>
                  </li>
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Logs
                    </Link>
                  </li>
                </ul>
              </div>
              <div>
                <h4 className="text-white font-bold mb-4">Support</h4>
                <ul className="space-y-2 text-sm">
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Documentation
                    </Link>
                  </li>
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Support Ticket
                    </Link>
                  </li>
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      API Docs
                    </Link>
                  </li>
                </ul>
              </div>
            </div>
            <div className="border-t border-gray-700 pt-8">
              <div className="flex justify-between items-center">
                <p className="text-sm">© 2026 Admin Console. All rights reserved.</p>
                <div className="flex gap-4 text-sm">
                  <Link href="#" className="hover:text-white transition">
                    Privacy
                  </Link>
                  <Link href="#" className="hover:text-white transition">
                    Terms
                  </Link>
                  <Link href="#" className="hover:text-white transition">
                    Contact
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </ProtectedRoute>
  );
}
