# Update Admin Password to admin123

## ✅ Files Updated

- `database.sql` - Updated admin password hash
- New credentials: **admin** / **admin123**

---

## 🔄 Update Production Database

**On your server (SSH), run this command:**

```bash
# Update admin password in production database
docker exec -it todolist-db mysql -utodouser -ptomato -D todolist_db -e "UPDATE users SET password = '\$2y\$10\$6c6YsakcjKcp6YsNaZZCKuHd3rT8JJN9H8BBB5FnQDvWYwgNchhopW' WHERE username = 'admin';"
```

**Or if admin user doesn't exist yet:**

```bash
# Insert admin user with new password
docker exec -it todolist-db mysql -utodouser -ptomato -D todolist_db -e "INSERT INTO users (username, password, role) VALUES ('admin', '\$2y\$10\$6c6YsakcjKcp6YsNaZZCKuHd3rT8JJN9H8BBB5FnQDvWYwgNchhopW', 'admin') ON DUPLICATE KEY UPDATE password = '\$2y\$10\$6c6YsakcjKcp6YsNaZZCKuHd3rT8JJN9H8BBB5FnQDvWYwgNchhopW';"
```

---

## ✅ Test Login

1. Go to `https://todo.novn.my.id/masuk.php`
2. Login with:
   - **Username:** `admin`
   - **Password:** `admin123`
3. Should redirect to admin panel!

---

## 🔄 For Local Development (XAMPP)

If you also want to update your local database:

```sql
UPDATE users SET password = '$2y$10$6c6YsakcjKcp6YsNaZZCKuHd3rT8JJN9H8BBB5FnQDvWYwgNchhopW' WHERE username = 'admin';
```

Or just drop and recreate the database using the updated `database.sql` file.

---

That's it! Admin password is now **admin123** 🎉
