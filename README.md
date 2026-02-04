# Student Enrollment System

A comprehensive Laravel-based student enrollment system with support for both regular and irregular student workflows.

## 🚀 Features

### Core Functionality
- **Dual Authentication System**: Separate login systems for students and professors
- **Student Classification**: Automatic classification of regular vs irregular students
- **Payment Verification**: Payment-based access control for enrollment
- **Real-time Validation**: Live schedule validation with conflict detection
- **Course Management**: Comprehensive course selection and management

### Student Features
- **Regular Enrollment**: Automatic schedule assignment based on school affiliation
- **Irregular Enrollment**: Manual course selection with real-time validation
- **Schedule Validation**: Unit load calculation and conflict detection
- **Petition System**: Submit petitions for failed courses
- **Dashboard**: View enrollment status and selected courses

### Technical Features
- **AJAX Interface**: Seamless course selection without page reloads
- **Responsive Design**: Mobile-friendly interface
- **Comprehensive Testing**: Unit and integration tests
- **Database Seeding**: Pre-populated test data
- **Error Handling**: Graceful error handling and user feedback

## 🛠️ Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL or SQLite
- Node.js and NPM (for frontend assets)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/Theo-retical808/enrollment_system.git
   cd enrollment_system
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**
   - Configure your database connection in `.env`
   - Run migrations:
   ```bash
   php artisan migrate
   ```

6. **Seed the database**
   ```bash
   php artisan db:seed
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## 📊 Test Data

The system comes with pre-seeded test data:

### Test Students
- **Regular Student**: `student001@example.com` / `password`
- **Irregular Student**: `student002@example.com` / `password`

### Test Professor
- **Professor**: `prof001@example.com` / `password`

### Test Courses
- Various courses across different schools
- Courses with prerequisites
- Failed courses for irregular student testing

## 🎯 Usage

### For Students

1. **Login**: Access `/student/login`
2. **Payment**: Complete payment verification if required
3. **Enrollment**: 
   - Regular students: View assigned schedule
   - Irregular students: Select courses manually
4. **Validation**: Real-time feedback on schedule conflicts and unit limits
5. **Submission**: Submit enrollment for professor approval

### For Professors

1. **Login**: Access `/professor/login`
2. **Dashboard**: View pending enrollments for review
3. **Review**: Approve or reject student enrollments

## 🧪 Testing

The system includes comprehensive tests:

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

### Test Coverage
- **Unit Tests**: Service layer validation and business logic
- **Feature Tests**: Controller and integration testing
- **Validation Tests**: Schedule conflict and prerequisite checking

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── Auth/                    # Authentication controllers
│   ├── IrregularEnrollmentController.php
│   ├── RegularEnrollmentController.php
│   └── StudentDashboardController.php
├── Models/                      # Eloquent models
├── Services/                    # Business logic services
└── Http/Middleware/            # Custom middleware

database/
├── migrations/                  # Database schema
├── seeders/                    # Test data seeders
└── factories/                  # Model factories

resources/
├── views/
│   ├── auth/                   # Login pages
│   ├── student/                # Student interface
│   └── layouts/                # Shared layouts
└── js/                         # Frontend JavaScript

tests/
├── Unit/                       # Unit tests
└── Feature/                    # Integration tests
```

## 🔧 Configuration

### Database Configuration
Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=enrollment_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Application Settings
```env
APP_NAME="Student Enrollment System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

## 🚦 API Endpoints

### Student Enrollment
- `GET /student/enrollment/irregular` - Irregular enrollment page
- `POST /student/enrollment/irregular/add-course` - Add course to enrollment
- `POST /student/enrollment/irregular/remove-course` - Remove course
- `GET /student/enrollment/validation/feedback` - Real-time validation

### Authentication
- `POST /student/login` - Student authentication
- `POST /professor/login` - Professor authentication
- `POST /student/logout` - Logout

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify database credentials in `.env`
   - Ensure database server is running
   - Check database exists

2. **Route Not Found**
   - Clear route cache: `php artisan route:clear`
   - Verify web server configuration

3. **JavaScript Errors**
   - Check browser console for errors
   - Ensure frontend assets are built: `npm run build`

4. **Authentication Issues**
   - Clear application cache: `php artisan cache:clear`
   - Verify session configuration

## 📈 Performance

### Optimization Features
- **Database Indexing**: Optimized queries for course search
- **Eager Loading**: Reduced N+1 query problems
- **Caching**: Session and route caching
- **Asset Optimization**: Minified CSS and JavaScript

## 🔒 Security

### Security Features
- **CSRF Protection**: All forms protected against CSRF attacks
- **Input Validation**: Comprehensive input sanitization
- **Authentication Guards**: Separate guards for students and professors
- **Password Hashing**: Secure password storage with bcrypt

## 📝 License

This project is open-source and available under the [MIT License](LICENSE).

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## 📞 Support

For support and questions:
- Create an issue on GitHub
- Check the documentation in `/docs`
- Review the test files for usage examples

## 🎉 Acknowledgments

Built with Laravel 12 and modern web technologies for educational purposes.

---

**Status**: ✅ All enrollment workflows tested and verified (Task 9 Checkpoint completed)