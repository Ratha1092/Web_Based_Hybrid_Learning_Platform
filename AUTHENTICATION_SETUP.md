# Authentication Setup Guide - Production Level

## Overview

This document outlines the production-level authentication system implemented for the Hybrid Learning Platform.

## Frontend Architecture

### 1. **Auth Service** (`services/auth.ts`)
- Handles all API calls for authentication
- Functions:
  - `register(name, email, password, password_confirmation)` - Register new user
  - `login(email, password)` - Login user
  - `logout()` - Logout user
  - `getMe()` - Fetch current user profile

### 2. **Auth Context** (`src/context/AuthContext.tsx`)
- Manages global authentication state
- Provides `useAuth()` hook for accessing auth data anywhere in the app
- Auto-initializes user from localStorage on page load
- Validates tokens and handles token expiry

**Usage:**
```tsx
import { useAuth } from "@/src/context/AuthContext";

export function MyComponent() {
  const { user, token, isAuthenticated, logout } = useAuth();
  
  if (!isAuthenticated) {
    return <div>Not logged in</div>;
  }
  
  return <div>Welcome {user?.name}</div>;
}
```

### 3. **Protected Routes** (`src/components/ProtectedRoute.tsx`)
- Wraps components that require authentication
- Redirects to login if not authenticated
- Supports role-based access control

**Usage:**
```tsx
import { ProtectedRoute } from "@/src/components/ProtectedRoute";

export default function AdminPage() {
  return (
    <ProtectedRoute requiredRole="admin">
      <div>Admin content</div>
    </ProtectedRoute>
  );
}
```

### 4. **API Client** (`src/lib/api.ts`)
- Automatically includes Bearer token in all requests
- Handles 401 errors (token expiry)
- Base URL: `http://localhost:8000/api/v1`

## Backend Architecture

### 1. **Auth Controller** (`Backend/app/Http/Controllers/Api/AuthController.php`)

#### Register Endpoint
- **Route:** `POST /v1/auth/register`
- **Validation:**
  - Name: required, string, max 255, letters & spaces only
  - Email: required, email, unique
  - Password: required, confirmed, min 8, must have uppercase, lowercase, numbers, symbols
- **Returns:** Token and user data
- **Status:** 201 Created

#### Login Endpoint
- **Route:** `POST /v1/auth/login`
- **Validation:**
  - Email: required, email format
  - Password: required
- **Returns:** Token and user data
- **Status:** 200 OK

#### Logout Endpoint
- **Route:** `POST /v1/auth/logout` (requires auth:sanctum)
- **Action:** Revokes current access token
- **Returns:** Success message

### 2. **Security Features**

#### Password Requirements
- Minimum 8 characters
- Must include uppercase letters
- Must include lowercase letters
- Must include numbers
- Must include symbols
- Must be confirmed (password_confirmation field)

#### Token Management
- Uses Laravel Sanctum for API token generation
- Tokens stored in `personal_access_tokens` table
- Each login generates a new token
- Option to revoke old tokens (see commented code in AuthController)

#### Data Protection
- Sensitive fields never exposed in API responses
- User transformation prevents leaking passwords, verification tokens, etc.
- Case-insensitive email lookup
- Generic error messages (don't reveal if email exists)

## Usage Examples

### Frontend - Register
```tsx
import { register } from "@/services/auth";

const handleRegister = async () => {
  try {
    const res = await register("John Doe", "john@example.com", "Pass123!@", "Pass123!@");
    // User is registered and logged in
    localStorage.setItem("token", res.data.token);
  } catch (err) {
    console.error(err.message);
  }
};
```

### Frontend - Login
```tsx
import { login } from "@/services/auth";

const handleLogin = async () => {
  try {
    const res = await login("john@example.com", "Pass123!@");
    localStorage.setItem("token", res.data.token);
  } catch (err) {
    console.error(err.message);
  }
};
```

### Frontend - Protected Component
```tsx
import { useAuth } from "@/src/context/AuthContext";

export function UserGreeting() {
  const { user, isAuthenticated } = useAuth();
  
  if (!isAuthenticated) return <div>Please login</div>;
  return <div>Hello {user?.name}!</div>;
}
```

### Backend - Protected Route
```php
// In routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    // Your protected routes here
    Route::get('/profile', [ProfileController::class, 'show']);
});
```

## API Response Format

### Success Response
```json
{
  "success": true,
  "data": {
    "token": "...",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "student",
      "created_at": "2024-05-01T10:00:00Z"
    }
  },
  "message": "Login successful"
}
```

### Error Response
```json
{
  "success": false,
  "data": null,
  "message": "Invalid credentials",
  "status": 401
}
```

## Environment Setup

### Frontend
1. Ensure `.env.local` has correct API URL:
```
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

2. Wrap app with AuthProvider in `src/app/layout.tsx`

### Backend
1. Ensure Laravel Sanctum is configured in `config/sanctum.php`
2. Database must have migration for `personal_access_tokens` table
3. User model must have `HasApiTokens` trait

## Testing

### Test Registration
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "Pass123!@",
    "password_confirmation": "Pass123!@"
  }'
```

### Test Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "Pass123!@"
  }'
```

### Test Protected Route
```bash
curl -X GET http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer TOKEN_HERE"
```

## Security Best Practices

✅ **Implemented:**
- Password hashing with bcrypt
- Token-based authentication (Sanctum)
- CORS protection
- Input validation
- Sanitized error messages
- No sensitive data in responses
- HTTPOnly cookies support (optional)
- Rate limiting ready (via middleware)

⚠️ **Recommendations:**
1. Enable HTTPS in production
2. Set secure CORS headers
3. Implement rate limiting on auth endpoints
4. Add email verification for registration
5. Add password reset functionality
6. Add 2FA for sensitive operations
7. Monitor failed login attempts
8. Log authentication events
9. Use environment variables for secrets
10. Regularly rotate API tokens

## Troubleshooting

### Issue: "CORS error"
- Check `config/cors.php` in Laravel
- Ensure frontend URL is in allowed origins

### Issue: "Invalid token"
- Token may have expired
- Check token is being sent in `Authorization: Bearer` header
- Verify token hasn't been revoked

### Issue: "Email already registered"
- Use different email or check database directly
- Reset test data if needed

### Issue: "Password doesn't meet requirements"
- Password must have: uppercase, lowercase, numbers, symbols
- Minimum 8 characters

## Next Steps

1. ✅ Basic auth system is ready
2. Add email verification
3. Add password reset functionality
4. Add 2FA support
5. Add OAuth providers (Google, GitHub)
6. Add session management UI
7. Add password change functionality
8. Add user profile management

