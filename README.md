# 💰 Expense Tracker — Full-Stack PHP Application

A high-performance, beautiful, and intuitive personal finance management tool built with a modern **PHP/MySQL** backend and a responsive **Glassmorphic** frontend. Track your daily spending, analyze your habits with interactive charts, and manage your budget with ease.

![Design Preview](https://img.shields.io/badge/Design-Glassmorphism-blueviolet)
![Tech Stack](https://img.shields.io/badge/Stack-PHP%20%7C%20MySQL%20%7C%20VanillaJS-blue)

---

## ✨ Key Features

### 📊 Powerful Dashboard
- **Real-time Overview**: Instantly see your total balance, monthly spending, and top spending category.
- **Dynamic Analytics**: Interactive Doughnut and Bar charts (powered by Chart.js) to visualize spending by category and 7/30-day trends.
- **Budget Tracking**: Set a monthly budget and monitor your progress with an intelligent progress bar that changes color as you approach your limit.

### 📝 Expense Management
- **Smart Entry**: Add expenses with titles, amounts, categories, dates, and optional notes.
- **Quick Edit/Delete**: Easily modify or remove past entries with a smooth, modal-based interface.
- **Search & Filter**: Find specific transactions quickly using the real-time search bar or category filters.
- **CSV Export**: Download your entire expense history as a CSV file for external analysis or backup.

### ⚙️ Profile & Security
- **Email Management**: Update your registered email address at any time.
- **Password Security**: Securely change your password with current password verification.
- **Eye Toggle**: Show/Hide password feature on the login page for better usability.

### 🛡️ Secure & Role-Based
- **Hybrid Authentication**: Secure login system using PHP Sessions and JWT tokens for API calls.
- **Admin Dashboard**: A dedicated space for administrators to monitor platform-wide statistics (Total Users, Total Transactions, Total Spent) and manage user accounts.
- **Session Protection**: All sensitive pages are protected; unauthorized users are automatically redirected to the login page.

### 🎨 Premium UI/UX
- **Improved Navbar**: A professional, sticky navbar with a user profile dropdown and animated transitions.
- **Glassmorphic Design**: A modern, translucent interface with vibrant animated backgrounds.
- **Dark/Light Mode**: Toggle between sleek dark mode and clean light mode with a single click.

---

## 🚀 Installation & Setup

### Prerequisites
- **XAMPP** (or any local server with PHP 7.4+ and MySQL).
- A web browser (Chrome, Firefox, Edge, etc.).

### Steps
1.  **Clone the Project**:
    Move the project folder into your XAMPP directory:
    ```bash
    C:\xampp\htdocs\Expense-Tracker-web-app
    ```
2.  **Database Setup**:
    - Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
    - Create a new database named `expense_tracker`.
    - Import the provided `schema.sql` file into the new database.
3.  **Configuration**:
    - Open `api/db.php` and verify the database credentials (default is `root` with no password).
4.  **Run the App**:
    - Start the Apache and MySQL modules in your XAMPP Control Panel.
    - Visit `http://localhost/Expense-Tracker-web-app` in your browser.

---

## 🔑 Default Credentials

| Role | Username / Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin` or `admin@expense.com` | `Admin@1234` |

---

## 📂 Project Structure

```text
├── api/                # Backend PHP endpoints
│   ├── auth.php        # Authentication middleware
│   ├── db.php          # Database connection
│   ├── expenses.php    # CRUD operations for expenses
│   ├── profile.php     # Profile update logic
│   ├── login.php       # Login handler
│   └── ...             # Other API helpers
├── includes/           # Reusable PHP templates
│   ├── header.php      # Meta tags, CSS, and SVG sprite
│   ├── navbar.php      # Improved Navigation bar
│   └── footer.php      # Scripts and closing tags
├── admin.php           # Admin Dashboard page
├── index.php           # User Dashboard (Main Page)
├── login.php           # Login page with Eye Toggle
├── settings.php        # Profile Settings page
├── app.js              # Main frontend logic
├── admin.js            # Admin dashboard logic
├── settings.js         # Profile management logic
├── style.css           # Global premium styles
└── schema.sql          # Database initialization script
```

---

## 🛠️ Technologies Used

- **Backend**: PHP 7.4+, MySQL (PDO)
- **Frontend**: Vanilla JavaScript (ES6+), HTML5, CSS3
- **Visualization**: [Chart.js](https://www.chartjs.org/)
- **Icons**: [Heroicons](https://heroicons.com/) & [Lucide](https://lucide.dev/) (via SVG)
- **Typography**: [Poppins](https://fonts.google.com/specimen/Poppins) (Google Fonts)

---

## 📜 License
This project is for educational purposes. Feel free to use and modify it as you see fit!
