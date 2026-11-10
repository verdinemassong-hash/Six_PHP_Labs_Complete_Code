SIX PHP PRACTICAL LABS — SETUP

Requirements: XAMPP with Apache, MySQL, PHP 8+ and phpMyAdmin.

1. Extract the php_six_labs folder into C:\xampp\htdocs\
2. Start Apache and MySQL in XAMPP.
3. Open http://localhost/phpmyadmin
4. Import database.sql from this folder.
5. Check config.php. Default XAMPP MySQL login is root with blank password.
6. Open http://localhost/php_six_labs/

FILES
config.php: shared PDO connection and secure helper functions.
database.sql: database and six tables.
assets/style.css: shared styling.
lab1 to lab6: each contains its own index.php with the form, validation and prepared INSERT.
uploads: stores uploaded product and support-ticket images.

NOTES
- All forms submit using POST and validate on the server.
- PDO prepared statements are used for database insertion.
- Unique constraints prevent duplicate student emails, product SKUs, and email/workshop event registrations.
- Lab 2 defines a loan as active while status is Active. To mark a returned loan, run:
  UPDATE loans SET status='Returned' WHERE id=1;
- Lab 3 prevents double booking for the same doctor/date/time slot.
- Lab 4 and Lab 6 verify image MIME type and limit files to 2 MB.
- The assignment does not specify exact dropdown options, so sample options are provided. Change them to match your teacher's instructions.
- The sample phone validation allows common local phone formats for labs 3 and 5; Lab 1 uses exactly nine digits as required.

SCREENSHOTS TO TAKE FOR EACH LAB
1. Form before submission.
2. Successful valid submission.
3. Missing required field error.
4. Invalid value error.
5. Duplicate/conflict error where applicable.
6. Database table showing the saved row.

VERIFY RECORDS IN phpMyAdmin > SQL:
SELECT * FROM students;
SELECT * FROM loans;
SELECT * FROM appointments;
SELECT * FROM products;
SELECT * FROM event_registrations;
SELECT * FROM support_tickets;
