# 🎛️ Admin Dashboard - Quick Start Guide

## 🚀 Getting Started in 3 Minutes

### Step 1: Start Backend & Frontend
```bash
# Terminal 1 - Start Laravel Backend
cd Backend
php artisan serve
# Output: Server running at http://localhost:8000

# Terminal 2 - Start Next.js Frontend
cd Frontend
npm run dev
# Output: ▲ Next.js ready on http://localhost:3000
```

---

## 🔑 Creating an Admin Account

### Option A: Via Laravel Artisan (Recommended)

```bash
# Terminal 3 - Create admin user
cd Backend

# Interactive command
php artisan tinker

# In the tinker shell:
>>> use App\Models\User;
>>> User::create([
  'name' => 'Admin User',
  'email' => 'admin@example.com',
  'password' => bcrypt('password123'),
  'role' => 'admin'
])
>>> exit
```

### Option B: Via Database Directly

```bash
# Or insert directly in your database
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) VALUES (
  'Admin User',
  'admin@example.com',
  '$2y$12$...',  // bcrypt('password123')
  'admin',
  NOW(),
  NOW(),
  NOW()
);
```

### Option C: Manual Registration (if enabled)

1. Visit http://localhost:3000/register
2. Register as normal user
3. Update role to `admin` in database manually

---

## 📋 Testing Checklist

### ✅ Admin Access Test

1. **Go to Login Page**
   - URL: http://localhost:3000/login
   - Enter admin email: `admin@example.com`
   - Enter admin password: `password123`
   - Click Login

2. **Verify Redirect to Home**
   - Should redirect to: http://localhost:3000/home
   - Should see "Admin" link in navigation (red, bold)

3. **Click Admin Link**
   - Should navigate to: http://localhost:3000/admin
   - Should see admin dashboard

### ✅ Admin Dashboard Pages Test

#### Main Dashboard (/admin)
- [ ] 6 metric cards display correctly
- [ ] Recent users table shows data
- [ ] Recent activities log visible
- [ ] Quick actions buttons work
- [ ] System status shows all checks
- [ ] Sidebar displays correctly

#### Users Page (/admin/users)
- [ ] User table displays all users
- [ ] Search box filters users by name/email
- [ ] Role filter works (All, Admin, Instructor, Student)
- [ ] Action buttons functional
- [ ] Pagination controls visible

#### Courses Page (/admin/courses)
- [ ] Course table displays all courses
- [ ] Search filters by title/instructor
- [ ] Status filter works (Active, Draft, Archived)
- [ ] Revenue and rating displayed
- [ ] Statistics cards show totals

#### Analytics Page (/admin/analytics)
- [ ] 4 KPI cards display
- [ ] Chart areas visible (placeholders)
- [ ] Top courses section shows data
- [ ] User demographics section displays
- [ ] Engagement metrics visible

#### Settings Page (/admin/settings)
- [ ] All 6 tabs visible and clickable
- [ ] General tab shows form fields
- [ ] Email tab shows SMTP fields
- [ ] Payment tab shows API key fields
- [ ] Security tab shows toggles
- [ ] Notification tab shows checkboxes
- [ ] Backup tab shows buttons
- [ ] Save feedback notification works

### ✅ Non-Admin Access Test

1. **Register as Regular User**
   - URL: http://localhost:3000/register
   - Create account with any name/email
   - Should auto-login

2. **Go to Homepage**
   - URL: http://localhost:3000/home
   - Should see user homepage
   - Should NOT see "Admin" link in navigation

3. **Try Manual Access**
   - Try to access: http://localhost:3000/admin
   - Should redirect to: http://localhost:3000/dashboard
   - Should NOT see admin content

### ✅ Security Tests

- [ ] Non-admin cannot access /admin
- [ ] Non-admin cannot access /admin/users
- [ ] Non-admin cannot access /admin/courses
- [ ] Non-admin cannot access /admin/analytics
- [ ] Non-admin cannot access /admin/settings
- [ ] Logout button works from admin pages
- [ ] User info displays correctly in navbar
- [ ] Navigation links all work

### ✅ Navigation Tests

From Admin Dashboard:
- [ ] Dashboard link → /admin
- [ ] Users link → /admin/users
- [ ] Courses link → /admin/courses
- [ ] Analytics link → /admin/analytics
- [ ] Settings link → /admin/settings

All Quick Actions:
- [ ] Manage Users → /admin/users
- [ ] Manage Courses → /admin/courses
- [ ] View Analytics → /admin/analytics
- [ ] Settings → /admin/settings

Footer Links (should navigate):
- [ ] Platform links work
- [ ] Management links work
- [ ] Settings links work
- [ ] Support links work

---

## 🎯 Sample Test Data

### Admin Account
```
Email: admin@example.com
Password: password123
Role: admin
```

### Regular Student Account (for comparison)
```
Email: student@example.com
Password: password123
Role: student
```

---

## 📊 What You Should See

### Admin Dashboard Main Page
```
⚙️ Admin Console (Header)
Red banner: "📊 Admin Dashboard"
6 Stat Cards:
  - 👥 Total Users: 1,247
  - 📚 Total Courses: 45
  - 💰 Total Revenue: $125.75K
  - 🔴 Active Sessions: 284
  - ✅ System Health: 99.9%
  - ⚡ Server Load: 42%
Recent Users Table
Recent Activities Log
Sidebar with Quick Actions, System Status, Alerts, Admin Info
Professional footer
```

### Users Page
```
Filter section (Search + Role filter)
User table with 6 columns
Action buttons (View, Edit, Delete)
Pagination at bottom
```

### Courses Page
```
Statistics cards (Total, Students, Revenue)
Filter section (Search + Status filter)
Course table with 8 columns
Pagination controls
```

### Analytics Page
```
4 KPI cards
2 Chart placeholders
Top performing courses
User demographics
Engagement metrics
```

### Settings Page
```
6 tabs on the left sidebar
Form fields in main content
Save button at bottom
Settings categories:
  - General
  - Email
  - Payment
  - Security
  - Notifications
  - Backup & Restore
```

---

## 🐛 Troubleshooting

### Issue: Admin link not showing in homepage
**Solution**: 
- Verify user role is "admin" in database
- Check if user is logged in
- Refresh page (F5)
- Clear browser cache

### Issue: Cannot access /admin
**Solution**:
- Verify you're logged in as admin
- Check browser console for errors (F12)
- Ensure role is set to "admin" in database
- Try logging out and back in

### Issue: Tables not showing data
**Solution**:
- Data is hardcoded for now (placeholder)
- Later will connect to API endpoints
- Check browser console for JavaScript errors

### Issue: Build errors
**Solution**:
```bash
# Clear cache and rebuild
cd Frontend
rm -rf .next
npm run build
```

---

## 📱 Testing Responsive Design

### Mobile (375px)
```bash
# Press F12, then Ctrl+Shift+M
# Test on iPhone SE view
# Tables should scroll horizontally
# Navigation should collapse/adapt
```

### Tablet (768px)
```bash
# Set viewport to iPad view
# Should show 2-column layout
# All sections visible and readable
```

### Desktop (1920px)
```bash
# Full screen
# Optimal 3-column layout
# All features visible
```

---

## ✅ Verification Checklist

After testing, verify:

- [x] Admin users can access /admin
- [x] Admin dashboard displays 6 metrics
- [x] All 5 admin pages accessible
- [x] Non-admins cannot access /admin
- [x] Search and filters work
- [x] Navigation links functional
- [x] Logout works properly
- [x] User info displays correctly
- [x] Responsive design works
- [x] Dark theme renders correctly
- [x] Build succeeds without errors
- [x] No console errors

---

## 🎉 Success!

If all tests pass, your admin dashboard is ready!

**Next Steps:**
1. Create more admin accounts as needed
2. Connect real API endpoints for live data
3. Set up email notifications for admins
4. Configure backup system
5. Test with multiple admin users

---

## 📞 Need Help?

**Files to Review:**
- Admin Dashboard: `/frontend/src/app/admin/page.tsx`
- Protected Route: `/frontend/src/components/ProtectedRoute.tsx`
- Auth Context: `/frontend/src/context/AuthContext.tsx`
- Full Guide: `/ADMIN_DASHBOARD_GUIDE.md`

**Quick Commands:**
```bash
# View admin files
ls -la /frontend/src/app/admin/

# Check build status
cd Frontend && npm run build

# Test login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -d "email=admin@example.com&password=password123"
```

