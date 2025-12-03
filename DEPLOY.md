# 🚀 Quick Deployment Reference

## Files Created for Deployment

- ✅ `Dockerfile` - PHP 8.2 Apache image
- ✅ `docker-compose.yml` - App + MySQL services with Traefik
- ✅ `.env.example` - Environment variables template
- ✅ `.dockerignore` - Exclude files from build
- ✅ `config.php` - Updated to use environment variables

## Code Changes Summary

Your app now works in **both** environments:

**Local (XAMPP):**
- Still uses `localhost`, `root`, empty password
- No changes needed to test locally!

**Production (Docker):**
- Reads from environment variables
- Database host is `db` (container name)
- Secure passwords from `.env` file

---

## 3-Step Quick Deploy

### 1️⃣ Build & Push (Local Machine)

```powershell
cd c:\xampp\htdocs\todo-list
docker build -t YOUR_DOCKERHUB_USERNAME/todolist-php:latest .
docker login
docker push YOUR_DOCKERHUB_USERNAME/todolist-php:latest
```

### 2️⃣ Setup Server

```bash
# SSH to your server
mkdir -p /docker/todo
cd /docker/todo

# Create .env file
nano .env
```

Paste this in `.env`:
```env
DB_NAME=todolist_db
DB_USER=todouser
DB_PASSWORD=YourSecurePassword123!
DB_ROOT_PASSWORD=YourRootPassword456!
```

Upload `docker-compose.yml` and `database.sql` to `/docker/todo/`

Edit `docker-compose.yml` - change line 8:
```yaml
build: .  # ← Remove this
image: YOUR_DOCKERHUB_USERNAME/todolist-php:latest  # ← Add this
```

### 3️⃣ Start

```bash
cd /docker/todo
docker compose up -d
docker compose logs -f  # Watch startup
```

---

## Cloudflare Tunnel Setup

In Cloudflare dashboard → Add route:
- **Subdomain:** `todo`
- **Domain:** `novn.my.id`  
- **Service:** Points to Traefik (`https://traefik:443`)

---

## Useful Commands

**Check status:**
```bash
docker compose ps
docker compose logs app
docker compose logs db
```

**Restart app:**
```bash
docker compose restart app
```

**Stop everything:**
```bash
docker compose down
```

**Update app after code changes:**
```bash
# Local: rebuild and push
docker build -t YOUR_USERNAME/todolist-php:latest .
docker push YOUR_USERNAME/todolist-php:latest

# Server: pull and restart
docker compose pull app
docker compose up -d app
```

**Database backup:**
```bash
docker exec todolist-db mysqldump -utodouser -p todolist_db > backup.sql
```

---

## Verify It Works

1. Go to `https://todo.novn.my.id`
2. Should see landing page
3. Login with: `admin` / `TomatoPotato01!`
4. Create a todo item
5. Refresh page - todo should persist

---

## Troubleshooting

**Can't access website:**
- Check Traefik dashboard: `https://traefik.novn.my.id`
- Look for `todolist` router
- Check Cloudflare tunnel routes

**Database errors:**
- Verify `.env` passwords are correct
- Check DB is healthy: `docker compose ps`
- View logs: `docker compose logs db`

**Need to rebuild:**
- Build → Push → Pull → Restart (see commands above)

---

## Docker Network Architecture

```
External:
  ┌─ proxy (connects to Traefik)
  
Internal:
  └─ internal (app ↔ database only)
```

Your app connects to **both networks**:
- `proxy` - So Traefik can route to it
- `internal` - To talk to MySQL securely
