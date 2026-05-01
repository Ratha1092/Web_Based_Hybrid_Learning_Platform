# 🏗️ Architecture & User Flows

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    USER BROWSER                             │
│  (Frontend - Next.js + React + TypeScript)                  │
└──────────────────┬──────────────────────────────────────────┘
                   │ HTTP/JSON
                   ↓
┌─────────────────────────────────────────────────────────────┐
│              NEXT.JS FRONTEND (Port 3000)                   │
│                                                              │
│  Pages:                                                      │
│  • /login                                                    │
│  • /register                                                 │
│  • /dashboard                                                │
│  • /forgot-password                                          │
│  • /reset-password/[token]                                   │
│  • /verify-email/[token]                                     │
│  • /2fa-setup                                                │
│  • /security-settings                                        │
│  • /activity-history                                         │
│                                                              │
│  Context:                                                    │
│  • AuthContext (token, user, auth state)                    │
│                                                              │
│  Components:                                                 │
│  • ProtectedRoute (authentication check)                     │
└──────────────────┬──────────────────────────────────────────┘
                   │ API Calls (Bearer Token)
                   ↓
┌─────────────────────────────────────────────────────────────┐
│             LARAVEL BACKEND (Port 8000)                     │
│         /api/v1/* endpoints                                 │
│                                                              │
│  Authentication:                                             │
│  • AuthController (login, register, logout)                 │
│  • Add token logging                                         │
│                                                              │
│  Features:                                                   │
│  • EmailVerificationController                               │
│  • PasswordResetController                                   │
│  • TwoFactorAuthController                                   │
│  • ActivityController                                        │
│  • OAuthController                                           │
│                                                              │
│  Services (Business Logic):                                 │
│  • EmailVerificationService                                  │
│  • PasswordResetService                                      │
│  • TwoFactorAuthService                                      │
│  • ActivityLogService                                        │
│                                                              │
│  Models:                                                     │
│  • User (with relationships)                                 │
│  • EmailVerificationToken                                    │
│  • PasswordResetToken                                        │
│  • TwoFactorCode                                             │
│  • ActivityLog                                               │
│  • OAuthAccount                                              │
└──────────────────┬──────────────────────────────────────────┘
                   │ SQL Queries
                   ↓
┌─────────────────────────────────────────────────────────────┐
│              MYSQL DATABASE                                 │
│                                                              │
│  Tables:                                                     │
│  • users (+ email_verified_at, two_factor_enabled, etc.)    │
│  • email_verification_tokens                                 │
│  • password_reset_tokens                                     │
│  • two_factor_codes                                          │
│  • activity_logs                                             │
│  • oauth_accounts                                            │
└─────────────────────────────────────────────────────────────┘
```

---

## Authentication Flow

```
┌──────────────────────────────────────────────────┐
│          USER VISITS PLATFORM                    │
└──────────────┬───────────────────────────────────┘
               ↓
        ┌──────────────┐
        │ Is Logged In?│
        └──┬─────────┬─┘
      Yes │         │ No
         ↓         ↓
    Dashboard   Login Page
                   ↓
        ┌────────────────────┐
        │ Enter Email & Pass │
        └────────┬───────────┘
                 ↓
        ┌────────────────────────┐
        │ Validate Credentials   │
        │ (POST /auth/login)     │
        └────┬──────────────┬────┘
             │              │
         ✅ Valid      ❌ Invalid
             ↓              ↓
        ┌──────────┐   Error Message
        │ 2FA On?  │   (Try Again)
        └──┬───┬──┘
      Yes │   │ No
         ↓   ↓
    2FA    Dashboard
    Page   (Token Stored)
         ↓
    ┌──────────────────┐
    │ Enter 6-Dig Code │
    └────┬─────────┬───┘
         │         │
      ✅ Valid  ❌ Invalid
         ↓         ↓
      Dashboard  Error
                Message
```

---

## Email Verification Flow

```
┌─────────────────────────────────┐
│  USER REGISTERS                 │
│  (POST /auth/register)          │
└──────────┬──────────────────────┘
           ↓
    ┌──────────────────┐
    │ Account Created  │
    │ Token Generated  │
    └──────┬───────────┘
           ↓
    ┌──────────────────────┐
    │ Email Sent:          │
    │ /verify-email/{token}│
    └──────┬───────────────┘
           ↓
    ┌──────────────────┐
    │ User Clicks Link │
    └──────┬───────────┘
           ↓
    ┌──────────────────────────┐
    │ POST /verify-email/token │
    │ Frontend Auto-Verifies   │
    └──────┬──────────┬────────┘
           │          │
        ✅ Valid   ❌ Invalid/Expired
           ↓          ↓
    ✅ Verified   Error Page
    [user.email_
     verified_at
     set]
```

---

## Password Reset Flow

```
┌──────────────────────────┐
│ USER CLICKS              │
│ "Forgot Password?"       │
└──────────┬───────────────┘
           ↓
    ┌─────────────────────┐
    │ /forgot-password    │
    │ Enter Email         │
    └──────┬──────────────┘
           ↓
    ┌──────────────────────────┐
    │ POST /auth/forgot-password│
    │ Backend Creates Token    │
    │ (1-hour expiry)          │
    └──────┬───────────────────┘
           ↓
    ┌──────────────────────┐
    │ Email Sent with Link:│
    │ /reset-password/{tok}│
    └──────┬───────────────┘
           ↓
    ┌──────────────────────┐
    │ User Clicks Link     │
    └──────┬───────────────┘
           ↓
    ┌──────────────────────┐
    │ /reset-password/[tok]│
    │ Enter New Password   │
    └──────┬───────────────┘
           ↓
    ┌──────────────────────────┐
    │ POST /auth/reset-password│
    │ • Validate token        │
    │ • Hash new password     │
    │ • Revoke all sessions   │
    │ • Log activity          │
    └──────┬────────┬──────────┘
           │        │
        ✅ OK   ❌ Error
           ↓        ↓
    ✅ Success   Error Page
    Redirect    (Expired Token?)
    to Login
```

---

## 2FA Setup & Login Flow

### Setup:
```
┌──────────────────────┐
│ /security-settings   │
│ Click "Enable 2FA"   │
└──────┬───────────────┘
       ↓
┌──────────────────────┐
│ /2fa-setup           │
│ Send Code            │
└──────┬───────────────┘
       ↓
┌──────────────────────────┐
│ POST /auth/2fa/enable    │
│ Generate 6-digit OTP     │
│ (5-minute expiry)        │
│ Email code to user       │
└──────┬───────────────────┘
       ↓
┌──────────────────────┐
│ User Enters Code     │
└──────┬───────────────┘
       ↓
┌───────────────────────────┐
│ POST /auth/2fa/verify-enable
│ • Verify code           │
│ • Enable 2FA            │
│ • Set two_factor_enabled│
└──────┬────────┬──────────┘
       │        │
    ✅ OK   ❌ Invalid
       ↓        ↓
   Success   Retry
```

### Login with 2FA:
```
┌────────────────────┐
│ Regular Login      │
│ Email + Password   │
└──────┬─────────────┘
       ↓
┌─────────────────────┐
│ 2FA Enabled?        │
└──┬──────────────┬──┘
   │              │
   │ No           │ Yes
   ↓              ↓
Dashboard    POST /auth/2fa/send-code
             Generate OTP
             Email to user
             ↓
          /2fa-verify Page
             Enter Code
             ↓
          POST /auth/2fa/verify-code
             ↓
          ┌────────┬──────────┐
          │        │          │
       ✅ OK   ❌ Invalid  ❌ Expired
          ↓        ↓         ↓
       Dashboard Retry   Resend Code
```

---

## Activity Logging Flow

```
┌─────────────────────────────┐
│  ANY AUTH ACTION            │
│  (register, login, logout)  │
└──────────┬──────────────────┘
           ↓
┌───────────────────────────────┐
│ ActivityLogService::log()      │
│ Captured:                      │
│ • Action: "login"              │
│ • User ID: 1                   │
│ • IP: 192.168.1.1              │
│ • User Agent: "Mozilla/5.0..." │
│ • Timestamp: now()             │
└──────────┬────────────────────┘
           ↓
┌───────────────────────────────┐
│ INSERT INTO activity_logs      │
│ (user_id, action, ip_address, │
│  user_agent, created_at)       │
└──────────┬────────────────────┘
           ↓
┌───────────────────────────────┐
│ User Views /activity-history  │
│ GET /api/v1/activity/history  │
│ Retrieve logs                  │
│ Display in table               │
└───────────────────────────────┘
```

---

## Database Schema Overview

### users Table
```sql
id (PK)
name
email (UNIQUE)
password (hashed)
role
email_verified_at ← NULL until verified
two_factor_enabled ← default FALSE
two_factor_secret ← NULL unless set
created_at
updated_at
```

### email_verification_tokens
```sql
id (PK)
user_id (FK)
token (UNIQUE)
created_at
expires_at ← 24 hours
used ← default FALSE
```

### password_reset_tokens
```sql
id (PK)
user_id (FK)
token (UNIQUE)
created_at
expires_at ← 1 hour
used ← default FALSE
```

### two_factor_codes
```sql
id (PK)
user_id (FK)
code ← 6 digits
created_at
expires_at ← 5 minutes
used ← default FALSE
```

### activity_logs
```sql
id (PK)
user_id (FK, nullable)
action ← "login", "logout", "email_verified", etc.
ip_address
user_agent
data ← JSON (optional)
created_at
```

### oauth_accounts
```sql
id (PK)
user_id (FK)
provider ← "google", "github"
provider_id (UNIQUE per provider)
email
name
avatar (nullable)
data ← JSON
created_at
updated_at
```

---

## API Request/Response Flow

### Example: Login Request
```
CLIENT                          SERVER
  │                               │
  ├─ POST /auth/login ────────────→
  │  {                            │
  │    "email": "user@ex.com",    │
  │    "password": "Pass123!"     │
  │  }                            │
  │                               │
  │                      ┌────────┴──────┐
  │                      │ Validate      │
  │                      │ Hash match?   │
  │                      └────────┬──────┘
  │                               │
  │                      ┌────────▼──────┐
  │                      │ Create Token  │
  │                      │ Log Activity  │
  │                      └────────┬──────┘
  │                               │
  │ ←────────── 200 OK ─────────────
  │  {                            │
  │    "success": true,           │
  │    "data": {                  │
  │      "token": "...",          │
  │      "user": {...}            │
  │    }                          │
  │  }                            │
  │                               │
  ├─ Store token in localStorage │
  ├─ Set Authorization header    │
  │                               │
  ├─ GET /me ─────────────────────→
  │  Header: Authorization: Bearer │
  │                               │
  │ ←────────── 200 OK ────────────
  │  { "data": {"user": {...}} }  │
```

---

## Security Measures Implemented

```
┌────────────────────────────────┐
│    INPUT VALIDATION            │
├────────────────────────────────┤
│ • Email format validation      │
│ • Password strength validation │
│ • Name validation (letters)    │
│ • CSRF protection (Laravel)    │
│ • SQL injection prevention     │
│ • XSS protection               │
└────────────────────────────────┘
                ↓
┌────────────────────────────────┐
│   AUTHENTICATION               │
├────────────────────────────────┤
│ • Bearer token auth (Sanctum)  │
│ • Password hashing (bcrypt)    │
│ • Token expiration             │
│ • Session token management     │
└────────────────────────────────┘
                ↓
┌────────────────────────────────┐
│   AUTHORIZATION                │
├────────────────────────────────┤
│ • Protected routes             │
│ • Role-based access control    │
│ • Token verification           │
│ • Admin-only endpoints         │
└────────────────────────────────┘
                ↓
┌────────────────────────────────┐
│   DATA PROTECTION              │
├────────────────────────────────┤
│ • One-time use tokens          │
│ • Token expiration (1-24 hrs)  │
│ • Generic error messages       │
│ • No email disclosure          │
│ • Secure password reset        │
└────────────────────────────────┘
                ↓
┌────────────────────────────────┐
│   AUDIT & MONITORING           │
├────────────────────────────────┤
│ • Activity logging             │
│ • IP address tracking          │
│ • User agent logging           │
│ • Failed login tracking        │
│ • Admin access logs            │
└────────────────────────────────┘
```

---

## File Organization

```
Web_Based_Hybrid_Learning_Platform/
│
├── Backend/
│   ├── app/
│   │   ├── Services/
│   │   │   ├── EmailVerificationService.php
│   │   │   ├── PasswordResetService.php
│   │   │   ├── TwoFactorAuthService.php
│   │   │   └── ActivityLogService.php
│   │   ├── Http/Controllers/Api/
│   │   │   ├── AuthController.php (updated)
│   │   │   ├── EmailVerificationController.php
│   │   │   ├── PasswordResetController.php
│   │   │   ├── TwoFactorAuthController.php
│   │   │   ├── ActivityController.php
│   │   │   └── OAuthController.php
│   │   └── Models/
│   │       ├── User.php (updated)
│   │       ├── EmailVerificationToken.php
│   │       ├── PasswordResetToken.php
│   │       ├── TwoFactorCode.php
│   │       ├── ActivityLog.php
│   │       └── OAuthAccount.php
│   ├── database/migrations/
│   │   ├── 2026_05_01_000001_add_email_verification.php
│   │   ├── 2026_05_01_000002_add_password_reset.php
│   │   ├── 2026_05_01_000003_add_two_factor_auth.php
│   │   ├── 2026_05_01_000004_add_activity_logs.php
│   │   └── 2026_05_01_000005_add_oauth_accounts.php
│   └── routes/
│       └── api.php (updated - 155 lines)
│
├── Frontend/
│   └── src/app/
│       ├── login/
│       ├── register/
│       ├── dashboard/ (updated)
│       ├── forgot-password/
│       ├── reset-password/[token]/
│       ├── verify-email/[token]/
│       ├── 2fa-setup/
│       ├── security-settings/
│       └── activity-history/
│
└── Documentation/
    ├── COMPLETION_SUMMARY.md
    ├── ADVANCED_SECURITY_FEATURES.md
    ├── TESTING_GUIDE.md
    ├── PRODUCTION_CHECKLIST.md
    ├── IMPLEMENTATION_STATUS.md
    ├── README_SECURITY.md
    ├── AUTH_TEST_SUITE.md
    └── AUTH_VISUAL_GUIDES.md
```

---

This comprehensive architecture shows how all 5 security features integrate together to create a secure, user-friendly authentication system! 🚀

