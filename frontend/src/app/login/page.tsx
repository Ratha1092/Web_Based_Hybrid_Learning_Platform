"use client";

import { useState } from "react";
import { login } from "@/services/auth";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { useAuth } from "@/src/context/AuthContext";

export default function LoginPage() {
  const router = useRouter();
  const { setToken, setUser } = useAuth();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [generalError, setGeneralError] = useState("");

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!email.trim()) newErrors.email = "Email is required";
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) newErrors.email = "Invalid email format";
    if (!password) newErrors.password = "Password is required";

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setGeneralError("");

    if (!validateForm()) return;

    try {
      setLoading(true);

      const res = await login(email, password);

      // ✅ Save token securely
      if (res.data?.token) {
        localStorage.setItem("token", res.data.token);
        
        // Optional: Store user info
        if (res.data?.user) {
          localStorage.setItem("user", JSON.stringify(res.data.user));
          setUser(res.data.user);
        }

        setToken(res.data.token);

        // ✅ Redirect to dashboard
        router.push("/dashboard");
      } else {
        setGeneralError("Login successful but no token received");
      }

    } catch (err: any) {
      const data = err.response?.data;

      // Laravel-style validation errors
      if (data?.errors) {
        const formattedErrors: Record<string, string> = {};
        Object.keys(data.errors).forEach((key) => {
          formattedErrors[key] = data.errors[key][0];
        });
        setErrors(formattedErrors);
      }
      // Single message (like "Invalid credentials")
      else if (data?.message) {
        setGeneralError(data.message);
      }
      // Fallback to error message
      else {
        const message = err.message || "Login failed";
        if (message.includes("Invalid credentials") || message.includes("401")) {
          setGeneralError("Invalid email or password");
        } else {
          setGeneralError(message);
        }
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex h-screen items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
      <form
        onSubmit={handleLogin}
        className="bg-white p-8 rounded-lg shadow-lg w-96 space-y-4"
      >
        <h2 className="text-2xl font-bold text-gray-800 text-center">Login</h2>

        {generalError && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            {generalError}
          </div>
        )}

        <div>
          <input
            type="email"
            placeholder="Email Address"
            className={`w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
              errors.email ? "border-red-500" : "border-gray-300"
            }`}
            value={email}
            onChange={(e) => {
              setEmail(e.target.value);
              if (errors.email) setErrors({ ...errors, email: "" });
            }}
            disabled={loading}
          />
          {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email}</p>}
        </div>

        <div>
          <input
            type="password"
            placeholder="Password"
            className={`w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
              errors.password ? "border-red-500" : "border-gray-300"
            }`}
            value={password}
            onChange={(e) => {
              setPassword(e.target.value);
              if (errors.password) setErrors({ ...errors, password: "" });
            }}
            disabled={loading}
          />
          {errors.password && <p className="text-red-500 text-sm mt-1">{errors.password}</p>}
        </div>

        <button
          type="submit"
          className="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded transition disabled:opacity-50 disabled:cursor-not-allowed"
          disabled={loading}
        >
          {loading ? "Logging in..." : "Login"}
        </button>

        <div className="text-center space-y-2 text-sm text-gray-600">
          <p>
            Don't have an account?{" "}
            <Link href="/register" className="text-indigo-600 font-semibold hover:underline">
              Register here
            </Link>
          </p>
          <p>
            <Link href="/forgot-password" className="text-indigo-600 font-semibold hover:underline">
              Forgot password?
            </Link>
          </p>
        </div>
      </form>
    </div>
  );
}