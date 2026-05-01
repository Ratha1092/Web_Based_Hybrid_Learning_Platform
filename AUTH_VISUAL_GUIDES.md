# Authentication System - Visual Guides

## 📊 Registration Flow Diagram

```
User fills form
    ↓
Frontend Validation (client-side)
    ├─ Name: letters only, required
    ├─ Email: valid format, required
    ├─ Password: 8+ chars, uppercase, lowercase, numbers, symbols
    └─ Confirm: matches password
    ↓ (all valid)
POST /v1/auth/register
    ↓
Backend Validation (server-side)
    ├─ Check all fields present
    ├─ Check email format
    ├─ Check email not already used
    └─ Check password strength
    ↓ (all valid)
Hash Password (bcrypt)
    ↓
Create User in Database
    ↓
Generate API Token (Sanctum)
    ↓
Return: { token, user }
    ↓
Frontend saves to localStorage
    ↓
Update Auth Context
    ↓
Redirect to Dashboard ✅
```

---

## 🔐 Login Flow Diagram

```
User fills form
    ↓
Frontend Validation
    ├─ Email: valid format, required
    └─ Password: required
    ↓ (valid)
POST /v1/auth/login
    ↓
Backend Validation
    ├─ Find user by email (case-insensitive)
    ├─ Compare password hash
    └─ Generate token if match
    ↓
Verify Password
    ↓ (match)
Generate API Token (Sanctum)
    ↓
Return: { token, user } ✅
    ↓ (no match)
Return: "Invalid credentials" ❌
    ↓
Frontend saves to localStorage
    ↓
Update Auth Context
    ↓
Redirect to Dashboard ✅
```

---

## 🛡️ Protected Route Flow Diagram

```
User navigates to protected page
    ↓
Check localStorage for token
    ↓ (token exists)           (no token)
Request /v1/me               Redirect to Login ❌
    ↓
Backend verifies token
    ↓ (valid)              (invalid/expired)
Return user data           Return 401 ❌
    ↓                       ↓
Load user in context    Redirect to Login ❌
    ↓
Show protected content ✅
```

---

## 🔄 Token Lifecycle Diagram

```
┌─────────────────────────────────────────────┐
│         Token Lifecycle                     │
└─────────────────────────────────────────────┘

1. Generation
   Login/Register → Backend → Generate Token → Return to Frontend

2. Storage
   localStorage.setItem('token', token)

3. Usage
   Every API request:
   Authorization: Bearer TOKEN
        ↓
   Backend validates token
        ↓ (valid)              (invalid)
   Execute request          Return 401 Unauthorized

4. Persistence
   Page refresh → Read from localStorage → Use existing token

5. Revocation
   User clicks Logout → POST /v1/auth/logout → Delete token
        ↓
   localStorage.removeItem('token')
        ↓
   Redirect to Login
```

---

## 🔗 Request/Response Flow Diagram

```
REGISTRATION REQUEST:
┌─────────────────────────────────────────────────────┐
│ POST /v1/auth/register                              │
│ Content-Type: application/json                      │
│                                                     │
│ {                                                   │
│   "name": "John Doe",                               │
│   "email": "john@example.com",                      │
│   "password": "MyPassword123!",                      │
│   "password_confirmation": "MyPassword123!"         │
│ }                                                   │
└─────────────────────────────────────────────────────┘
          ↓
┌─────────────────────────────────────────────────────┐
│ 201 CREATED                                         │
│ Content-Type: application/json                      │
│                                                     │
│ {                                                   │
│   "success": true,                                  │
│   "message": "User registered successfully",        │
│   "data": {                                         │
│     "token": "1|Xxxx...",                           │
│     "user": {                                       │
│       "id": 1,                                      │
│       "name": "John Doe",                           │
│       "email": "john@example.com",                  │
│       "role": "student",                            │
│       "created_at": "2024-05-01T..."                │
│     }                                               │
│   }                                                 │
│ }                                                   │
└─────────────────────────────────────────────────────┘

LOGIN REQUEST:
┌─────────────────────────────────────────────────────┐
│ POST /v1/auth/login                                 │
│ Content-Type: application/json                      │
│                                                     │
│ {                                                   │
│   "email": "john@example.com",                      │
│   "password": "MyPassword123!"                      │
│ }                                                   │
└─────────────────────────────────────────────────────┘
          ↓
┌─────────────────────────────────────────────────────┐
│ 200 OK                                              │
│ Content-Type: application/json                      │
│                                                     │
│ {                                                   │
│   "success": true,                                  │
│   "message": "Login successful",                    │
│   "data": {                                         │
│     "token": "2|Xxxx...",                           │
│     "user": { ... }                                 │
│   }                                                 │
│ }                                                   │
└─────────────────────────────────────────────────────┘

GET ME REQUEST:
┌─────────────────────────────────────────────────────┐
│ GET /v1/me                                          │
│ Authorization: Bearer 2|Xxxx...                     │
│ Content-Type: application/json                      │
└─────────────────────────────────────────────────────┘
          ↓
┌─────────────────────────────────────────────────────┐
│ 200 OK                                              │
│ Content-Type: application/json                      │
│                                                     │
│ {                                                   │
│   "id": 1,                                          │
│   "name": "John Doe",                               │
│   "email": "john@example.com",                      │
│   "role": "student",                                │
│   "created_at": "2024-05-01T..."                    │
│ }                                                   │
└─────────────────────────────────────────────────────┘

ERROR RESPONSE:
┌─────────────────────────────────────────────────────┐
│ 401 UNAUTHORIZED                                    │
│ Content-Type: application/json                      │
│                                                     │
│ {                                                   │
│   "success": false,                                 │
│   "message": "Invalid credentials"                  │
│ }                                                   │
└─────────────────────────────────────────────────────┘
```

---

## 🏗️ Architecture Diagram

```
┌─────────────────────────────────────────────────────────┐
│                    FRONTEND (Next.js)                   │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │         Layout (with AuthProvider)              │   │
│  └─────────────────────────────────────────────────┘   │
│                         ↓                               │
│  ┌─────────────────────────────────────────────────┐   │
│  │           Auth Context                          │   │
│  │  ├─ useAuth() hook                              │   │
│  │  ├─ User state                                  │   │
│  │  ├─ Token state                                 │   │
│  │  └─ Logout function                             │   │
│  └─────────────────────────────────────────────────┘   │
│      ↓              ↓              ↓                    │
│  ┌────────┐   ┌────────┐   ┌──────────────┐            │
│  │ Login  │   │Register│   │ Protected    │            │
│  │ Page   │   │ Page   │   │ Routes       │            │
│  └────────┘   └────────┘   └──────────────┘            │
│      │              │              │                   │
│      └──────────────┴──────────────┘                   │
│             ↓                                           │
│      ┌─────────────────┐                               │
│      │ Auth Service    │                               │
│      │ ├─ register()   │                               │
│      │ ├─ login()      │                               │
│      │ ├─ logout()     │                               │
│      │ └─ getMe()      │                               │
│      └─────────────────┘                               │
│             ↓                                           │
│      ┌─────────────────┐                               │
│      │ API Client      │                               │
│      │ (apiFetch)      │                               │
│      │ ├─ Adds token   │                               │
│      │ ├─ Sets headers │                               │
│      │ └─ Handles CORS │                               │
│      └─────────────────┘                               │
│             ↓                                           │
└──────────────┼─────────────────────────────────────────┘
               │
           HTTP/REST
               │
┌──────────────┼─────────────────────────────────────────┐
│              ↓                                          │
│         ┌─────────────────┐                            │
│         │  BACKEND (Laravel)                           │
│         ├─────────────────┤                            │
│         │ API Routes      │                            │
│         │ /v1/auth/*      │                            │
│         └─────────────────┘                            │
│              ↓                                          │
│         ┌─────────────────┐                            │
│         │ Auth Middleware │                            │
│         │ (Sanctum)       │                            │
│         │ Validates token │                            │
│         └─────────────────┘                            │
│              ↓                                          │
│         ┌─────────────────┐                            │
│         │ AuthController  │                            │
│         │ ├─ register()   │                            │
│         │ ├─ login()      │                            │
│         │ ├─ logout()     │                            │
│         │ └─ transform()  │                            │
│         └─────────────────┘                            │
│              ↓                                          │
│         ┌──────────────────────┐                       │
│         │ User Model           │                       │
│         │ ├─ id                │                       │
│         │ ├─ name              │                       │
│         │ ├─ email             │                       │
│         │ ├─ password (hashed) │                       │
│         │ └─ role              │                       │
│         └──────────────────────┘                       │
│              ↓                                          │
│         ┌──────────────────────┐                       │
│         │ Database             │                       │
│         │ ├─ users table       │                       │
│         │ └─ tokens table      │                       │
│         └──────────────────────┘                       │
│                                                        │
└────────────────────────────────────────────────────────┘
```

---

## 🔄 State Management Diagram

```
LocalStorage
├─ token: "1|Xxxx..."
└─ user: { id, name, email, role }
          ↓
AuthContext (React Context)
├─ user: User object
├─ token: string
├─ isAuthenticated: boolean
├─ loading: boolean
└─ methods: logout(), setUser(), setToken()
          ↓
Components via useAuth()
├─ LoginPage
├─ RegisterPage
├─ Dashboard
├─ ProtectedRoute
└─ Any component needing auth
```

---

## 🚦 Request Headers & Response Codes

### Headers Sent
```
Content-Type: application/json
Authorization: Bearer TOKEN_HERE
```

### Response Codes
```
200 OK           ✅ Success
201 Created      ✅ Resource created
400 Bad Request  ❌ Invalid input
401 Unauthorized ❌ Invalid/missing token
422 Unprocessable Entity ❌ Validation error
500 Internal Server Error ❌ Server error
```

---

## 🎯 Validation Rules

```
NAME
├─ Required: Yes
├─ Format: Letters & spaces only
├─ Min length: 1
└─ Max length: 255

EMAIL
├─ Required: Yes
├─ Format: valid@email.com
├─ Unique: Yes (in database)
└─ Case-insensitive

PASSWORD
├─ Required: Yes
├─ Min length: 8
├─ Uppercase: Required (A-Z)
├─ Lowercase: Required (a-z)
├─ Numbers: Required (0-9)
├─ Symbols: Required (!@#$%^&*)
└─ Confirmation: Required (must match)
```

---

## 📱 UI Component Structure

```
App Layout
├─ AuthProvider
│  └─ AuthContext Consumer
│     ├─ /login
│     │  ├─ Email Input
│     │  ├─ Password Input
│     │  ├─ Error Messages
│     │  ├─ Loading State
│     │  └─ Register Link
│     │
│     ├─ /register
│     │  ├─ Name Input
│     │  ├─ Email Input
│     │  ├─ Password Input
│     │  ├─ Confirm Password Input
│     │  ├─ Error Messages
│     │  ├─ Loading State
│     │  └─ Login Link
│     │
│     └─ /dashboard (Protected)
│        ├─ User Profile Card
│        ├─ Quick Stats Card
│        ├─ Account Settings Card
│        ├─ Logout Button
│        └─ Navigation Menu
```

---

## 🔐 Security Layers

```
Layer 1: Frontend
├─ Real-time form validation
├─ Password strength requirements
├─ Error handling
└─ Secure token storage

Layer 2: Network
├─ HTTPS ready
├─ CORS protection
├─ Authorization header
└─ Content-Type validation

Layer 3: Backend
├─ Input validation
├─ Email uniqueness check
├─ Password hashing (bcrypt)
├─ Token validation (Sanctum)
└─ Generic error messages

Layer 4: Database
├─ Users table
├─ Personal access tokens table
├─ Indexes on email
└─ Foreign keys
```

---

## 📊 Response Time Expectations

```
Registration:     200-500ms  ⚡
Login:            150-300ms  ⚡
Get User:         100-200ms  ⚡
Logout:           100-200ms  ⚡
Validation Error: 10-50ms    ⚡⚡

(Times may vary based on network & server load)
```

---

## 🎓 Data Flow Example: Complete Login Process

```
1. User enters credentials
   ↓
2. Form validates frontend
   ✅ Email valid
   ✅ Password not empty
   ↓
3. API request sent
   POST /v1/auth/login
   {email, password}
   ↓
4. Backend receives request
   ↓
5. Validates input format
   ✅ Email format valid
   ✅ Password provided
   ↓
6. Looks up user by email
   ✅ User found in database
   ↓
7. Compares password hash
   ✅ Password matches
   ↓
8. Generates token
   New token created: "2|Xxxx..."
   ↓
9. Transforms user data
   Returns: {id, name, email, role}
   (No password, no sensitive fields)
   ↓
10. Sends response
    {
      success: true,
      token: "2|Xxxx...",
      user: {...}
    }
    ↓
11. Frontend receives response
    ✅ Saves token to localStorage
    ✅ Saves user to localStorage
    ✅ Updates AuthContext
    ↓
12. UI updates
    ✅ Login form disappears
    ✅ Dashboard shows
    ✅ User profile displays
    ↓
13. Future requests
    All requests include:
    Authorization: Bearer 2|Xxxx...
    ↓
14. Backend verifies token
    ✅ Token valid
    ✅ User authenticated
    ↓
15. Request processed
    Returns protected data
    ↓
    ✅ SUCCESS
```

---

This visual guide should help you understand how all the pieces work together!
