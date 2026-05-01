# Security Features Testing Guide

Complete guide to test all 5 advanced security features using curl commands.

## Prerequisites

1. Backend running: `cd Backend && php artisan serve`
2. Frontend running: `cd Frontend && npm run dev`
3. Test user registered or use examples below

---

## 1. Email Verification Testing

### 1.1 Register a New User
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "TestPass123!",
    "password_confirmation": "TestPass123!"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "token": "...",
    "user": {...}
  }
}
```

### 1.2 Check Email Verification Status
```bash
TOKEN="<token_from_login>"

curl -X GET http://localhost:8000/api/v1/auth/email/status \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "is_verified": false,
    "verified_at": null
  }
}
```

### 1.3 Request Verification Email
```bash
curl -X POST http://localhost:8000/api/v1/auth/email/send-verification \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "verification_link": "http://localhost:3000/verify-email/TOKEN_HERE"
  }
}
```

### 1.4 Verify Email with Token
Extract token from response above and:

```bash
VERIFY_TOKEN="<token_from_send_verification>"

curl -X POST http://localhost:8000/api/v1/verify-email/$VERIFY_TOKEN \
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Email verified successfully"
}
```

### 1.5 Verify Changed Status
```bash
curl -X GET http://localhost:8000/api/v1/auth/email/status \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "is_verified": true,
    "verified_at": "2026-05-01T10:00:00Z"
  }
}
```

---

## 2. Password Reset Testing

### 2.1 Request Password Reset
```bash
curl -X POST http://localhost:8000/api/v1/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com"}'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "If email exists, reset link has been sent",
  "data": {
    "reset_link": "http://localhost:3000/reset-password/TOKEN_HERE"
  }
}
```

### 2.2 Verify Reset Token
```bash
RESET_TOKEN="<token_from_forgot_password>"

curl -X POST http://localhost:8000/api/v1/auth/verify-reset-token \
  -H "Content-Type: application/json" \
  -d "{\"token\": \"$RESET_TOKEN\"}"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Token is valid"
}
```

### 2.3 Reset Password
```bash
curl -X POST http://localhost:8000/api/v1/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "token": "'"$RESET_TOKEN"'",
    "password": "NewPassword123!",
    "password_confirmation": "NewPassword123!"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Password reset successfully. Please login with your new password."
}
```

### 2.4 Login with New Password
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "NewPassword123!"
  }'
```

**Expected Response:**
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

---

## 3. Two-Factor Authentication Testing

### 3.1 Enable 2FA (Send Code)
```bash
TOKEN="<login_token>"

curl -X POST http://localhost:8000/api/v1/auth/2fa/enable \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "2FA code sent",
  "data": {
    "code": "123456"
  }
}
```

**Note:** Copy the code for next step (for testing only; in production, users receive via email)

### 3.2 Verify and Enable 2FA
```bash
OTP_CODE="<code_from_enable>"

curl -X POST http://localhost:8000/api/v1/auth/2fa/verify-enable \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{\"code\": \"$OTP_CODE\"}"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "2FA enabled successfully"
}
```

### 3.3 Check 2FA Status
```bash
curl -X GET http://localhost:8000/api/v1/auth/2fa/status \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "two_factor_enabled": true
  }
}
```

### 3.4 Login with 2FA (Step 1: Get Code)
```bash
curl -X POST http://localhost:8000/api/v1/auth/2fa/send-code \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com"}'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "2FA code sent",
  "data": {
    "code": "654321"
  }
}
```

### 3.5 Login with 2FA (Step 2: Verify Code)
```bash
TWO_FA_CODE="<code_from_send_code>"

curl -X POST http://localhost:8000/api/v1/auth/2fa/verify-code \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "code": "'"$TWO_FA_CODE"'"
  }'
```

**Expected Response:**
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

### 3.6 Disable 2FA
```bash
curl -X POST http://localhost:8000/api/v1/auth/2fa/disable \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"password": "TestPass123!"}'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "2FA disabled successfully"
}
```

---

## 4. Activity Logging Testing

### 4.1 Get User Activity History
```bash
curl -X GET "http://localhost:8000/api/v1/activity/history?limit=50" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

**Expected Response:**
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
        "ip_address": "127.0.0.1",
        "user_agent": "curl/7.64.1",
        "data": null,
        "created_at": "2026-05-01T10:00:00Z"
      },
      {
        "id": 2,
        "user_id": 1,
        "action": "email_verified",
        "ip_address": "127.0.0.1",
        "user_agent": "curl/7.64.1",
        "data": null,
        "created_at": "2026-05-01T10:05:00Z"
      }
    ],
    "count": 2
  }
}
```

### 4.2 Get Recent Logins Only
```bash
curl -X GET "http://localhost:8000/api/v1/activity/logins?limit=10" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "User logins",
  "data": {
    "logs": [
      {
        "id": 1,
        "action": "login",
        "ip_address": "127.0.0.1",
        "created_at": "2026-05-01T10:00:00Z"
      }
    ],
    "count": 1
  }
}
```

### 4.3 View Activity on Frontend
Navigate to: `http://localhost:3000/activity-history`

You should see a table of activities with:
- Action type (color-coded badges)
- Date & time
- IP address
- Device info

---

## 5. OAuth Testing (Placeholder)

### 5.1 Get Linked OAuth Accounts
```bash
curl -X GET http://localhost:8000/api/v1/auth/oauth/accounts \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": []
}
```

### 5.2 Link OAuth Account (Manual)
```bash
curl -X POST http://localhost:8000/api/v1/auth/oauth/link \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "provider": "google",
    "provider_id": "12345",
    "email": "user@gmail.com",
    "name": "Test User"
  }'
```

**Note:** Full OAuth implementation requires Google/GitHub API keys and configuration.

---

## Frontend Testing

### Email Verification Page
```
1. Navigate to: http://localhost:3000/verify-email/<token>
2. Should show "Verifying..." then success/error
3. Should redirect to login after success
```

### Forgot Password Page
```
1. Navigate to: http://localhost:3000/forgot-password
2. Enter email: test@example.com
3. Should show: "Verification link sent"
4. Should receive reset link (see backend response)
```

### Reset Password Page
```
1. Click reset link: http://localhost:3000/reset-password/<token>
2. Enter new password: NewPass456!
3. Confirm password
4. Should redirect to login
5. Login with new password
```

### 2FA Setup Page
```
1. Navigate to: http://localhost:3000/2fa-setup
2. Click "Send Code"
3. Enter 6-digit code (shown in testing)
4. Click "Verify Code"
5. Should redirect to security settings
```

### Security Settings Page
```
1. Navigate to: http://localhost:3000/security-settings
2. Should show:
   - Email verification status
   - 2FA status & toggle
   - Connected OAuth accounts
   - Activity history link
```

### Activity History Page
```
1. Navigate to: http://localhost:3000/activity-history
2. Should show table of activities
3. Can filter by "All Activity" or "Login History"
4. Shows IP, device, date/time
```

---

## Expected Activity Log Actions

After running tests, you should see these in activity logs:

```
✅ registration    - User registered
✅ login           - Successful login
❌ failed_login    - Failed login attempt
🚪 logout          - User logged out
🔐 password_changed - Password changed
🔄 password_reset_requested - Password reset requested
📧 email_verified  - Email verified
🔒 2fa_enabled     - 2FA enabled
🔓 2fa_disabled    - 2FA disabled
🔗 oauth_signup    - OAuth signup
🔗 oauth_linked    - OAuth account linked
✂️ oauth_unlinked  - OAuth account unlinked
```

---

## Database Verification

### Check Email Verification Tokens
```bash
cd Backend
php artisan tinker
```

```php
DB::table('email_verification_tokens')->get();
DB::table('users')->where('id', 1)->first();
```

### Check Activity Logs
```php
DB::table('activity_logs')->latest()->get();
DB::table('activity_logs')->where('action', 'login')->get();
```

### Check 2FA Status
```php
DB::table('users')->where('id', 1)->select('two_factor_enabled')->first();
DB::table('two_factor_codes')->latest()->get();
```

### Check OAuth Accounts
```php
DB::table('oauth_accounts')->get();
```

---

## Troubleshooting

### "Token has expired" or "Token invalid"
- Generate new token by logging in again
- Check token hasn't been revoked

### Email verification token not working
- Check token exists in database: `php artisan tinker` → `DB::table('email_verification_tokens')->get()`
- Check token hasn't been marked as used
- Verify it hasn't expired (created_at + 24 hours)

### 2FA code rejected
- Use exact code from response (6 digits)
- Code is valid for 5 minutes only
- Code can only be used once

### Activity not logging
- Check ActivityLogService is being called
- Verify `activity_logs` table exists
- Check user_id is being set correctly

### Password reset fails
- Token must be valid (not used, not expired)
- Password must meet requirements (8+ chars, mixed case, numbers, symbols)
- confirmation field must match password field

---

## Load Testing

After verifying all features work, you can stress test:

```bash
# Create 100 test users
for i in {1..100}; do
  curl -X POST http://localhost:8000/api/v1/auth/register \
    -H "Content-Type: application/json" \
    -d "{\"name\": \"User $i\", \"email\": \"user$i@test.com\", \"password\": \"TestPass$i!\", \"password_confirmation\": \"TestPass$i!\"}"
done
```

---

## Production Checklist

- [ ] Email service configured (SendGrid, MailTrap, etc.)
- [ ] HTTPS enabled
- [ ] Rate limiting added
- [ ] CORS properly configured
- [ ] OAuth API keys configured
- [ ] Database backups enabled
- [ ] Error logging configured
- [ ] Monitoring alerts set up
- [ ] Security headers added
- [ ] Session timeout configured

