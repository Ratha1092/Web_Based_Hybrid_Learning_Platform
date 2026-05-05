"use client";

import { useAuth } from "@/src/context/AuthContext";
import { useRouter } from "next/navigation";
import { useState } from "react";
import Link from "next/link";
import { ProtectedRoute } from "@/src/components/ProtectedRoute";

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  status: "active" | "inactive" | "suspended";
  joinedDate: string;
  courses: number;
}

export default function AdminUsersPage() {
  const { user, logout } = useAuth();
  const router = useRouter();
  const [searchTerm, setSearchTerm] = useState("");
  const [filterRole, setFilterRole] = useState("all");

  const users: User[] = [
    {
      id: 1,
      name: "Alice Johnson",
      email: "alice@example.com",
      role: "student",
      status: "active",
      joinedDate: "May 1, 2026",
      courses: 3,
    },
    {
      id: 2,
      name: "Bob Smith",
      email: "bob@example.com",
      role: "instructor",
      status: "active",
      joinedDate: "April 30, 2026",
      courses: 5,
    },
    {
      id: 3,
      name: "Carol White",
      email: "carol@example.com",
      role: "student",
      status: "active",
      joinedDate: "April 29, 2026",
      courses: 2,
    },
    {
      id: 4,
      name: "David Brown",
      email: "david@example.com",
      role: "student",
      status: "inactive",
      joinedDate: "April 28, 2026",
      courses: 1,
    },
    {
      id: 5,
      name: "Emma Davis",
      email: "emma@example.com",
      role: "instructor",
      status: "active",
      joinedDate: "April 27, 2026",
      courses: 4,
    },
    {
      id: 6,
      name: "Frank Miller",
      email: "frank@example.com",
      role: "student",
      status: "suspended",
      joinedDate: "April 26, 2026",
      courses: 0,
    },
  ];

  const handleLogout = async () => {
    if (confirm("Are you sure you want to logout?")) {
      await logout();
      router.push("/login");
    }
  };

  const filteredUsers = users.filter((u) => {
    const matchesSearch =
      u.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      u.email.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesRole = filterRole === "all" || u.role === filterRole;
    return matchesSearch && matchesRole;
  });

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
                <Link
                  href="/admin/users"
                  className="text-white font-medium transition border-b-2 border-red-500"
                >
                  Users
                </Link>
                <Link href="/admin/courses" className="text-gray-300 hover:text-white font-medium transition">
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
                <h2 className="text-4xl font-bold mb-2">👥 User Management</h2>
                <p className="text-red-100">Manage platform users and their roles</p>
              </div>
              <button className="bg-white text-red-600 px-4 py-2 rounded font-semibold hover:bg-red-50 transition">
                + Add User
              </button>
            </div>
          </div>
        </div>

        {/* Main Content */}
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* Filters and Search */}
          <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 mb-8">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-gray-300 text-sm font-semibold mb-2">Search Users</label>
                <input
                  type="text"
                  placeholder="Search by name or email..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                />
              </div>
              <div>
                <label className="block text-gray-300 text-sm font-semibold mb-2">Filter by Role</label>
                <select
                  value={filterRole}
                  onChange={(e) => setFilterRole(e.target.value)}
                  className="w-full bg-gray-700 border border-gray-600 text-white px-4 py-2 rounded focus:outline-none focus:border-red-500"
                >
                  <option value="all">All Roles</option>
                  <option value="admin">Admin</option>
                  <option value="instructor">Instructor</option>
                  <option value="student">Student</option>
                </select>
              </div>
            </div>
          </div>

          {/* Users Table */}
          <div className="bg-gray-800 border border-gray-700 rounded-lg shadow-lg overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="bg-gray-700 border-b border-gray-600">
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Name</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Email</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Role</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Status</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Courses</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Joined</th>
                    <th className="text-left py-3 px-4 text-gray-300 font-semibold">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredUsers.map((u, index) => (
                    <tr key={u.id} className={`border-b border-gray-700 hover:bg-gray-700 transition ${index % 2 === 0 ? "bg-gray-800" : "bg-gray-750"}`}>
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
                      <td className="py-3 px-4 text-gray-400">{u.courses}</td>
                      <td className="py-3 px-4 text-gray-400 text-sm">{u.joinedDate}</td>
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
                Showing {filteredUsers.length} of {users.length} users
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
