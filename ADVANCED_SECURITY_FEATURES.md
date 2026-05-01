# Advanced Security Features Implementation Guide

## Overview

This guide covers the implementation of 5 advanced security features:
- ✅ Email Verification
- ✅ Password Reset
- ✅ Two-Factor Authentication (2FA)
- ✅ Activity Logging
- ✅ OAuth Integration (Google & GitHub)

---

## 1. Email Verification

### Purpose
Verify that users own the email address they registered with, preventing spam/invalid registrations.

### Database Tables
- `email_verification_tokens` - Stores verification tokens
- Modified `users` table - Added `email_verified_at` column

### API Endpoints

#### Send Verification Email
```
POST /v1/auth/email/send-verification
Authorization: Bearer TOKEN
```

**Response:**
```json
{
  "success": true,
  "message": "Verification email sent",
  "data": {
    "message": "Verification email sent",
    "verification_link": "http://localhost:3000/verify-email/TOKEN"
  }
}
```

#### Verify Email with Token
```
POST /v1/verify-email/{token}
```

**Response:**
```json
{
  "success": true,
  "message": "Email verified successfully"
}
```

#### Check Verification Status
```
GET /v1/auth/email/status
Authorization: Bearer TOKEN
```

**Response:**
```json
{
  "success": true,
  "data": {
    "is_verified": true,
    "verified_at": "2026-05-01T10:00:00Z"
  }
}
```

### Frontend Implementation
- Page: `/src/app/verify-email/[token]/page.tsx`
- Auto-verifies when user clicks email link
- Shows success/error messages
- Redirects to login after verification

### How It Works
1. User registers → Receives verification email with link
2. Clicks link → Redirects to frontend `/verify-email/{token}`
3. Frontend sends token to backend
4. Backend validates token and marks email as verified
5. Activity logged: `email_verified`

---

## 2. Password Reset

### Purpose
Allow users to reset forgotten passwords securely with time-limited tokens.

### Database Tables
- `password_reset_tokens` - Stores password reset tokens (expires in 1 hour)

### API Endpoints

#### Request Password Reset
```
POST /v1/auth/forgot-password
```

**Request:**
```json
{
  "email": "user@example.com"
}
```

**Response:**
```json
{
  "success": true,
  "message": "If email exists, reset link has been sent",
  "data": {
    "reset_link": "http://localhost:3000/reset-password/TOKEN"
  }
}
```

#### Verify Reset Token
```
POST /v1/auth/verify-reset-token
```

**Request:**
```json
{
  "token": "TOKEN_HERE"
}
```

#### Reset Password with Token
```
POST /v1/auth/reset-password
```

**Request:**
```json
{
  "token": "TOKEN_HERE",
  "password": "NewPassword123!",
  "password_confirmation": "NewPassword123!"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Password reset successfully. Please login with your new password."
}
```

### Frontend Implementation
- Page: `/src/app/forgot-password/page.tsx` - Request reset
- Page: `/src/app/reset-password/[token]/page.tsx` - Reset password
- Password validation enforced
- Auto-redirects to login after reset

### Features
- ✅ 1-hour token expiration
- ✅ Generic error messages (security)
- ✅ Revokes all existing tokens after reset
- ✅ Strong password validation required

---

## 3. Two-Factor Authentication (2FA)

### Purpose
Add extra layer of security with 6-digit OTP codes sent via email.

### Database Tables
- `two_factor_codes` - Stores 6-digit OTP codes (5-minute expiry)
- Modified `users` table - Added `two_factor_enabled` and `two_factor_secret`

### API Endpoints

#### Enable 2FA
```
POST /v1/auth/2fa/enable
Authorization: Bearer TOKEN
```

**Response:**
```json
{
  "success": true,
  "message": "2FA code sent",
  "data": {
    "code": "123456",
    "message": "6-digit code sent to your email"
  }
}
```

#### Verify & Enable 2FA
```
POST /v1/auth/2fa/verify-enable
Authorization: Bearer TOKEN
```

**Request:**
```json
{
  "code": "123456"
}
```

#### Disable 2FA
```
POST /v1/auth/2fa/disable
Authorization: Bearer TOKEN
```

**Request:**
```json
{
  "password": "CurrentPassword123!"
}
```

#### Get 2FA Status
```
GET /v1/auth/2fa/status
Authorization: Bearer TOKEN
```

#### During Login - Send Code
```
POST /v1/auth/2fa/send-code
```

**Request:**
```json
{
  "email": "user@example.com"
}
```

#### During Login - Verify Code
```
POST /v1/auth/2fa/verify-code
```

**Request:**
```json
{
  "email": "user@example.com",
  "code": "123456"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "...",
    "user": {...}
  }
}
```

### 2FA Login Flow
```
User enters email/password
    ↓
Backend validates credentials
    ↓
If 2FA enabled: Ask for code
    POST /auth/2fa/send-code
    (User receives 6-digit code via email)
    ↓
User enters code
    POST /auth/2fa/verify-code
    ↓
Backend verifies code and returns token
    ↓
Login complete
```

### Features
- ✅ 6-digit OTP codes
- ✅ 5-minute code expiration
- ✅ One-time use per code
- ✅ Optional (not required)
- ✅ Can be disabled by user

---

## 4. Activity Logging

### Purpose
Track user actions and login history for security audits and suspicious activity detection.

### Database Tables
- `activity_logs` - Records all user actions with IP, user agent, timestamp

### API Endpoints

#### Get User Activity History
```
GET /v1/activity/history?limit=50
Authorization: Bearer TOKEN
```

**Response:**
```json
{
  "success": true,
  "message": "Activity history",
  "data": {
    "logs": [
      {
        "id": 1,
        "user_id": 1,
        "action": "login",
        "ip_address": "192.168.1.1",
        "user_agent": "Mozilla/5.0...",
        "data": null,
        "created_at": "2026-05-01T10:00:00Z"
      }
    ],
    "count": 50
  }
}
```

#### Get Recent Logins
```
GET /v1/activity/logins?limit=10
Authorization: Bearer TOKEN
```

#### Get All Activity Logs (Admin Only)
```
GET /v1/activity/all?limit=100
Authorization: Bearer TOKEN
```

### Logged Actions
- `registration` - User registered
- `login` - Successful login
- `failed_login` - Failed login attempt
- `logout` - User logged out
- `password_changed` - Password was changed
- `password_reset_requested` - Password reset requested
- `email_verified` - Email verified
- `2fa_enabled` - 2FA enabled
- `2fa_disabled` - 2FA disabled
- `2fa_enable_requested` - 2FA enable requested
- `oauth_signup` - OAuth signup
- `oauth_linked` - OAuth account linked
- `oauth_unlinked` - OAuth account unlinked

### Data Tracked
- User ID
- Action type
- IP address
- User agent (browser info)
- Timestamp
- Additional context data (JSON)

---

## 5. OAuth Integration

### Purpose
Allow users to sign up/login using Google or GitHub accounts.

### Database Tables
- `oauth_accounts` - Links OAuth provider accounts to users

### API Endpoints

#### Google OAuth Callback
```
POST /v1/auth/oauth/google
```

**Request:**
```json
{
  "id_token": "GOOGLE_ID_TOKEN",
  "email": "user@gmail.com",
  "name": "John Doe"
}
```

#### GitHub OAuth Callback
```
POST /v1/auth/oauth/github
```

**Request:**
```json
{
  "code": "GITHUB_AUTH_CODE",
  "state": "STATE_PARAMETER"
}
```

#### Get Linked OAuth Accounts
```
GET /v1/auth/oauth/accounts
Authorization: Bearer TOKEN
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "provider": "google",
      "email": "user@gmail.com",
      "avatar": "https://...",
      "created_at": "2026-05-01T10:00:00Z"
    }
  ]
}
```

#### Link OAuth Account
```
POST /v1/auth/oauth/link
Authorization: Bearer TOKEN
```

**Request:**
```json
{
  "provider": "google",
  "provider_id": "GOOGLE_ID",
  "email": "user@gmail.com",
  "name": "John Doe"
}
```

#### Unlink OAuth Account
```
DELETE /v1/auth/oauth/{provider}
Authorization: Bearer TOKEN
```

### OAuth Flow
```
1. User clicks "Sign in with Google/GitHub"
    ↓
2. Redirected to OAuth provider
    ↓
3. User authorizes app
    ↓
4. Provider redirects back with auth code/token
    ↓
5. Frontend sends auth data to backend
    ↓
6. Backend checks if OAuth account exists
    If exists: Login user
    If not exists: Create user & link OAuth account
    ↓
7. Return token and user data
```

### Features
- ✅ Auto-create account on first OAuth signup
- ✅ Auto-verify email (provider already verified)
- ✅ Link multiple OAuth providers to one account
- ✅ Unlink OAuth accounts
- ✅ Fallback email/password login

---

## Database Migrations

All migrations are in `Backend/database/migrations/`:

```
2026_05_01_000001_add_email_verification.php
2026_05_01_000002_add_password_reset.php
2026_05_01_000003_add_two_factor_auth.php
2026_05_01_000004_add_activity_logs.php
2026_05_01_000005_add_oauth_accounts.php
```

### Run Migrations
```bash
cd Backend
php artisan migrate
```

---

## Backend Services

Located in `Backend/app/Services/`:

### EmailVerificationService
- `generateToken()` - Create verification token
- `verifyEmail()` - Verify with token
- `isVerified()` - Check if verified
- `getVerificationLink()` - Get link for email

### PasswordResetService
- `generateToken()` - Create reset token
- `resetPassword()` - Reset with token
- `isTokenValid()` - Validate token
- `getResetLink()` - Get link for email

### TwoFactorAuthService
- `generateCode()` - Create 6-digit OTP
- `verifyCode()` - Verify OTP
- `enable()` - Enable 2FA
- `disable()` - Disable 2FA
- `isEnabled()` - Check if enabled

### ActivityLogService
- `log()` - Log an action
- `getUserHistory()` - Get user's activity
- `getRecentLogins()` - Get recent logins
- `getAllLogs()` - Get all logs (admin)

---

## Frontend Pages

### New Pages Created

1. `/src/app/forgot-password/page.tsx`
   - Request password reset
   - Shows success message

2. `/src/app/reset-password/[token]/page.tsx`
   - Reset password with token
   - Validate new password
   - Redirect to login

3. `/src/app/verify-email/[token]/page.tsx`
   - Auto-verify email
   - Show status
   - Redirect to login

### Updated Login Page
- Add "Forgot password?" link
- Support 2FA code entry if enabled

---

## Configuration

### Email Verification
Add to `config/app.php`:
```php
'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
```

### Email Setup (Required for Production)
```php
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@platform.com
```

---

## Security Considerations

### ✅ Implemented
- Token expiration (1 hour for password reset, 5 min for 2FA)
- One-time use tokens (marked as `used`)
- Generic error messages (don't reveal if email exists)
- Password hashing
- Activity logging
- IP tracking
- Rate limiting ready (add middleware)

### 🔐 Recommendations
1. **Email Service** - Integrate real email sending (MailTrap, SendGrid, etc.)
2. **Rate Limiting** - Limit password reset/2FA requests per email
3. **HTTPS Only** - In production, always use HTTPS
4. **Backup Codes** - For 2FA, provide backup codes if 2FA device lost
5. **Email Templates** - Create professional HTML email templates
6. **Suspicious Activity** - Alert users of unusual login locations
7. **Session Timeout** - Auto-logout after inactivity
8. **CAPTCHA** - Add CAPTCHA to registration/login

---

## Testing the Features

### 1. Email Verification
```bash
# Register new user
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name":"Test User",
    "email":"test@example.com",
    "password":"Pass123!@",
    "password_confirmation":"Pass123!@"
  }'

# Check verification status
curl -X GET http://localhost:8000/api/v1/auth/email/status \
  -H "Authorization: Bearer TOKEN"

# Verify email with token (get from response above)
curl -X POST http://localhost:8000/api/v1/verify-email/TOKEN
```

### 2. Password Reset
```bash
# Request reset
curl -X POST http://localhost:8000/api/v1/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com"}'

# Reset password (use token from response above)
curl -X POST http://localhost:8000/api/v1/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "token":"TOKEN_HERE",
    "password":"NewPass123!",
    "password_confirmation":"NewPass123!"
  }'
```

### 3. 2FA
```bash
# Enable 2FA
curl -X POST http://localhost:8000/api/v1/auth/2fa/enable \
  -H "Authorization: Bearer TOKEN"

# Get code from response (for testing)
# Then verify to enable
curl -X POST http://localhost:8000/api/v1/auth/2fa/verify-enable \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"code":"123456"}'

# Check status
curl -X GET http://localhost:8000/api/v1/auth/2fa/status \
  -H "Authorization: Bearer TOKEN"
```

### 4. Activity Logging
```bash
# Get user's activity
curl -X GET http://localhost:8000/api/v1/activity/history \
  -H "Authorization: Bearer TOKEN"

# Get recent logins
curl -X GET http://localhost:8000/api/v1/activity/logins \
  -H "Authorization: Bearer TOKEN"
```

---

## Next Steps

1. ✅ Setup email service (MailTrap/SendGrid)
2. ✅ Create email templates
3. ✅ Test all flows in development
4. ✅ Add rate limiting middleware
5. ✅ Setup backup codes for 2FA
6. ✅ Add suspicious activity alerts
7. ✅ Deploy to production with HTTPS

---

## Troubleshooting

### Issue: "Class not found" errors
- Run `composer dump-autoload`

### Issue: Database columns missing
- Run `php artisan migrate`

### Issue: Tokens not generating
- Check `users` table has `email_verified_at` column
- Check personal_access_tokens table exists

### Issue: Email not sent
- Setup mail configuration in `.env`
- Use MailTrap for testing: https://mailtrap.io

### Issue: OAuth not working
- Get API keys from Google/GitHub developer console
- Configure redirect URIs correctly

