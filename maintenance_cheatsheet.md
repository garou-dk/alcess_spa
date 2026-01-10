# Alcess SPA - Maintenance & Command Cheatsheet

Save this file for future reference. It contains all the commands you need when moving to a new computer or managing your live server.

---

## 1. Setting Up on a New Computer (Local Development)

If you get a new laptop, follow these steps to start coding again.

### **Prerequisites**
Ensure you have installed:
-   **Git** (for version control)
-   **PHP 8.2+** & **Composer**
-   **Node.js (LTS)** & **NPM**
-   **Docker Desktop** (optional, if you want to run Redis/Soketi locally)

### **Step-by-Step Setup**

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/YOUR_GITHUB_USERNAME/alcess_spa.git
    cd alcess_spa
    ```

2.  **Install Backend Dependencies**
    ```bash
    composer install
    ```

3.  **Install Frontend Dependencies**
    ```bash
    npm install
    ```

4.  **Setup Environment File**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *   *Edit `.env` to match your local database settings (e.g., `DB_CONNECTION=sqlite` or `mysql`).*

5.  **Run Migrations (Create Database Tables)**
    ```bash
    php artisan migrate
    ```
    *(If using SQLite, it will ask to create the file. Say Yes).*

6.  **Start the Local Server**
    ```bash
    npm run dev
    # This runs: Laravel Server + Vite + Queue Worker
    ```

---

## 2. Managing the Live Server (VPS)

Use these commands when you are connected to your VPS via SSH (`ssh root@YOUR_IP`).

### **Deploying New Changes**
When you have pushed code from your laptop to GitHub, update the server:

1.  **Go to the directory**
    ```bash
    cd /var/www/alcess
    ```

2.  **Pull the latest code**
    ```bash
    git pull
    ```

3.  **Update Dependencies (if needed)**
    ```bash
    composer install --optimize-autoloader --no-dev
    npm install && npm run build
    ```

4.  **Run Migrations (if database changed)**
    ```bash
    php artisan migrate --force
    ```

5.  **Clear Caches (Important!)**
    ```bash
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

6.  **Restart Queue Workers (Important!)**
    ```bash
    # Tells Supervisor to restart the background workers
    supervisorctl restart all
    ```

### **Troubleshooting & Maintenance**

*   **View Error Logs (last 50 lines)**
    ```bash
    tail -n 50 storage/logs/laravel.log
    ```

*   **Restart Web Server**
    ```bash
    systemctl restart nginx
    ```

*   **Restart Database**
    ```bash
    systemctl restart mariadb
    ```

*   **Check Disk Space**
    ```bash
    df -h
    ```

*   **Fix Permissions (if "Permission Denied" errors occur)**
    ```bash
    chown -R www-data:www-data /var/www/alcess
    chmod -R 775 /var/www/alcess/storage
    chmod -R 775 /var/www/alcess/bootstrap/cache
    ```

---

## 3. Useful Artisan Commands

*   **Create a Link to Storage (for images)**
    ```bash
    php artisan storage:link
    ```

*   **Clear All Caches**
    ```bash
    php artisan optimize:clear
    ```

*   **Enter Interactive PHP Shell**
    ```bash
    php artisan tinker
    ```

*   **Create a New Admin User (via Tinker)**
    ```bash
    php artisan tinker
    # Inside tinker:
    # \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@alcess.online', 'password' => bcrypt('yourpassword')]);
    ```
