# Quick Login Guide

## 🚀 QUICK FIX - Auto Login (Bypass Login Form)

I've added a temporary auto-login route for testing. Just click these links in your browser:

**Regular Student (Auto-assigned schedule):**
- http://127.0.0.1:8000/test-auto-login/2024-001

**Irregular Student (Manual course selection):**
- http://127.0.0.1:8000/test-auto-login/2024-003

**Other Students:**
- http://127.0.0.1:8000/test-auto-login/2024-002 (Engineering)
- http://127.0.0.1:8000/test-auto-login/2024-004 (Business)

These links will automatically log you in and redirect to the dashboard!

---

## The Issue

You're getting a **419 CSRF Token Mismatch** error. This is a security feature in Laravel that prevents cross-site request forgery attacks.

## Solution

### Option 1: Use the Browser (Recommended)

1. Open your browser and go to: **http://127.0.0.1:8000/student/login**
2. Enter credentials:
   - **Student ID:** `2024-001`
   - **Password:** `password`
3. Click "Login"

The CSRF token is automatically included in the form when you load the page in a browser.

### Option 2: Test Credentials

Try these accounts:

**Regular Student (Auto-assigned schedule):**
- Student ID: `2024-001`
- Password: `password`
- School: Computer Science

**Irregular Student (Manual course selection):**
- Student ID: `2024-003`
- Password: `password`
- School: Computer Science
- Has failed courses: CS101, PE101

### Option 3: Disable CSRF for Testing (NOT RECOMMENDED)

If you need to test via API/curl, you can temporarily disable CSRF protection, but this is NOT recommended for production.

## Common Issues

1. **419 Error** - CSRF token mismatch
   - Solution: Always load the login page in a browser first
   
2. **Session expired** - Clear browser cookies
   - Solution: Clear cookies for localhost:8000

3. **Invalid credentials** - Wrong student ID or password
   - Solution: Use the credentials above exactly as shown

## Server Status

Server is running at: **http://127.0.0.1:8000**

Check if server is running:
```bash
curl http://127.0.0.1:8000/test-auth
```

Should return:
```json
{"message":"Authentication system is working!","student_guard":"Not authenticated","professor_guard":"Not authenticated"}
```
