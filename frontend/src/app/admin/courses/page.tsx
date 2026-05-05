"use client";

import { useAuth } from "@/src/context/AuthContext";
import { useRouter } from "next/navigation";
import { useState } from "react";
import Link from "next/link";
import { ProtectedRoute } from "@/src/components/ProtectedRoute";

interface Course {
  id: number;
  title: string;
  instructor: string;
  students: number;
  rating: number;
  status: "active" | "draft" | "archived";
  createdDate: string;
  revenue: number;
}

export default function AdminCoursesPage() {
  const { user, logout } = useAuth();
  const router = useRouter();
  const [searchTerm, setSearchTerm] = useState("");
  const [filterStatus, setFilterStatus] = useState("all");

  const courses: Course[] = [
    {
      id: 1,
      title: "Introduction to Web Development",
      instructor: "John Doe",
      students: 1250,
      rating: 4.8,
      status: "active",
      createdDate: "March 15, 2026",
      revenue: 12500,
    },
    {
      id: 2,
      title: "Advanced React Patterns",
      instructor: "Jane Smith",
      students: 680,
      rating: 4.9,
      status: "active",
      createdDate: "February 10, 2026",
      revenue: 6800,
    },
    {
      id: 3,
      title: "Full Stack Development",
      instructor: "Mike Johnson",
      students: 920,
      rating: 4.7,
      status: "active",
      createdDate: "January 20, 2026",
      revenue: 9200,
    },
    {
      id: 4,
      title: "Python Basics",
      instructor: "Sarah Williams",
      students: 0,
      rating: 0,
      status: "draft",
      createdDate: "April 28, 2026",
      revenue: 0,
    },
    {
      id: 5,
      title: "Machine Learning 101",
      instructor: "Tom Brown",
      students: 450,
      rating: 4.6,
      status: "active",
      createdDate: "December 5, 2025",
      revenue: 4500,
    },
    {
      id: 6,
      title: "Legacy Course",
      instructor: "Old Instructor",
      students: 50,
      rating: 3.9,
      status: "archived",
      createdDate: "June 1, 2025",
      revenue: 500,
    },
  ];

  const handleLogout = async () => {
    if (confirm("Are you sure you want to logout?")) {
      await logout();
      router.push("/login");
    }
  };

  const filteredCourses = courses.filter((c) => {
    const matchesSearch =
      c.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
      c.instructor.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesStatus = filterStatus === "all" || c.status === filterStatus;
    return matchesSearch && matchesStatus;
  });

  const totalRevenue = filteredCourses.reduce((sum, c) => sum + c.revenue, 0);
  const totalStudents = filteredCourses.reduce((sum, c) => sum + c.students, 0);

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
                <Link
                  href="/admin/courses"
                  className="text-white font-medium transition border-b-2 border-red-500"
                >
                  Courses
                </Link>
                <Link href="/admin/analytics" className="text-gray-300 hover:text-white font-medium transition">
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
            <div className="flex justify-between items-center">
              <div>
                <h2 className="text-4xl font-bold mb-2">📚 Course Management</h2>
                <p className="text-red-100">Manage all platform courses</p>
              </div>
              <button className="bg-white text-red-600 px-4 py-2 rounded font-semibold hover:bg-red-50 transition">
                + Create Course
              </button>
            </div>
          </div>
        </div>

        {/* Main Content */}
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* Stats */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
              <p className="text-gray-400 text-sm">Total Courses</p>
              <p className="text-3xl font-bold text-white mt-2">{courses.length}</p>
            </div>
            <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
              <p className="text-gray-400 text-sm">Total Students</p>
              <p className="text-3xl font-bold text-white mt-2">{totalStudents.toLocaleString()}</p>
            </div>
            <div className="bg-gray-800 border border-gray-700 rounded-lg p-6">
              <p className="text-gray-400 text-sm">Total Revenue</p>
              <p className="text-3xl font-bold text-white mt-2">${totalRevenue.toLocaleString()}</p>
            </div>
          </div>

          {/* Filters and Search */}
          <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 mb-8">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-gray-300 text-sm font-semibold mb-2">Search Courses</label>
                <input
                  type="text"
                  placeholder="Search by title or instructor..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                />
              </div>
              <div>
                <label className="block text-gray-300 text-sm font-semibold mb-2">Filter by Status</label>
                <select
                  value={filterStatus}
                  onChange={(e) => setFilterStatus(e.target.value)}
                  className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                >
                  <option value="all">All Status</option>
                  <option value="active">Active</option>
                  <option value="draft">Draft</option>
                  <option value="archived">Archived</option>
                </select>
              </div>
            </div>
          </div>

          {/* Courses Table */}
          <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="bg-gray-700 border-b border-gray-600">
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Title</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Instructor</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Students</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Rating</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Revenue</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Status</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Created</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredCourses.map((c, index) => (
                    <tr key={c.id} className={`border-b border-gray-700 hover:bg-gray-700 transition ${index % 2 === 0 ? "bg-gray-800" : "bg-gray-750"}`}>
                      <td className="py-3 px-4 text-white font-medium">{c.title}</td>
                      <td className="py-3 px-4 text-gray-400">{c.instructor}</td>
                      <td className="py-3 px-4 text-gray-400">{c.students.toLocaleString()}</td>
                      <td className="py-3 px-4">
                        {c.rating > 0 ? (
                          <span className="text-yellow-400 font-semibold">
                            ⭐ {c.rating}
                          </span>
                        ) : (
                          <span className="text-gray-500">-</span>
                        )}
                      </td>
                      <td className="py-3 px-4 text-green-400 font-semibold">${c.revenue.toLocaleString()}</td>
                      <td className="py-3 px-4">
                        <span
                          className={`px-2 py-1 rounded text-xs font-semibold ${
                            c.status === "active"
                              ? "bg-green-900 text-green-200"
                              : c.status === "draft"
                              ? "bg-yellow-900 text-yellow-200"
                              : "bg-gray-700 text-gray-200"
                          }`}
                        >
                          {c.status.charAt(0).toUpperCase() + c.status.slice(1)}
                        </span>
                      </td>
                      <td className="py-3 px-4 text-gray-400 text-sm">{c.createdDate}</td>
                      <td className="py-3 px-4">
                        <div className="flex gap-2">
                          <button className="text-blue-400 hover:text-blue-300 text-sm font-semibold transition">
                            View
                          </button>
                          <button className="text-yellow-400 hover:text-yellow-300 text-sm font-semibold transition">
                            Edit
                          </button>
                          <button className="text-red-400 hover:text-red-300 text-sm font-semibold transition">
                            Delete
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Pagination */}
            <div className="bg-gray-700 border-t border-gray-600 px-4 py-3 flex justify-between items-center">
              <span className="text-gray-400 text-sm">
                Showing {filteredCourses.length} of {courses.length} courses
              </span>
              <div className="flex gap-2">
                <button className="bg-gray-600 hover:bg-gray-500 text-white px-3 py-1 rounded text-sm transition">
                  Previous
                </button>
                <button className="bg-gray-600 hover:bg-gray-500 text-white px-3 py-1 rounded text-sm transition">
                  Next
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </ProtectedRoute>
  );
}
