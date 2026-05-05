"use client";

import { useState } from "react";
import { register } from "@/services/auth";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { useAuth } from "@/src/context/AuthContext";

export default function RegisterPage() {
  const router = useRouter();
  const { setToken, setUser } = useAuth();

  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [passwordConfirm, setPasswordConfirm] = useState("");
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [generalError, setGeneralError] = useState("");

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!name.trim()) newErrors.name = "Name is required";
    if (!email.trim()) newErrors.email = "Email is required";
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) newErrors.email = "Invalid email format";
    if (password.length < 8) newErrors.password = "Password must be at least 8 characters";
    if (password !== passwordConfirm) newErrors.passwordConfirm = "Passwords do not match";

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    setGeneralError("");

    if (!validateForm()) return;

    try {
      setLoading(true);

      const res = await register(name, email, password, passwordConfirm);

      if (res.data?.token) {
        // Auto-login after registration
        localStorage.setItem("token", res.data.token);
        if (res.data?.user) {
          localStorage.setItem("user", JSON.stringify(res.data.user));
          setUser(res.data.user);
        }
        setToken(res.data.token);

        // Redirect to home
        router.push("/home");
      }

    } 
    catch (err: any) {
      const data = err.response?.data;

      // Laravel-style validation errors
      if (data?.errors) {
        const formattedErrors: Record<string, string> = {};

        Object.keys(data.errors).forEach((key) => {
          formattedErrors[key] = data.errors[key][0];
        });

        setErrors(formattedErrors);
      }

      // Single message (like "Email already exists")
      else if (data?.message) {
        setGeneralError(data.message);
      }

      // Fallback
      else {
        setGeneralError("Registration failed");
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex h-screen items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
      <form
        onSubmit={handleRegister}
        className="bg-white p-8 rounded-lg shadow-lg w-96 space-y-4"
      >
        <h2 className="text-2xl font-bold text-gray-800 text-center">Create Account</h2>

        {generalError && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            {generalError}
          </div>
        )}

        <div>
          <input
            type="text"
            placeholder="Full Name"
            className={`w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
              errors.name ? "border-red-500" : "border-gray-300"
            }`}
            value={name}
            onChange={(e) => {
              setName(e.target.value);
              if (errors.name) setErrors({ ...errors, name: "" });
            }}
            disabled={loading}
          />
          {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
        </div>

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
            placeholder="Password (min 8 characters)"
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

        <div>
          <input
            type="password"
            placeholder="Confirm Password"
            className={`w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
              errors.passwordConfirm ? "border-red-500" : "border-gray-300"
            }`}
            value={passwordConfirm}
            onChange={(e) => {
              setPasswordConfirm(e.target.value);
              if (errors.passwordConfirm) setErrors({ ...errors, passwordConfirm: "" });
            }}
            disabled={loading}
          />
          {errors.passwordConfirm && (
            <p className="text-red-500 text-sm mt-1">{errors.passwordConfirm}</p>
          )}
        </div>

        <button
          type="submit"
          className="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded transition disabled:opacity-50 disabled:cursor-not-allowed"
          disabled={loading}
        >
          {loading ? "Creating Account..." : "Register"}
        </button>

        <p className="text-sm text-center text-gray-600">
          Already have an account?{" "}
          <Link href="/login" className="text-indigo-600 font-semibold hover:underline">
            Login
          </Link>
        </p>
      </form>
    </div>
  );
}