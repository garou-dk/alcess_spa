# Git Bash & Database Query Guide

A complete reference for installing Git for Windows and querying your Alcess database on your Hostinger VPS using Git Bash.

---

## Part 1: Installing Git for Windows

### Step 1 — Download Git

1. Open your browser and go to: **https://git-scm.com/download/win**
2. The download should start automatically. If not, click the **"Click here to download manually"** link.
3. Choose the **Standalone Installer** (64-bit).

### Step 2 — Run the Installer

1. Double-click the downloaded `.exe` file.
2. Click **"Yes"** if Windows asks for permission (UAC prompt).

### Step 3 — Accept the License

1. Read through the GNU General Public License.
2. Click **Next**.

### Step 4 — Choose Install Location

1. Leave the default path: `C:\Program Files\Git`
2. Click **Next**.

### Step 5 — Select Components

1. Leave all defaults checked:
   - ✅ Git Bash Here
   - ✅ Git GUI Here
   - ✅ Git LFS
   - ✅ Associate .git* configuration files with the default text editor
   - ✅ Associate .sh files to be run with Bash
2. Click **Next**.

### Step 6 — Start Menu Folder

1. Leave as default (`Git`).
2. Click **Next**.

### Step 7 — Choose the Default Editor ⚠️ IMPORTANT

> **This is where you choose Notepad.**

1. Click the **dropdown** menu.
2. Scroll down and select: **Use Notepad as Git's default editor**
3. Click **Next**.

### Step 8 — Adjusting Your PATH Environment

1. Select: **Git from the command line and also from 3rd-party software** (recommended).
2. Click **Next**.

### Step 9 — Choose SSH Executable

1. Select: **Use bundled OpenSSH**.
2. Click **Next**.

### Step 10 — Choose HTTPS Transport Backend

1. Select: **Use the OpenSSL library**.
2. Click **Next**.

### Step 11 — Line Ending Conversions

1. Select: **Checkout Windows-style, commit Unix-style line endings** (recommended).
2. Click **Next**.

### Step 12 — Terminal Emulator

1. Select: **Use MinTTY (the default terminal of MSYS2)**.
2. Click **Next**.

### Step 13 — Default Behavior of `git pull`

1. Select: **Fast-forward or merge** (default).
2. Click **Next**.

### Step 14 — Credential Helper

1. Select: **Git Credential Manager** (default).
2. Click **Next**.

### Step 15 — Extra Options

1. ✅ Enable file system caching
2. Leave the rest as default.
3. Click **Next**.

### Step 16 — Experimental Options

1. Leave everything **unchecked**.
2. Click **Install**.

### Step 17 — Finish

1. ✅ Check **"Launch Git Bash"** if you want to open it right away.
2. Click **Finish**.

### Step 18 — Verify Installation

1. Open **Git Bash** (search for it in the Start Menu).
2. Type:
   ```bash
   git --version
   ```
3. You should see something like: `git version 2.47.x.windows.x`

---

## Part 2: Connecting to Your VPS via Git Bash

### Connect via SSH

```bash
ssh root@72.61.148.153
```
- Replace `72.61.148.153` with your actual Hostinger VPS IP address.
- Enter your root password when prompted.
- type `yes` if mag ask
- root password `Unfairness#0427`
- You are now inside your VPS terminal.

### Navigate to Your Project

```bash
cd /var/www/alcess
```

---

## Part 3: Database Query Commands

All commands below assume you are already SSH'd into your VPS and inside `/var/www/alcess`.

> **Database:** `alcess_db` | **Engine:** MariaDB
> **Base command format:** `mysql -u root -p alcess_db -e "YOUR_QUERY"`

---

### 3.1 — List All Tables

```bash
mysql -u root -p alcess_db -e "SHOW TABLES;"
```

---

### 3.2 — Show Table Structure (Columns & Types)

**See columns of a specific table:**
```bash
mysql -u root -p alcess_db -e "DESCRIBE orders;"
```

**See columns of another table:**
```bash
mysql -u root -p alcess_db -e "DESCRIBE sales;"
```

> Replace `orders` or `sales` with any table name from the list above.

---

### 3.3 — View ALL Data in a Table

> ⚠️ Be careful with large tables — this returns every row.

```bash
mysql -u root -p alcess_db -e "SELECT * FROM orders;"
```

```bash
mysql -u root -p alcess_db -e "SELECT * FROM sales;"
```

---

### 3.4 — View Data with a Row Limit

**Get the first 10 rows:**
```bash
mysql -u root -p alcess_db -e "SELECT * FROM sales LIMIT 10;"
```

**Get the latest 10 rows (newest first):**
```bash
mysql -u root -p alcess_db -e "SELECT * FROM sales ORDER BY created_at DESC LIMIT 10;"
```

**Get rows 11–20 (pagination):**
```bash
mysql -u root -p alcess_db -e "SELECT * FROM sales ORDER BY created_at DESC LIMIT 10 OFFSET 10;"
```

---

### 3.5 — View Only Specific Columns

```bash
mysql -u root -p alcess_db -e "SELECT sale_id, sale_code, total_amount, created_at FROM sales;"
```

```bash
mysql -u root -p alcess_db -e "SELECT order_id, status, total_amount, created_at FROM orders;"
```

---

### 3.6 — Count Total Rows in a Table

**Single table:**
```bash
mysql -u root -p alcess_db -e "SELECT COUNT(*) AS total FROM orders;"
```

**All tables at once:**
```bash
mysql -u root -p alcess_db -e "
SELECT 'orders' AS tbl, COUNT(*) AS total FROM orders
UNION ALL SELECT 'sales', COUNT(*) FROM sales
UNION ALL SELECT 'product_orders', COUNT(*) FROM product_orders
UNION ALL SELECT 'sale_items', COUNT(*) FROM sale_items
UNION ALL SELECT 'order_notifications', COUNT(*) FROM order_notifications
UNION ALL SELECT 'products', COUNT(*) FROM products
UNION ALL SELECT 'categories', COUNT(*) FROM categories
UNION ALL SELECT 'users', COUNT(*) FROM users
UNION ALL SELECT 'rates', COUNT(*) FROM rates
UNION ALL SELECT 'batches', COUNT(*) FROM batches
UNION ALL SELECT 'batch_products', COUNT(*) FROM batch_products
UNION ALL SELECT 'carts', COUNT(*) FROM carts;
"
```

---

### 3.7 — Filter by Date Range

**Orders within a specific date range:**
```bash
mysql -u root -p alcess_db -e "SELECT order_id, status, total_amount, created_at FROM orders WHERE created_at BETWEEN '2026-02-17' AND '2026-03-30 23:59:59';"
```

**Sales for a specific month (e.g., March 2026):**
```bash
mysql -u root -p alcess_db -e "SELECT sale_id, sale_code, total_amount, created_at FROM sales WHERE created_at BETWEEN '2026-03-01' AND '2026-03-31 23:59:59';"
```

**Records from today:**
```bash
mysql -u root -p alcess_db -e "SELECT * FROM sales WHERE DATE(created_at) = CURDATE();"
```

---

### 3.8 — Filter by Status or Condition

**Orders with a specific status:**
```bash
mysql -u root -p alcess_db -e "SELECT order_id, status, total_amount, created_at FROM orders WHERE status = 'Pending';"
```

**Sales by payment method:**
```bash
mysql -u root -p alcess_db -e "SELECT sale_id, total_amount, payment_method, created_at FROM sales WHERE payment_method = 'Cash';"
```

**Orders above a certain amount:**
```bash
mysql -u root -p alcess_db -e "SELECT order_id, total_amount, created_at FROM orders WHERE total_amount > 1000;"
```

---

### 3.9 — Search for Text (LIKE)

**Find a customer by name:**
```bash
mysql -u root -p alcess_db -e "SELECT sale_id, customer_name, total_amount FROM sales WHERE customer_name LIKE '%Juan%';"
```

**Find an order by tracking number:**
```bash
mysql -u root -p alcess_db -e "SELECT order_id, tracking_number, status FROM orders WHERE tracking_number LIKE '%ABC%';"
```

---

### 3.10 — View a Specific Row by ID

```bash
mysql -u root -p alcess_db -e "SELECT * FROM orders WHERE order_id = 9;"
```

```bash
mysql -u root -p alcess_db -e "SELECT * FROM sales WHERE sale_id = 54;"
```

---

### 3.11 — View Related Data (JOINs)

**Order with its products:**
```bash
mysql -u root -p alcess_db -e "
SELECT o.order_id, o.status, p.product_id, po.quantity, po.price
FROM orders o
JOIN product_orders po ON o.order_id = po.order_id
JOIN products p ON po.product_id = p.product_id
WHERE o.order_id = 9;
"
```

**Sale with its items:**
```bash
mysql -u root -p alcess_db -e "
SELECT s.sale_id, s.sale_code, si.product_id, si.quantity, si.price
FROM sales s
JOIN sale_items si ON s.sale_id = si.sale_id
WHERE s.sale_id = 54;
"
```

**Order with customer info (user):**
```bash
mysql -u root -p alcess_db -e "
SELECT o.order_id, o.status, u.name, u.email, o.created_at
FROM orders o
JOIN users u ON o.user_id = u.user_id;
"
```

---

### 3.12 — Summary / Report Queries

**Total sales amount per day:**
```bash
mysql -u root -p alcess_db -e "
SELECT DATE(created_at) AS sale_date, COUNT(*) AS total_transactions, SUM(total_amount) AS revenue
FROM sales
GROUP BY DATE(created_at)
ORDER BY sale_date DESC;
"
```

**Total orders by status:**
```bash
mysql -u root -p alcess_db -e "
SELECT status, COUNT(*) AS total FROM orders GROUP BY status;
"
```

**Date range summary (earliest & latest per table):**
```bash
mysql -u root -p alcess_db -e "
SELECT 'orders' AS tbl, COUNT(*) AS total, MIN(created_at) AS earliest, MAX(created_at) AS latest FROM orders
UNION ALL SELECT 'sales', COUNT(*), MIN(created_at), MAX(created_at) FROM sales
UNION ALL SELECT 'product_orders', COUNT(*), MIN(created_at), MAX(created_at) FROM product_orders
UNION ALL SELECT 'sale_items', COUNT(*), MIN(created_at), MAX(created_at) FROM sale_items
UNION ALL SELECT 'order_notifications', COUNT(*), MIN(created_at), MAX(created_at) FROM order_notifications;
"
```

---

### 3.13 — View Notifications

**All notifications:**
```bash
mysql -u root -p alcess_db -e "SELECT order_notification_id, notification_type, notification_to, is_read, message, created_at FROM order_notifications ORDER BY created_at DESC;"
```

**Unread notifications only:**
```bash
mysql -u root -p alcess_db -e "SELECT * FROM order_notifications WHERE is_read = 0 ORDER BY created_at DESC;"
```

---

### 3.14 — View Users

```bash
mysql -u root -p alcess_db -e "SELECT user_id, name, email, created_at FROM users;"
```

---

## Part 4: Quick Reference Cheat Sheet

| What you want | Command |
|---|---|
| List all tables | `SHOW TABLES;` |
| See table columns | `DESCRIBE table_name;` |
| See all rows | `SELECT * FROM table_name;` |
| See latest 10 | `SELECT * FROM table_name ORDER BY created_at DESC LIMIT 10;` |
| Count rows | `SELECT COUNT(*) FROM table_name;` |
| Filter by date | `WHERE created_at BETWEEN 'start' AND 'end'` |
| Filter by value | `WHERE column = 'value'` |
| Search text | `WHERE column LIKE '%keyword%'` |
| Find by ID | `WHERE primary_key = ID` |
| Join tables | `JOIN table2 ON table1.col = table2.col` |
| Group & count | `GROUP BY column` |

---

## Part 5: Available Tables

| Table | Description | Primary Key |
|---|---|---|
| `users` | Customer & admin accounts | `user_id` |
| `orders` | Online orders (Delivery/Pickup) | `order_id` |
| `product_orders` | Products inside each order | `product_order_id` |
| `sales` | Walk-in / POS transactions | `sale_id` |
| `sale_items` | Products inside each sale | `sale_item_id` |
| `order_notifications` | Order status notifications | `order_notification_id` |
| `products` | Product catalog | `product_id` |
| `categories` | Product categories | `category_id` |
| `rates` | Product reviews/ratings | `rate_id` |
| `rate_likes` | Likes on reviews | `rate_like_id` |
| `rate_images` | Images on reviews | `rate_image_id` |
| `batches` | Inventory batches | `batch_id` |
| `batch_products` | Products in each batch | `batch_product_id` |
| `carts` | Shopping cart items | `cart_id` |
| `addresses` | User saved addresses | `address_id` |
| `app_configurations` | App settings | `id` |

---

> 💡 **Tip:** You can always run `mysql -u root -p alcess_db` without `-e` to enter **interactive mode**, where you can type queries one by one and see results live. Type `exit;` to leave.
