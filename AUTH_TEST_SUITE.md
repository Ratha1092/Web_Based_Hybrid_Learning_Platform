# Authentication System - Test Suite

## Prerequisites

- Backend running on `http://localhost:8000`
- Frontend running on `http://localhost:3000`
- Browser DevTools open (F12)

## Test 1: User Registration

### Frontend Test
1. Navigate to `http://localhost:3000/register`
2. Fill in the form:
   - Name: `Test User`
   - Email: `testuser@example.com` (must be unique)
   - Password: `MyPassword123!`
   - Confirm Password: `MyPassword123!`
3. Click "Register"
4. **Expected Result:**
   - Should redirect to `/dashboard`
   - Token should be in localStorage
   - User info should be displayed

### Backend Test (cURL)
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "testuser@example.com",
    "password": "MyPassword123!",
    "password_confirmation": "MyPassword123!"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "token": "1|...",
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "testuser@example.com",
      "role": "student",
      "created_at": "2024-05-01T10:00:00Z"
    }
  }
}
```

---

## Test 2: User Login

### Frontend Test
1. Clear localStorage (DevTools -> Application -> Storage)
2. Navigate to `http://localhost:3000/login`
3. Enter credentials:
   - Email: `testuser@example.com`
   - Password: `MyPassword123!`
4. Click "Login"
5. **Expected Result:**
   - Should redirect to `/dashboard`
   - Token should be in localStorage
   - User info should be displayed

### Backend Test (cURL)
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "testuser@example.com",
    "password": "MyPassword123!"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "2|...",
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "testuser@example.com",
      "role": "student"
    }
  }
}
```

---

## Test 3: Get Current User

### Frontend Test
1. Login first (Test 2)
2. Check DevTools Console:
```javascript
// In browser console
const token = localStorage.getItem('token');
console.log(token);

// Make request
fetch('http://localhost:8000/api/v1/me', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
}).then(r => r.json()).then(console.log);
```

3. **Expected Result:**
   - Should return user object
   - Status code: 200

### Backend Test (cURL)
```bash
TOKEN="your_token_here"
curl -X GET http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer $TOKEN"
```

**Expected Response:**
```json
{
  "id": 1,
  "name": "Test User",
  "email": "testuser@example.com",
  "role": "student"
}
```

---

## Test 4: Invalid Login

### Frontend Test
1. Navigate to `http://localhost:3000/login`
2. Enter:
   - Email: `testuser@example.com`
   - Password: `WrongPassword123!`
3. Click "Login"
4. **Expected Result:**
   - Error message: "❌ Invalid email or password"
   - Should NOT redirect
   - No token in localStorage

### Backend Test (cURL)
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "testuser@example.com",
    "password": "WrongPassword123!"
  }'
```

**Expected Response (Status: 401):**
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

---

## Test 5: Password Validation

### Test 5a: Weak Password (no symbols)
**Input:** `Password123`

**Expected Error:** Password must contain symbols

### Test 5b: No Numbers
**Input:** `MyPassword!@`

**Expected Error:** Password must contain numbers

### Test 5c: No Uppercase
**Input:** `mypassword123!`

**Expected Error:** Password must contain uppercase

### Test 5d: Too Short
**Input:** `Pass1!`

**Expected Error:** Password must be at least 8 characters

### Test 5e: Valid Password
**Input:** `MyPassword123!`

**Expected:** Registration succeeds

---

## Test 6: Email Validation

### Test 6a: Duplicate Email
**Steps:**
1. Register: `test@example.com`
2. Register again: `test@example.com`

**Expected Error:** "This email is already registered"

### Test 6b: Invalid Email Format
**Input:** `invalidemail`

**Expected Error:** "Invalid email format"

### Test 6c: Valid Email
**Input:** `valid@example.com`

**Expected:** Proceeds to next validation

---

## Test 7: Password Confirmation

### Test 7a: Mismatched Passwords
**Inputs:**
- Password: `MyPassword123!`
- Confirm: `MyPassword456!`

**Expected Error:** "Passwords do not match"

### Test 7b: Matching Passwords
**Inputs:**
- Password: `MyPassword123!`
- Confirm: `MyPassword123!`

**Expected:** Registration succeeds

---

## Test 8: Protected Routes

### Frontend Test
1. Clear localStorage
2. Try to access `http://localhost:3000/dashboard`
3. **Expected Result:**
   - Should redirect to `/login`
   - Show loading state briefly
   - No error message

### With Token Test
1. Login (Test 2)
2. Navigate to `http://localhost:3000/dashboard`
3. **Expected Result:**
   - Should display dashboard
   - Show user profile
   - Logout button works

---

## Test 9: Logout

### Frontend Test
1. Login first (Test 2)
2. Click "Logout" button
3. Confirm logout
4. **Expected Result:**
   - Should redirect to `/login`
   - Token should be removed from localStorage
   - User data should be cleared

### Backend Test (cURL)
```bash
TOKEN="your_token_here"
curl -X POST http://localhost:8000/api/v1/auth/logout \
  -H "Authorization: Bearer $TOKEN"
```

**Expected Response (Status: 200):**
```json
{
  "success": true,
  "message": "Logged out successfully",
  "data": null
}
```

---

## Test 10: Token Persistence

### Test Setup
1. Login successfully (Test 2)
2. Check localStorage:
   ```javascript
   // In browser console
   localStorage.getItem('token')
   localStorage.getItem('user')
   ```

3. **Expected:** Both values should exist

### Test Persistence
1. Refresh page
2. **Expected:**
   - Should not redirect to login
   - Should show dashboard
   - User info should be displayed
   - No loading state (or very brief)

### Test Token Expiry
1. Login
2. Manually delete token from localStorage:
   ```javascript
   localStorage.removeItem('token')
   ```
3. Refresh page
4. **Expected:**
   - Should redirect to login
   - Should clear user info

---

## Test 11: CORS & Network

### Frontend Test
1. Open DevTools -> Network
2. Perform login
3. Check requests:
   - Should see POST to `localhost:8000/api/v1/auth/login`
   - Response headers should have CORS headers
   - Status: 200 OK
4. **Expected:** No CORS errors

### Debug CORS Issues
```bash
# Check if server is running
curl -I http://localhost:8000/api/v1/auth/login

# Check CORS headers
curl -X OPTIONS http://localhost:8000/api/v1/auth/login \
  -H "Origin: http://localhost:3000" \
  -v
```

---

## Test 12: Form Validation (Real-time)

### Frontend Test
1. Navigate to `/register`
2. Type in Name field: `123` (numbers)
3. **Expected:** Show error "Name can only contain letters and spaces"
4. Clear and type: `John Doe`
5. **Expected:** Error disappears

### Test Email Field
1. Type: `invalid`
2. **Expected:** Show error "Invalid email format"
3. Type: `valid@example.com`
4. **Expected:** Error disappears

### Test Password Fields
1. Type: `weak`
2. **Expected:** Show error "Password must be at least 8 characters"
3. Type: `WeakPass123`
4. **Expected:** Show error about symbols
5. Type: `WeakPass123!`
6. **Expected:** Error clears

---

## Browser DevTools Checks

### Check Token Storage
1. Open DevTools
2. Go to Application -> Local Storage
3. Click `http://localhost:3000`
4. Look for keys:
   - `token` - should have JWT-like string
   - `user` - should have JSON object

### Check Network Requests
1. Open DevTools -> Network
2. Filter by XHR
3. Perform login
4. Click on auth request
5. Check:
   - Request Headers: Should have `Authorization: Bearer TOKEN`
   - Response: Should have `success: true`

### Check Console
1. Open DevTools -> Console
2. No red error messages should appear
3. You may see info/warning logs
4. Auth actions should log: "Auth initialized" or similar

---

## Test Checklist

- [ ] Registration with valid data works
- [ ] Registration with duplicate email fails
- [ ] Password validation enforces all rules
- [ ] Login with valid credentials works
- [ ] Login with invalid credentials fails
- [ ] Token is saved to localStorage
- [ ] User info is saved to localStorage
- [ ] Protected routes redirect to login if not authenticated
- [ ] Protected routes work if authenticated
- [ ] Logout clears token and user data
- [ ] Page refresh maintains auth state
- [ ] Token in requests (Authorization header)
- [ ] Form validation shows real-time errors
- [ ] Error messages are clear and helpful
- [ ] No CORS errors in console
- [ ] Redirect to dashboard after successful auth

---

## Common Test Issues & Solutions

### Issue: "Cannot POST /api/v1/auth/register"
**Solution:** Backend not running. Run `php artisan serve` in Backend folder

### Issue: CORS Error
**Solution:** 
- Check Backend/config/cors.php
- Add `http://localhost:3000` to allowed origins
- Run `php artisan config:clear`

### Issue: "Email already registered" always shows
**Solution:** 
- Use different email each test or clear database
- Run `php artisan migrate:fresh --seed` to reset DB

### Issue: Token not persisting after refresh
**Solution:**
- Check browser localStorage is enabled
- Check browser privacy/incognito mode settings
- Check for JavaScript errors in console

### Issue: Redirect loops
**Solution:**
- Clear localStorage
- Check AuthProvider is in layout.tsx
- Check useAuth hook is returning correct values

---

## Performance Benchmarks

### Expected Response Times
- Registration: < 500ms
- Login: < 300ms
- Get Current User: < 200ms
- Logout: < 200ms

### Monitoring
```javascript
// In browser console
performance.mark('auth-start');
// ... perform auth action ...
performance.mark('auth-end');
performance.measure('auth', 'auth-start', 'auth-end');
console.log(performance.getEntriesByName('auth')[0]);
```

