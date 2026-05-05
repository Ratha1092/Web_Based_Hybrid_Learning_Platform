"use client";

import { useAuth } from "@/src/context/AuthContext";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { ProtectedRoute } from "@/src/components/ProtectedRoute";

export default function AdminAnalyticsPage() {
  const { user, logout } = useAuth();
  const router = useRouter();

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
                <Link href="/admin" className="text-gray-300 hover:text-white font-medium transition">
                  Dashboard
                </Link>
                <Link href="/admin/users" className="text-gray-300 hover:text-white font-medium transition">
                  Users
                </Link>
                <Link href="/admin/courses" className="text-gray-300 hover:text-white font-medium transition">
                  Courses
                </Link>
                <Link
                  href="/admin/analytics"
                  className="text-white font-medium transition border-b-2 border-red-500"
                >
                  Analytics
                </Link>
                <Link href="/admin/settings" className="text-gray-300 hover:text-white font-medium transition">
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
            <h2 className="text-4xl font-bold mb-2">📊 Platform Analytics</h2>
            <p className="text-red-100">Track performance metrics and user insights</p>
          </div>
        </div>

        {/* Main Content */}
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* Key Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
              <p className="text-gray-400 text-sm">Monthly Active Users</p>
              <p className="text-4xl font-bold text-white mt-2">4,562</p>
              <p className="text-xs text-green-400 mt-2">↑ 12.5% from last month</p>
            </div>
            <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
              <p className="text-gray-400 text-sm">Total Enrollments</p>
              <p className="text-4xl font-bold text-white mt-2">8,234</p>
              <p className="text-xs text-green-400 mt-2">↑ 8.2% from last month</p>
            </div>
            <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
              <p className="text-gray-400 text-sm">Course Completion Rate</p>
              <p className="text-4xl font-bold text-white mt-2">68.3%</p>
              <p className="text-xs text-green-400 mt-2">↑ 2.1% from last month</p>
            </div>
            <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
              <p className="text-gray-400 text-sm">Monthly Revenue</p>
              <p className="text-4xl font-bold text-white mt-2">$45.2K</p>
              <p className="text-xs text-green-400 mt-2">↑ 15.3% from last month</p>
            </div>
          </div>

          {/* Analytics Charts - Placeholder */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            {/* User Growth Chart */}
            <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
              <h3 className="text-xl font-bold text-white mb-6">User Growth (Last 6 Months)</h3>
              <div className="bg-gray-700 rounded p-8 text-center">
                <p className="text-gray-400">📈 Chart visualization would render here</p>
                <p className="text-sm text-gray-500 mt-2">Data: 1.2K → 2.1K → 3.4K → 3.8K → 4.2K → 4.562K</p>
              </div>
            </div>

            {/* Revenue Chart */}
            <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
              <h3 className="text-xl font-bold text-white mb-6">Monthly Revenue</h3>
              <div className="bg-gray-700 rounded p-8 text-center">
                <p className="text-gray-400">💰 Chart visualization would render here</p>
                <p className="text-sm text-gray-500 mt-2">Data: $12K → $18K → $25K → $35K → $40K → $45.2K</p>
              </div>
            </div>
          </div>

          {/* Course Performance */}
          <div className="bg-gray-800 border border-gray-700 rounded-lg p-6 mb-8">
            <h3 className="text-xl font-bold text-white mb-6">Top Performing Courses</h3>
            <div className="space-y-4">
              {[
                { title: "Web Development", students: 1250, revenue: 12500, rating: 4.8 },
                { title: "Advanced React", students: 680, revenue: 6800, rating: 4.9 },
                { title: "Full Stack Dev", students: 920, revenue: 9200, rating: 4.7 },
              ].map((course, idx) => (
                <div key={idx} className="bg-gray-700 p-4 rounded flex justify-between items-center">
                  <div>
                    <p className="font-semibold text-white">{course.title}</p>
                    <p className="text-sm text-gray-400">{course.students} students enrolled</p>
                  </div>
                  <div className="text-right">
                    <p className="text-green-400 font-bold">${course.revenue.toLocaleString()}</p>
                    <p className="text-yellow-400 text-sm">⭐ {course.rating}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* User Demographics */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
              <h3 className="text-xl font-bold text-white mb-6">User Distribution</h3>
              <div className="space-y-3">
                {[
                  { role: "Students", count: 3850, percentage: 76 },
                  { role: "Instructors", count: 420, percentage: 18 },
                  { role: "Admins", count: 60, percentage: 6 },
                ].map((item, idx) => (
                  <div key={idx}>
                    <div className="flex justify-between mb-1">
                      <span className="text-gray-300">{item.role}</span>
                      <span className="text-white font-semibold">{item.count} ({item.percentage}%)</span>
                    </div>
                    <div className="w-full bg-gray-700 rounded-full h-2">
                      <div
                        className="bg-gradient-to-r from-red-600 to-red-500 h-2 rounded-full"
                        style={{ width: `${item.percentage}%` }}
                      ></div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
              <h3 className="text-xl font-bold text-white mb-6">Engagement Metrics</h3>
              <div className="space-y-3">
                {[
                  { metric: "Daily Active Users", value: "1,234" },
                  { metric: "Average Session Duration", value: "28 min" },
                  { metric: "Course Completion Rate", value: "68.3%" },
                  { metric: "Return Rate (7 days)", value: "62.5%" },
                ].map((item, idx) => (
                  <div key={idx} className="bg-gray-700 p-3 rounded flex justify-between">
                    <span className="text-gray-300">{item.metric}</span>
                    <span className="text-white font-bold">{item.value}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </ProtectedRoute>
  );
}
