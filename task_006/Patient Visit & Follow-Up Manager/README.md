# Patient Visit & Follow-Up Manager

A robust, modern Patient Visit and Follow-Up Management System built with PHP and MySQL. This system provides a comprehensive module to track patients, manage their clinical visits, and monitor follow-ups dynamically using efficient SQL-based date logic and modern UI design.

## 🚀 Features & Workflow

### 1. User Authentication (Access Control)
- **Login System**: Secure session-based authentication to manage access.
- **Roles**: System includes Admin and User roles configured out of the box.

### 2. Patient Management
- **Add & Manage Patients**: Register new patients with details including DOB, phone, and address.
- **Dynamic Age Calculation**: The system accurately computes patient age (Years and Months) dynamically through SQL without relying on PHP side date functions.

### 3. Visit Logging
- **Track Visits**: Log visits for existing patients including visit date, consultation fees, and lab fees.
- **Assign Follow-Ups**: Set specific due dates for follow-up appointments.
- **Visit History**: View a detailed historical timeline of a patient's visits.

### 4. Advanced Follow-Up Reporting (SQL Driven)
- **Upcoming Follow-Ups**: View all follow-ups due within the next 7 days.
- **Missed & Overdue**: Accurately tracks if a follow-up date has passed *and* no subsequent visit has been logged.
- **SQL Driven Calculations**: Status categories (Upcoming, Missed, Overdue) are calculated entirely via complex SQL logic.

### 5. Financial Summary Reports
- Generate a unified summary displaying aggregated patient metrics such as Total Consultation Fees and Total Lab Fees, allowing administrators to get an overview of operations.

### 6. Modern, Responsive UI
- **Bootstrap 5 UI**: Built with responsive layouts.
- **Glassmorphism Search Bars**: Aesthetically pleasing, pill-shaped filtering bars with smooth focus elevation.
- **Modern Pagination**: Custom-designed circular pagination buttons with distinct active states and hover micro-animations.

## 🛠️ Technology Stack
- **Backend**: PHP (PDO for Database Abstraction)
- **Database**: MySQL (Auto-configured schema on boot)
- **Frontend**: HTML5, CSS3, Bootstrap 5, Bootstrap Icons

## ⚙️ Installation & Setup
1. **Prerequisites**: Ensure you have a web server running PHP and MySQL (e.g., WAMP, XAMPP, or MAMP).
2. **Clone the Repository**: Place the application folder in your server's root directory (e.g., `C:/wamp64/www/`).
3. **Database Configuration**: Open `config/db.php`. Ensure the `$user` and `$pass` variables match your MySQL instance.
   - The system uses an auto-setup script. On the first run, the database `patient_manager_db` and all necessary tables will be created automatically.
   - Dummy data (8 patients and 15 visits) will be automatically seeded so you can test features right away!
4. **Access the App**: Navigate to `http://localhost/Patient Visit & Follow-Up Manager/` in your browser.
5. **Default Credentials**:
   - Username: `admin` | Password: `password123`
   - Username: `user` | Password: `user123`

## 🎨 Theme Customization
Custom CSS logic (such as modern search forms and custom pagination components) is defined centrally in `assets/style.css`.
