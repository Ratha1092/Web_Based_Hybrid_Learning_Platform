import { useState } from 'react';
import {
  Eye,
  EyeOff,
  Mail,
  Lock,
  Award,
  ArrowRight,
  BookOpen,
  Users,
  Star,
} from 'lucide-react';
import { useAuth } from '../context/AuthContext';
import './AuthPages.css';

interface LoginPageProps {
  onNavigate: (page: 'home' | 'login' | 'register') => void;
}

export default function LoginPage({ onNavigate }: LoginPageProps) {
  const { login, isLoading, error, clearError } = useAuth();
  const [showPassword, setShowPassword] = useState(false);
  const [form, setForm] = useState({ email: '', password: '', remember: false });
  const [localError, setLocalError] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLocalError(null);
    clearError();

    try {
      await login({
        email: form.email,
        password: form.password,
      });
      onNavigate('home');
    } catch (err) {
      const errorMsg = err instanceof Error ? err.message : 'Login failed';
      try {
        const parsed = JSON.parse(errorMsg);
        setLocalError(parsed.message || 'Login failed');
      } catch {
        setLocalError(errorMsg || 'Login failed');
      }
    }
  };

  const displayError = localError || error;

  return (
    <div className="auth-root">
      {/* ── Left panel ── */}
      <div className="auth-panel auth-left">
        <div className="auth-left-inner">
          {/* Logo */}
          <div className="auth-logo" onClick={() => onNavigate('home')}>
            <div className="auth-logo-icon">
              <Award size={22} />
            </div>
            <span className="auth-logo-text">DRC</span>
          </div>

          {/* Illustration backdrop */}
          <div className="auth-illustration">
            <div className="illus-circle illus-circle-1" />
            <div className="illus-circle illus-circle-2" />
            <div className="illus-circle illus-circle-3" />

            <div className="illus-card illus-card-top">
              <div className="illus-card-icon">
                <BookOpen size={18} />
              </div>
              <div>
                <div className="illus-card-title">250k+ Courses</div>
                <div className="illus-card-sub">Learn at your own pace</div>
              </div>
            </div>

            <div className="illus-avatar-stack">
              {['#FF6B35', '#2EC4B6', '#9C27B0', '#4CAF50'].map((c, i) => (
                <div
                  key={i}
                  className="illus-avatar"
                  style={{ background: c, zIndex: 4 - i }}
                />
              ))}
              <span className="illus-avatar-label">+12k Students</span>
            </div>

            <div className="illus-card illus-card-bottom">
              <Star size={16} fill="#FFC107" stroke="#FFC107" />
              <div>
                <div className="illus-card-title">4.9 / 5 Rating</div>
                <div className="illus-card-sub">From 38,000 reviews</div>
              </div>
            </div>
          </div>

          {/* Tagline */}
          <div className="auth-tagline">
            <h2>Learn without limits</h2>
            <p>
              Join 250,000+ students already growing their skills on our
              platform.
            </p>
          </div>

          {/* Stats row */}
          <div className="auth-stats">
            {[
              { icon: BookOpen, value: '250k+', label: 'Courses' },
              { icon: Users, value: '50k+', label: 'Instructors' },
              { icon: Star, value: '4.9', label: 'Rating' },
            ].map(({ icon: Icon, value, label }) => (
              <div className="auth-stat" key={label}>
                <Icon size={16} />
                <span className="stat-val">{value}</span>
                <span className="stat-lbl">{label}</span>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* ── Right panel ── */}
      <div className="auth-panel auth-right">
        <div className="auth-form-wrap">
          <div className="auth-form-header">
            <h1 className="auth-form-title">Welcome back 👋</h1>
            <p className="auth-form-sub">
              Sign in to continue your learning journey
            </p>
          </div>

          {/* Error message */}
          {displayError && (
            <div className="auth-error-alert">
              <span>{displayError}</span>
              <button
                type="button"
                className="error-close"
                onClick={() => {
                  setLocalError(null);
                  clearError();
                }}
              >
                ×
              </button>
            </div>
          )}

          {/* Social login */}
          <div className="social-login-row">
            <button type="button" className="social-login-btn">
              <img
                src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                alt="Google"
                width={18}
              />
              Google
            </button>
            <button type="button" className="social-login-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
              </svg>
              Facebook
            </button>
          </div>

          <div className="auth-divider">
            <span>or sign in with email</span>
          </div>

          <form className="auth-form" onSubmit={handleSubmit}>
            {/* Email */}
            <div className="form-group">
              <label className="form-label">Email Address</label>
              <div className="input-wrap">
                <Mail size={17} className="input-icon" />
                <input
                  type="email"
                  className="form-input"
                  placeholder="you@example.com"
                  value={form.email}
                  onChange={(e) => setForm({ ...form, email: e.target.value })}
                  required
                  disabled={isLoading}
                />
              </div>
            </div>

            {/* Password */}
            <div className="form-group">
              <div className="label-row">
                <label className="form-label">Password</label>
                <a href="#" className="forgot-link">
                  Forgot password?
                </a>
              </div>
              <div className="input-wrap">
                <Lock size={17} className="input-icon" />
                <input
                  type={showPassword ? 'text' : 'password'}
                  className="form-input"
                  placeholder="Enter your password"
                  value={form.password}
                  onChange={(e) =>
                    setForm({ ...form, password: e.target.value })
                  }
                  required
                  disabled={isLoading}
                />
                <button
                  type="button"
                  className="eye-btn"
                  onClick={() => setShowPassword((v) => !v)}
                  disabled={isLoading}
                >
                  {showPassword ? <EyeOff size={16} /> : <Eye size={16} />}
                </button>
              </div>
            </div>

            {/* Remember me */}
            <label className="remember-row">
              <input
                type="checkbox"
                className="remember-check"
                checked={form.remember}
                onChange={(e) =>
                  setForm({ ...form, remember: e.target.checked })
                }
                disabled={isLoading}
              />
              <span>Remember me for 30 days</span>
            </label>

            {/* Submit */}
            <button
              type="submit"
              className={`auth-submit-btn ${isLoading ? 'loading' : ''}`}
              disabled={isLoading}
            >
              {isLoading ? (
                <span className="spinner" />
              ) : (
                <>
                  Sign In <ArrowRight size={18} />
                </>
              )}
            </button>
          </form>

          <p className="auth-switch">
            Don't have an account?{' '}
            <button
              type="button"
              className="auth-switch-link"
              onClick={() => onNavigate('register')}
              disabled={isLoading}
            >
              Sign up for free
            </button>
          </p>
        </div>
      </div>
    </div>
  );
}
