"use client";

import { useAuth } from "@/src/context/AuthContext";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import Link from "next/link";
import { ProtectedRoute } from "@/src/components/ProtectedRoute";

interface Course {
  id: number;
  title: string;
  progress: number;
  students: number;
  instructor: string;
  image: string;
}

interface Announcement {
  id: number;
  title: string;
  content: string;
  date: string;
}

export default function HomePage() {
  const { user, logout, isAuthenticated, loading } = useAuth();
  const router = useRouter();
  const [stats, setStats] = useState({
    coursesEnrolled: 5,
    hoursSpent: 42,
    assignmentsPending: 3,
    certificatesEarned: 2,
  });

  const enrolledCourses: Course[] = [
    {
      id: 1,
      title: "Introduction to Web Development",
      progress: 75,
      students: 1250,
      instructor: "John Doe",
      image: "🌐",
    },
    {
      id: 2,
      title: "Advanced React Patterns",
      progress: 45,
      students: 680,
      instructor: "Jane Smith",
      image: "⚛️",
    },
    {
      id: 3,
      title: "Full Stack Development",
      progress: 60,
      students: 920,
      instructor: "Mike Johnson",
      image: "💻",
    },
    {
      id: 4,
      title: "Database Design & SQL",
      progress: 90,
      students: 540,
      instructor: "Sarah Williams",
      image: "🗄️",
    },
  ];

  const announcements: Announcement[] = [
    {
      id: 1,
      title: "New Course Available: Machine Learning Basics",
      content:
        "A comprehensive course on machine learning fundamentals has been added to our platform.",
      date: "May 2, 2026",
    },
    {
      id: 2,
      title: "Maintenance Schedule",
      content:
        "System maintenance scheduled for May 5-6. Platform will be unavailable during this time.",
      date: "April 30, 2026",
    },
    {
      id: 3,
      title: "Certificate Programs Now Available",
      content:
        "Earn recognized certificates upon completion of selected course tracks.",
      date: "April 28, 2026",
    },
  ];

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
      <div className="flex h-screen items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
          <p className="mt-4 text-gray-600">Loading...</p>
        </div>
      </div>
    );
  }

  return (
    <ProtectedRoute>
      <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        {/* Navigation */}
        <nav className="bg-white shadow-sm sticky top-0 z-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex justify-between items-center h-16">
              <div className="flex items-center gap-3">
                <span className="text-2xl">🎓</span>
                <h1 className="text-xl font-bold text-indigo-600">Learning Hub</h1>
              </div>
              <div className="hidden md:flex items-center gap-6">
                <Link href="/home" className="text-gray-700 hover:text-indigo-600 font-medium">
                  Home
                </Link>
                <Link href="/courses" className="text-gray-700 hover:text-indigo-600 font-medium">
                  Courses
                </Link>
                <Link href="/activity-history" className="text-gray-700 hover:text-indigo-600 font-medium">
                  Activity
                </Link>
                <Link href="/security-settings" className="text-gray-700 hover:text-indigo-600 font-medium">
                  Settings
                </Link>
                {user?.role === "admin" && (
                  <Link href="/admin" className="text-red-600 hover:text-red-700 font-bold">
                    Admin
                  </Link>
                )}
              </div>
              <div className="flex items-center gap-4">
                <div className="flex flex-col items-end">
                  <span className="text-sm font-semibold text-gray-800">{user?.name}</span>
                  <span className="text-xs text-gray-500 capitalize">{user?.role}</span>
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
        <div className="bg-gradient-to-r from-indigo-600 to-blue-600 text-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
              <div>
                <h2 className="text-4xl font-bold mb-4">Welcome Back, {user?.name}! 👋</h2>
                <p className="text-lg opacity-90 mb-6">
                  Continue learning and growing with our comprehensive course platform. You're{" "}
                  <strong>making great progress</strong> on your learning journey!
                </p>
                <div className="flex gap-4">
                  <Link
                    href="/courses"
                    className="bg-white text-indigo-600 px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transition"
                  >
                    Browse Courses
                  </Link>
                  <Link
                    href="/security-settings"
                    className="bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-800 transition"
                  >
                    Settings
                  </Link>
                </div>
              </div>
              <div className="text-6xl text-center">📚</div>
            </div>
          </div>
        </div>

        {/* Main Content */}
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* Stats Dashboard */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-600">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm font-medium">Courses Enrolled</p>
                  <p className="text-3xl font-bold text-gray-800 mt-1">{stats.coursesEnrolled}</p>
                </div>
                <div className="text-4xl">📚</div>
              </div>
              <p className="text-xs text-gray-500 mt-3">Active learning tracks</p>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm font-medium">Hours Spent</p>
                  <p className="text-3xl font-bold text-gray-800 mt-1">{stats.hoursSpent}h</p>
                </div>
                <div className="text-4xl">⏱️</div>
              </div>
              <p className="text-xs text-gray-500 mt-3">This month</p>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-600">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm font-medium">Pending</p>
                  <p className="text-3xl font-bold text-gray-800 mt-1">{stats.assignmentsPending}</p>
                </div>
                <div className="text-4xl">📝</div>
              </div>
              <p className="text-xs text-gray-500 mt-3">Assignments to submit</p>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-600">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm font-medium">Certificates</p>
                  <p className="text-3xl font-bold text-gray-800 mt-1">{stats.certificatesEarned}</p>
                </div>
                <div className="text-4xl">🏆</div>
              </div>
              <p className="text-xs text-gray-500 mt-3">Earned certificates</p>
            </div>
          </div>

          {/* Sections Container */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Left Column - Main Content */}
            <div className="lg:col-span-2 space-y-8">
              {/* Your Courses Section */}
              <div className="bg-white rounded-lg shadow-md p-6">
                <div className="flex justify-between items-center mb-6">
                  <h3 className="text-2xl font-bold text-gray-800">Your Courses</h3>
                  <Link href="/courses" className="text-indigo-600 hover:text-indigo-700 font-semibold text-sm">
                    View All →
                  </Link>
                </div>

                <div className="space-y-4">
                  {enrolledCourses.map((course) => (
                    <div
                      key={course.id}
                      className="border border-gray-200 rounded-lg p-4 hover:shadow-md transition"
                    >
                      <div className="flex items-start justify-between mb-3">
                        <div className="flex items-start gap-3">
                          <span className="text-3xl">{course.image}</span>
                          <div>
                            <h4 className="font-semibold text-gray-800 text-lg">{course.title}</h4>
                            <p className="text-sm text-gray-600">by {course.instructor}</p>
                          </div>
                        </div>
                        <span className="text-lg font-bold text-indigo-600">{course.progress}%</span>
                      </div>

                      {/* Progress Bar */}
                      <div className="w-full bg-gray-200 rounded-full h-2 mb-3">
                        <div
                          className="bg-gradient-to-r from-indigo-600 to-blue-600 h-2 rounded-full transition-all duration-300"
                          style={{ width: `${course.progress}%` }}
                        ></div>
                      </div>

                      <div className="flex justify-between items-center text-sm">
                        <span className="text-gray-600">{course.students.toLocaleString()} students</span>
                        <button className="text-indigo-600 hover:text-indigo-700 font-semibold">
                          Continue Learning →
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Recent Activity Section */}
              <div className="bg-white rounded-lg shadow-md p-6">
                <div className="flex justify-between items-center mb-6">
                  <h3 className="text-2xl font-bold text-gray-800">Recent Activity</h3>
                  <Link href="/activity-history" className="text-indigo-600 hover:text-indigo-700 font-semibold text-sm">
                    View All →
                  </Link>
                </div>

                <div className="space-y-3">
                  <div className="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <span className="text-xl">✅</span>
                    <div>
                      <p className="font-medium text-gray-800">Completed "React Hooks" Module</p>
                      <p className="text-sm text-gray-600">2 hours ago</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <span className="text-xl">📝</span>
                    <div>
                      <p className="font-medium text-gray-800">Submitted "Portfolio Project"</p>
                      <p className="text-sm text-gray-600">5 hours ago</p>
                    </div>
                  </div>
                  <div className="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <span className="text-xl">🏆</span>
                    <div>
                      <p className="font-medium text-gray-800">Earned "Week Warrior" Badge</p>
                      <p className="text-sm text-gray-600">1 day ago</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Right Column - Sidebar */}
            <div className="space-y-8">
              {/* Quick Actions */}
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-xl font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div className="space-y-2">
                  <Link
                    href="/security-settings"
                    className="block w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded font-semibold text-center transition"
                  >
                    🔒 Security
                  </Link>
                  <Link
                    href="/activity-history"
                    className="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold text-center transition"
                  >
                    📊 Activity
                  </Link>
                  <Link
                    href="/forgot-password"
                    className="block w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded font-semibold text-center transition"
                  >
                    🔑 Password Reset
                  </Link>
                </div>
              </div>

              {/* Announcements */}
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-xl font-bold text-gray-800 mb-4">📢 Announcements</h3>
                <div className="space-y-4">
                  {announcements.map((announcement) => (
                    <div key={announcement.id} className="border-l-4 border-indigo-600 pl-3 py-2">
                      <h4 className="font-semibold text-gray-800 text-sm">{announcement.title}</h4>
                      <p className="text-xs text-gray-600 mt-1">{announcement.content}</p>
                      <p className="text-xs text-gray-500 mt-2">{announcement.date}</p>
                    </div>
                  ))}
                </div>
              </div>

              {/* Profile Card */}
              <div className="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg shadow-md p-6 border border-indigo-200">
                <h3 className="text-lg font-bold text-gray-800 mb-4">👤 Profile</h3>
                <div className="space-y-3 text-sm">
                  <div>
                    <p className="text-gray-600">Email</p>
                    <p className="font-semibold text-gray-800">{user?.email}</p>
                  </div>
                  <div>
                    <p className="text-gray-600">Role</p>
                    <p className="font-semibold text-gray-800 capitalize">{user?.role}</p>
                  </div>
                  <div>
                    <p className="text-gray-600">Member Since</p>
                    <p className="font-semibold text-gray-800">May 2026</p>
                  </div>
                </div>
                <Link
                  href="/security-settings"
                  className="block w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded font-semibold text-center transition mt-4"
                >
                  Manage Account
                </Link>
              </div>
            </div>
          </div>
        </div>

        {/* Footer */}
        <footer className="bg-gray-800 text-gray-300 mt-12">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
              <div>
                <h4 className="text-white font-bold mb-4">Platform</h4>
                <ul className="space-y-2 text-sm">
                  <li>
                    <Link href="/home" className="hover:text-white transition">
                      Home
                    </Link>
                  </li>
                  <li>
                    <Link href="/courses" className="hover:text-white transition">
                      Courses
                    </Link>
                  </li>
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Instructors
                    </Link>
                  </li>
                </ul>
              </div>
              <div>
                <h4 className="text-white font-bold mb-4">Learning</h4>
                <ul className="space-y-2 text-sm">
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Browse Courses
                    </Link>
                  </li>
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Certificates
                    </Link>
                  </li>
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Paths
                    </Link>
                  </li>
                </ul>
              </div>
              <div>
                <h4 className="text-white font-bold mb-4">Account</h4>
                <ul className="space-y-2 text-sm">
                  <li>
                    <Link href="/security-settings" className="hover:text-white transition">
                      Settings
                    </Link>
                  </li>
                  <li>
                    <Link href="/activity-history" className="hover:text-white transition">
                      Activity
                    </Link>
                  </li>
                  <li>
                    <button onClick={handleLogout} className="hover:text-white transition">
                      Logout
                    </button>
                  </li>
                </ul>
              </div>
              <div>
                <h4 className="text-white font-bold mb-4">Support</h4>
                <ul className="space-y-2 text-sm">
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Help Center
                    </Link>
                  </li>
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Contact Us
                    </Link>
                  </li>
                  <li>
                    <Link href="#" className="hover:text-white transition">
                      Feedback
                    </Link>
                  </li>
                </ul>
              </div>
            </div>
            <div className="border-t border-gray-700 pt-8">
              <div className="flex justify-between items-center">
                <p className="text-sm">© 2026 Learning Hub. All rights reserved.</p>
                <div className="flex gap-4 text-sm">
                  <Link href="#" className="hover:text-white transition">
                    Privacy
                  </Link>
                  <Link href="#" className="hover:text-white transition">
                    Terms
                  </Link>
                  <Link href="#" className="hover:text-white transition">
                    Cookies
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
