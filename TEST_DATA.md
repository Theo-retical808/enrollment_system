# 🧪 Test Data & Login Credentials

## 🔑 Student Login Credentials

### **Regular Students** (Automatic Schedule Assignment)

| Student ID | Password | Name | School | Year | Type |
|------------|----------|------|--------|------|------|
| `2024-001` | `password` | John Doe | Computer Science | 2nd Year | Regular |
| `2024-002` | `password` | Jane Smith | Engineering | 1st Year | Regular |

**What to expect:**
- ✅ Automatic schedule assignment based on school
- ✅ Pre-defined course templates
- ✅ Read-only schedule display
- ✅ One-click enrollment submission

---

### **Irregular Students** (Manual Course Selection)

| Student ID | Password | Name | School | Year | Type |
|------------|----------|------|--------|------|------|
| `2024-003` | `password` | Bob Wilson | Computer Science | 3rd Year | Irregular |
| `2024-004` | `password` | Alice Brown | Business Administration | 2nd Year | Irregular |

**What to expect:**
- ✅ Manual course browsing and selection
- ✅ Real-time prerequisite validation
- ✅ Course conflict detection
- ✅ Petition system for failed courses
- ✅ Interactive AJAX-powered interface

---

## 🏫 School Information

| Code | Name | Available Programs |
|------|------|-------------------|
| **CS** | College of Computer Science | Programming, Data Structures, Databases, Web Dev |
| **ENG** | College of Engineering | Engineering Drawing, Statics, Dynamics |
| **BUS** | College of Business Administration | Business Intro, Marketing, Finance |
| **AS** | College of Arts and Sciences | Math, English, Physics, Chemistry, History |

---

## 📚 Sample Courses Available

### **Computer Science Courses**
- `CS101` - Introduction to Programming (3 units)
- `CS201` - Data Structures and Algorithms (3 units) *[Requires CS101]*
- `CS202` - Object-Oriented Programming (3 units) *[Requires CS101]*
- `CS301` - Database Systems (3 units) *[Requires CS201]*
- `CS302` - Web Development (3 units) *[Requires CS202]*

### **Engineering Courses**
- `ENGR101` - Engineering Drawing (3 units)
- `ENG201` - Statics (3 units) *[Requires ENGR101]*
- `ENG202` - Dynamics (3 units) *[Requires ENG201]*

### **Business Courses**
- `BUS101` - Introduction to Business (3 units)
- `BUS201` - Marketing Management (3 units) *[Requires BUS101]*
- `BUS202` - Financial Management (3 units) *[Requires BUS101]*

### **General Education**
- `MATH101` - College Algebra (3 units)
- `MATH201` - Calculus I (3 units) *[Requires MATH101]*
- `ENGL101` - English Composition (3 units)
- `ENGL201` - Literature (3 units) *[Requires ENGL101]*
- `PHYS101` - General Physics I (3 units)
- `CHEM101` - General Chemistry (3 units)
- `HIST101` - World History (3 units)
- `PE101` - Physical Education (2 units)
- `ECON101` - Principles of Economics (3 units)
- `ACCT101` - Principles of Accounting (3 units)
- `STAT101` - Statistics (3 units)
- `DRAW101` - Technical Drawing (2 units)

---

## 🎯 Testing Scenarios

### **Scenario 1: Regular Student Workflow**
1. Login with `2024-001` (John Doe - CS Regular)
2. View automatically assigned schedule
3. Review course details and schedule
4. Submit enrollment for approval
5. Check enrollment status

### **Scenario 2: Irregular Student Workflow**
1. Login with `2024-003` (Bob Wilson - CS Irregular)
2. Browse available courses
3. Add courses to enrollment (watch for prerequisites)
4. Try adding conflicting courses (should show validation)
5. Submit petition for failed courses
6. Submit final enrollment

### **Scenario 3: Cross-School Testing**
1. Login with `2024-002` (Jane Smith - Engineering)
2. See engineering-specific course assignments
3. Compare with business student `2024-004`
4. Test different school workflows

### **Scenario 4: Payment System**
1. All test students have **paid enrollment fees**
2. No payment barriers should appear
3. Direct access to enrollment features

---

## 📊 Student Academic History

### **John Doe (2024-001) - Regular**
- ✅ **Completed:** No previous courses (1st time enrollment)
- ✅ **Status:** Good standing
- ✅ **Payment:** Paid for 2nd Semester 2025-2026

### **Jane Smith (2024-002) - Regular**
- ✅ **Completed:** No previous courses (1st time enrollment)
- ✅ **Status:** Good standing
- ✅ **Payment:** Paid for 2nd Semester 2025-2026

### **Bob Wilson (2024-003) - Irregular**
- ✅ **Completed:** MATH101, ENGL101, HIST101 (Grade: B)
- ❌ **Failed:** CS101, PE101 (Grade: F)
- ⚠️ **Status:** Irregular due to failed courses
- ✅ **Payment:** Paid for 2nd Semester 2025-2026

### **Alice Brown (2024-004) - Irregular**
- ✅ **Completed:** ENGL101, MATH101 (Grade: B)
- ❌ **Failed:** BUS101, ECON101 (Grade: F)
- ⚠️ **Status:** Irregular due to failed courses
- ✅ **Payment:** Paid for 2nd Semester 2025-2026

---

## 🔧 System Features to Test

### **Authentication System**
- ✅ Student ID-based login
- ✅ Password validation
- ✅ Session management
- ✅ Multi-guard authentication

### **Student Classification**
- ✅ Regular vs Irregular detection
- ✅ Failed course analysis
- ✅ Workflow routing

### **Payment Verification**
- ✅ Enrollment fee checking
- ✅ Semester-based validation
- ✅ Access control

### **Enrollment Workflows**
- ✅ Regular: Automatic assignment
- ✅ Irregular: Manual selection
- ✅ Prerequisite validation
- ✅ Schedule conflict detection
- ✅ Unit load calculation (21-unit limit)

### **User Interface**
- ✅ Blue color theme
- ✅ Responsive design
- ✅ Real-time AJAX updates
- ✅ Form validation
- ✅ Error handling

---

## 🚀 Quick Test Commands

```bash
# Check student data
C:\xampp\php\php.exe artisan tinker
>>> App\Models\Student::with('school')->get()

# Check payment status
>>> App\Models\Payment::all()

# Check course prerequisites
>>> App\Models\Course::with('prerequisites')->get()

# Reset test data (if needed)
C:\xampp\php\php.exe artisan migrate:fresh --seed
```

---

**🎯 Happy Testing! The system is fully loaded with realistic test data.**