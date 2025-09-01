# NewMod User Management Panel

A secure PHP-based user management system for game mod distribution.

## ⚠️ Security Notice

This application has been **SECURITY HARDENED** with the following improvements:
- SQL injection protection using prepared statements
- Input validation and sanitization
- CSRF protection
- Rate limiting
- Secure session management
- Environment variable configuration
- Security headers

## 🚀 Railway Deployment (Recommended)

### Prerequisites
1. Railway account (free)
2. GitHub repository
3. MySQL database (Railway provides this)

### Quick Deployment Steps

1. **Go to Railway:**
   - Visit: https://railway.app
   - Sign up with GitHub

2. **Create New Project:**
   - Click "New Project"
   - Select "Deploy from GitHub repo"
   - Connect your repository

3. **Add MySQL Database:**
   - Click "+ New" → "Database" → "MySQL"
   - Railway provides connection details automatically

4. **Configure Environment Variables:**
   ```
   DB_SERVER=${{MySQL.MYSQL_HOST}}
   DB_USER=${{MySQL.MYSQL_USER}}
   DB_PASS=${{MySQL.MYSQL_PASSWORD}}
   DB_NAME=${{MySQL.MYSQL_DATABASE}}
   ```

5. **Deploy:**
   - Railway auto-detects PHP and deploys
   - Your app is live in minutes!

6. **Import Database:**
   - Use Railway's MySQL console
   - Import `newmod fix.sql`
   - Create admin user (see below)

### Database Setup

Run the SQL commands from `newmod fix.sql` to create the required tables:

```sql
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `registered` timestamp NULL DEFAULT NULL,
  `expired` timestamp NULL DEFAULT NULL,
  `uid` varchar(60) DEFAULT NULL,
  `type` text NOT NULL,
  `reseller` text NOT NULL,
  `credits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Default Admin Account

After database setup, create an admin user:
```sql
INSERT INTO `users` (`username`, `password`, `registered`, `expired`, `uid`, `type`, `reseller`, `credits`) 
VALUES ('admin', 'admin123', NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), NULL, 'admin', '', 100000);
```

## 🔧 Configuration

### Environment Variables
- `DB_SERVER`: Database host
- `DB_USER`: Database username  
- `DB_PASS`: Database password
- `DB_NAME`: Database name

### User Types
- `admin`: Full system access
- `reseller`: Can manage their own users
- `cliente`: Basic user access

## 🛡️ Security Features

- **SQL Injection Protection**: All queries use prepared statements
- **Input Validation**: Comprehensive input sanitization
- **CSRF Protection**: Token-based request validation
- **Rate Limiting**: Prevents brute force attacks
- **Secure Sessions**: HTTP-only, secure cookies
- **Security Headers**: XSS, clickjacking protection

## 📁 File Structure

```
├── index.php              # Main admin panel
├── index_reseller.php     # Reseller panel
├── index_cliente.php      # Client panel
├── login.php              # Authentication
├── add-users.php          # User creation
├── edit-users.php         # User management
├── security.php           # Security functions
├── dbconnection.php       # Database connection
├── vercel.json           # Vercel configuration
├── composer.json         # PHP dependencies
└── assets/               # Static assets
```

## 🔍 Security Audit

The following vulnerabilities have been fixed:
- ✅ SQL injection vulnerabilities
- ✅ Hardcoded credentials
- ✅ Missing input validation
- ✅ Session security issues
- ✅ File access vulnerabilities
- ✅ Error disclosure

## 📝 Usage

1. Access the login page
2. Use admin credentials to log in
3. Manage users, resellers, and system settings
4. Monitor user activity and credits

## ⚡ Performance

- Optimized database queries
- Prepared statements for better performance
- Minimal resource usage
- Fast page load times

## 🆘 Support

For issues or questions:
1. Check the security configuration
2. Verify environment variables
3. Review database connection settings
4. Check Vercel deployment logs

## 📄 License

This project is for educational purposes. Use responsibly and in compliance with applicable laws.
