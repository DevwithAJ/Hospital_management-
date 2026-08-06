# Hospital Management System (PHP & MySQL)

A simple **Hospital Management System (HMS)** built using **PHP, MySQL, HTML, CSS, and JavaScript**. This project is designed for learning and small-scale hospital/clinic management. It includes **Admin** and **Staff (Doctor/Compounder)** roles with patient management and prescription features.

---

## 🚀 Features

### 👨‍💼 Admin Module

* Admin registration & login
* Secure authentication (session-based)
* Dashboard overview
* Add & view patients
* View patient details
* Manage staff access
* Logout functionality

### 👨‍⚕️ Staff Module (Doctor / Compounder)

* Staff login & logout
* Doctor dashboard
* Compounder dashboard
* View assigned patients
* Create and view prescriptions

### 🗂 Common Features

* MySQL database integration
* Role-based access control
* Clean and simple UI
* Easy to deploy on local or live server

---

## 🛠 Tech Stack

* **Frontend:** HTML, CSS, JavaScript
* **Backend:** PHP (Core PHP)
* **Database:** MySQL
* **Server:** Apache (XAMPP / WAMP / LAMP)

---

## 📁 Project Structure

```
hospital_mg/
│
├── admin/
│   ├── dashboard.php
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── forgot.php
│   ├── reset.php
│   ├── view_patient.php
│   └── db.php
│
├── staff/
│   ├── doctor_dashboard.php
│   ├── compounder_dashboard.php
│   ├── doctor_prescription.php
│   ├── login.php
│   ├── logout.php
│   └── db.php
│
├── add_patient.php
├── db.sql
└── README.md
```

---

## ⚙️ Installation & Setup

### 1️⃣ Prerequisites

* XAMPP / WAMP / LAMP installed
* PHP 7.x or above
* MySQL

### 2️⃣ Project Setup

1. Extract the project zip file
2. Copy the project folder to:

   ```
   C:/xampp/htdocs/
   ```
3. Start **Apache** and **MySQL** from XAMPP Control Panel

### 3️⃣ Database Setup

1. Open browser and go to:

   ```
   http://localhost/phpmyadmin
   ```
2. Create a new database (example: `hospital_db`)
3. Import the file:

   ```
   db.sql
   ```
4. Update database credentials in:

   * `admin/db.php`
   * `staff/db.php`

```php
$conn = mysqli_connect("localhost", "root", "", "hospital_db");
```

---

## ▶️ Run the Project

* **Admin Panel:**

  ```
  http://localhost/hospital_mg/admin/login.php
  ```

* **Staff Panel:**

  ```
  http://localhost/hospital_mg/staff/login.php
  ```

---

## 🔐 Security Notes

* This project uses basic PHP sessions
* For production use:

  * Use password hashing (`password_hash()`)
  * Add input validation & prepared statements
  * Implement CSRF protection

---

## 🎓 Use Case

* B.Tech / BCA / MCA Mini Project
* PHP & MySQL Practice Project
* College Submission / Demo

---

## 📌 Future Improvements

* Appointment booking system
* Role management
* PDF prescription download
* Responsive UI (Bootstrap/Tailwind)
* Search & filter patients

---

## 👨‍💻 Author

**Ajit Kumar**
B.Tech (Computer Science) Student
PHP | MySQL | Web Development

---

## 📄 License

This project is for **educational purposes only**.
You are free to modify and use it for learning.

---

⭐ If you like this project, don’t forget to star it!
