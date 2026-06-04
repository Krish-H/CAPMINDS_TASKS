# AJAX Patient Appointment Management System

A robust, real-time healthcare appointment management system built using **Vanilla JavaScript (AJAX/Fetch)** on the frontend and **PHP/MySQL** on the backend. This system allows you to perform full CRUD (Create, Read, Update, Delete) operations without reloading the page.

## 🚀 Features

- **No Page Reloads**: Seamless user experience powered by asynchronous JavaScript `fetch()`.
- **Full CRUD Operations**: Create, Read, Update, and Delete appointments instantly.
- **Real-Time Status Updates**: Change appointment statuses (Pending, Confirmed, Cancelled) on the fly.
- **Advanced Backend Validations**:
  - **Double Booking Prevention**: Strictly prevents double bookings for same doctor on same date and same time slot.
  - **Daily Limits**: Restricts the maximum number of appointments to 10 per day to ensure efficiency.
  - **Business Hours**: Time slots are validated to be strictly between 09:00 AM and 05:00 PM.
  - **Past Dates**: Prevents booking appointments on dates that have already passed.
- **Enhanced Security**: Built-in CSRF (Cross-Site Request Forgery) protection using session tokens.
- **Modern UI**: A newly redesigned responsive, clean, and premium user interface featuring a soft blue palette, modern typography, toast notifications, loading states, and dynamic animations.

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript (ES6+ async/await)
- **Backend**: Core PHP 
- **Database**: MySQL (mysqli)

## 📁 Project Structure

```
ajax_healthcare/
│
├── index.html       # The main UI containing the form and data table
├── app.js           # JavaScript logic handling AJAX fetch requests and DOM manipulation
├── api.php          # RESTful PHP backend handling GET, POST, PUT, DELETE requests
├── config.php       # Database connection setup and session/CSRF initialization
├── database.sql     # SQL schema for setting up the database
└── README.md        # Project documentation
```

## ⚙️ Installation & Setup

Follow these steps to run the project locally:

### 1. Prerequisites
- A local web server like **WAMP**, **XAMPP**, or **MAMP**.
- PHP 7.4 or higher.
- MySQL database.

### 2. Setup the Database
1. Open your database manager (e.g., phpMyAdmin).
2. Create a new database named `clinic_db` (or simply import the provided SQL file).
3. Import the `database.sql` file provided in this repository to automatically create the `appointments` table.

### 3. Configure Database Connection
Open `config.php` and verify your database credentials. 
Update them if your local MySQL setup requires a specific username or password.

```php
$host = 'localhost';
$db   = 'clinic_db';
$user = 'root'; // Adjust your database username (default for WAMP/XAMPP is 'root')
$pass = '';     // Adjust your database password (e.g., 'root', or leave empty '')
```
*(Note: The `config.php` has already been configured to work with the standard WAMP setup).*

### 4. Run the Application
1. Place the `ajax_healthcare` folder into your web server's root directory (`www` for WAMP, `htdocs` for XAMPP).
2. Open your web browser and navigate to:
   ```
   http://localhost/ajax_healthcare/
   ```

## 📝 How to Use

1. **Booking an Appointment**: Fill out the form with valid details on the left side of the page and click "Save Appointment". You'll see a success notification and the table on the right will update instantly.
2. **Editing**: Click the yellow "Edit" button next to any appointment. The form will populate with that patient's data. Modify the details and click "Update Appointment".
3. **Changing Status**: Use the colored dropdown in the "Status" column to instantly change a patient's status. The background color of the status badge will update dynamically.
4. **Deleting**: Click the red "Delete" button. You will be asked to confirm; upon confirming, the record will be erased without reloading the page.

## 🛡️ Security Notes
- **CSRF Token**: A unique CSRF token is generated in `config.php` and embedded as a hidden field in `index.html`. It validates all `POST`, `PUT`, and `DELETE` requests in `api.php`.
- **Prepared Statements**: SQL injection is mitigated using `mysqli` prepared statements for all database queries.
