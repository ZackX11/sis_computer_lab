SIS Q2 - MIDNIGHT CYAN THEME
============================

FILES
-----
index.php                 Home page
signup.php                Student/Faculty registration
login.php                 User ID + password + CAPTCHA
captcha.php               CAPTCHA image generator (requires PHP GD)
verify_otp.php            Six-digit OTP verification; maximum 3 attempts
student_dashboard.php     Student landing page
faculty_dashboard.php     Faculty landing page
logout.php                Secure session logout
config.php                Database and DEMO_MODE configuration
db.php                    PDO database connection
functions.php             Validation, escaping, CSRF and auth helpers
style.css                 Responsive Midnight Cyan theme
script.js                 CAPTCHA refresh and client-side helpers
database.sql              Minimal database and demo master records

SETUP (XAMPP)
-------------
1. Extract this folder into C:\xampp\htdocs\
2. Start Apache and MySQL.
3. Open phpMyAdmin or MySQL command line and import database.sql.
4. Open config.php and set DB_PORT, DB_USER and DB_PASS to match your MySQL.
   DB_PORT is set to 3307. Change it to 3306 if your XAMPP MySQL uses 3306.
5. Ensure PHP GD extension is enabled (required for CAPTCHA image).
6. Visit http://localhost/sis_q2_midnight_cyan/

DEMO ACCOUNTS
-------------
The SQL creates master records only; signup is required before login.
Student ID: StuDemo2026
Faculty ID: FacDemo2026

The IDs above are examples and meet the lab's minimum ID length and character rules.
Create a password meeting the displayed rules during signup.
DEMO_MODE is true in config.php, so the OTP appears on the verification screen.

IMPORTANT NOTES
---------------
- Registration checks that the supplied ID exists in the Student or Faculty master table.
- If using your existing Q1 database, do not blindly replace your existing Student,
  Faculty or Login tables. Back up first, then adapt the schema to include the columns
  this project uses. The Login table needs Stakeholder_Type to distinguish roles.
- This lab brief specifies SHA-256 with a random salt. This implementation follows that
  requirement. For a real production system, use PHP password_hash()/password_verify()
  rather than a fast general-purpose hash.
- OTP delivery is not configured. DEMO_MODE displays it on screen as required by the lab.
  For real deployments, send OTP through a verified email/SMS provider and set DEMO_MODE=false.
- CAPTCHA refresh uses an image request without reloading the login page.
