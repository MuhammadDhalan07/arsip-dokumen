# Deployment Guide

Complete guide for deploying this Filament v4 Laravel 12 application to production environments.

## 📋 Table of Contents

- [Production Checklist](#production-checklist)
- [Server Requirements](#server-requirements)
- [Deployment Methods](#deployment-methods)
- [Environment Configuration](#environment-configuration)
- [Database Setup](#database-setup)
- [Web Server Configuration](#web-server-configuration)
- [Security Hardening](#security-hardening)
- [Performance Optimization](#performance-optimization)
- [Monitoring & Logging](#monitoring--logging)
- [Maintenance](#maintenance)

## Production Checklist

### Pre-Deployment

- [ ] Server meets minimum requirements
- [ ] SSL certificate obtained and configured
- [ ] Database server setup and secured
- [ ] Domain/subdomain configured
- [ ] Environment variables configured
- [ ] Asset compilation completed
- [ ] Database migrations tested
- [ ] Backup strategy implemented

### Post-Deployment

- [ ] Application accessible via HTTPS
- [ ] Admin user created
- [ ] Permissions configured
- [ ] Email functionality tested
- [ ] Queue workers running
- [ ] Monitoring tools configured
- [ ] Backup verification
- [ ] Performance testing completed

## Server Requirements

### Minimum Requirements

- **PHP**: 8.3+ with required extensions
- **Web Server**: Nginx 1.18+ or Apache 2.4+
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Memory**: 2GB RAM minimum, 4GB recommended
- **Storage**: 10GB minimum, SSD recommended
- **SSL**: Valid SSL certificate

### PHP Extensions

```bash
# Required extensions
php8.3-cli
php8.3-fpm
php8.3-mysql          # or php8.3-pgsql for PostgreSQL
php8.3-curl
php8.3-json
php8.3-mbstring
php8.3-xml
php8.3-zip
php8.3-bcmath
php8.3-gd
php8.3-intl
php8.3-redis          # for caching/sessions
```

### Ubuntu/Debian Installation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.3 php8.3-fpm php8.3-cli php8.3-mysql php8.3-curl \
    php8.3-json php8.3-mbstring php8.3-xml php8.3-zip php8.3-bcmath \
    php8.3-gd php8.3-intl php8.3-redis

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Nginx
sudo apt install nginx

# Install MySQL
sudo apt install mysql-server
sudo mysql_secure_installation
```

## Deployment Methods

### Method 1: Manual Deployment

#### 1. Server Setup

```bash
# Create application directory
sudo mkdir -p /var/www/filament-starter
sudo chown $USER:www-data /var/www/filament-starter

# Clone repository
cd /var/www/filament-starter
git clone <repository-url> .

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install --production
npm run build
```

#### 2. Set Permissions

```bash
# Set proper ownership
sudo chown -R $USER:www-data /var/www/filament-starter

# Set directory permissions
find /var/www/filament-starter -type d -exec chmod 755 {} \;
find /var/www/filament-starter -type f -exec chmod 644 {} \;

# Set writable directories
sudo chmod -R 775 /var/www/filament-starter/storage
sudo chmod -R 775 /var/www/filament-starter/bootstrap/cache
```

### Method 2: Using Laravel Forge

Laravel Forge provides automated deployment and server management.

#### Setup Steps

1. **Connect Server**: Link your VPS to Forge
2. **Create Site**: Configure domain and repository
3. **Set Environment**: Configure production variables
4. **Deploy Script**: Customize deployment script
5. **SSL Certificate**: Enable automatic SSL renewal

#### Custom Deploy Script

```bash
cd /home/forge/filament-starter
git pull origin main

$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

if [ -f artisan ]; then
    $FORGE_PHP artisan migrate --force
    $FORGE_PHP artisan config:cache
    $FORGE_PHP artisan route:cache
    $FORGE_PHP artisan view:cache
    $FORGE_PHP artisan queue:restart
fi

npm ci --production
npm run build
```

### Method 3: Docker Deployment

#### Dockerfile

```dockerfile
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql bcmath gd xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader
RUN npm install --production && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

#### Docker Compose

```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: filament-app
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - filament-network

  nginx:
    image: nginx:alpine
    container_name: filament-nginx
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
      - ./docker/ssl:/etc/ssl/certs
    networks:
      - filament-network

  mysql:
    image: mysql:8.0
    container_name: filament-mysql
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: filament_starter
      MYSQL_USER: filament
      MYSQL_PASSWORD: secret
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - filament-network

  redis:
    image: redis:alpine
    container_name: filament-redis
    networks:
      - filament-network

networks:
  filament-network:
    driver: bridge

volumes:
  mysql_data:
```

## Environment Configuration

### Production .env

```env
# Application
APP_NAME="Your App Name"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-app-key
APP_URL=https://yourdomain.com
APP_TIMEZONE=UTC

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password

# Cache & Session
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your-mail-server
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Security
BCRYPT_ROUNDS=12
```

### Environment Security

```bash
# Set proper permissions
chmod 600 .env

# Never commit .env to version control
echo ".env" >> .gitignore
```

## Database Setup

### MySQL Configuration

#### 1. Create Database and User

```sql
-- Connect as root
mysql -u root -p

-- Create database
CREATE DATABASE filament_starter CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'filament'@'localhost' IDENTIFIED BY 'secure_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON filament_starter.* TO 'filament'@'localhost';
FLUSH PRIVILEGES;

-- Exit
EXIT;
```

#### 2. Optimize MySQL Configuration

```ini
# /etc/mysql/mysql.conf.d/mysqld.cnf

[mysqld]
# Performance
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_file_per_table = 1

# Security
bind-address = 127.0.0.1
skip-networking = false
max_connections = 100

# Character set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
```

### PostgreSQL Configuration

#### 1. Create Database and User

```sql
-- Connect as postgres user
sudo -u postgres psql

-- Create user
CREATE USER filament WITH PASSWORD 'secure_password';

-- Create database
CREATE DATABASE filament_starter OWNER filament;

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE filament_starter TO filament;

-- Exit
\q
```

### Run Migrations

```bash
# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --force

# Create admin user
php artisan make:user

# Generate permissions
php artisan shield:generate --all
```

## Web Server Configuration

### Nginx Configuration

#### Main Site Configuration

```nginx
# /etc/nginx/sites-available/filament-starter
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/filament-starter/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /path/to/ssl/certificate.pem;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_session_cache shared:SSL:1m;
    ssl_session_timeout 5m;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES128-GCM-SHA256;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    # Handle Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Security - deny access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ /(storage|bootstrap\/cache) {
        deny all;
    }

    # Static file caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|pdf|txt)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Logs
    access_log /var/log/nginx/filament-starter.access.log;
    error_log /var/log/nginx/filament-starter.error.log;
}
```

#### Enable Site

```bash
# Create symbolic link
sudo ln -s /etc/nginx/sites-available/filament-starter /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### Apache Configuration

```apache
# /etc/apache2/sites-available/filament-starter.conf
<VirtualHost *:80>
    ServerName yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/filament-starter/public

    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /path/to/ssl/certificate.pem
    SSLCertificateKeyFile /path/to/ssl/private.key

    # Security Headers
    Header always set X-Frame-Options SAMEORIGIN
    Header always set X-Content-Type-Options nosniff
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"

    # Directory Settings
    <Directory /var/www/filament-starter/public>
        AllowOverride All
        Require all granted
    </Directory>

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/filament-starter_error.log
    CustomLog ${APACHE_LOG_DIR}/filament-starter_access.log combined
</VirtualHost>
```

## Security Hardening

### 1. File Permissions

```bash
# Set correct ownership
sudo chown -R www-data:www-data /var/www/filament-starter

# Set directory permissions
find /var/www/filament-starter -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/filament-starter -type f -exec chmod 644 {} \;

# Make artisan executable
chmod +x /var/www/filament-starter/artisan

# Secure sensitive directories
chmod 600 /var/www/filament-starter/.env
chmod -R 775 /var/www/filament-starter/storage
chmod -R 775 /var/www/filament-starter/bootstrap/cache
```

### 2. Firewall Configuration

```bash
# Install UFW
sudo apt install ufw

# Default policies
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Allow SSH, HTTP, HTTPS
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status verbose
```

### 3. Application Security

```php
// config/app.php - Production settings
'debug' => env('APP_DEBUG', false),
'env' => env('APP_ENV', 'production'),

// Hide server information
'asset_url' => env('ASSET_URL', null),
```

### 4. Database Security

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Configure firewall for database
sudo ufw allow from your_app_server_ip to any port 3306
```

## Performance Optimization

### 1. Application Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Generate optimized class loader
php artisan optimize
```

### 2. Database Optimization

```sql
-- Add indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_created_at ON users(created_at);
CREATE INDEX idx_model_has_roles_model_type_model_id ON model_has_roles(model_type, model_id);
```

### 3. Caching Strategy

#### Redis Configuration

```bash
# Install Redis
sudo apt install redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf
```

```conf
# /etc/redis/redis.conf
bind 127.0.0.1
port 6379
requirepass your_redis_password
maxmemory 512mb
maxmemory-policy allkeys-lru
```

#### Laravel Cache Configuration

```php
// config/cache.php
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
],
```

### 4. Queue Workers

#### Supervisor Configuration

```bash
# Install Supervisor
sudo apt install supervisor

# Create worker configuration
sudo nano /etc/supervisor/conf.d/filament-worker.conf
```

```ini
[program:filament-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/filament-starter/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/filament-starter/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start filament-worker:*
```

## Monitoring & Logging

### 1. Log Management

```bash
# Configure log rotation
sudo nano /etc/logrotate.d/filament-starter
```

```
/var/www/filament-starter/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

### 2. Application Monitoring

#### Health Check Endpoint

```php
// routes/web.php - Already configured in Laravel 12
// Available at /up endpoint
```

#### Custom Health Checks

```bash
# Create monitoring script
sudo nano /usr/local/bin/monitor-filament.sh
```

```bash
#!/bin/bash

# Check if application is responding
if curl -f -s https://yourdomain.com/up > /dev/null; then
    echo "Application is healthy"
else
    echo "Application is down!"
    # Send alert (email, Slack, etc.)
fi

# Check queue status
QUEUE_SIZE=$(php /var/www/filament-starter/artisan queue:monitor | grep -o '[0-9]*' | head -1)
if [ "$QUEUE_SIZE" -gt 100 ]; then
    echo "Queue size is high: $QUEUE_SIZE"
fi
```

### 3. Performance Monitoring

#### Install Laravel Telescope (Optional)

```bash
# Install only for debugging/monitoring
composer require laravel/telescope

# Publish assets
php artisan telescope:install

# Migrate
php artisan migrate
```

## Maintenance

### 1. Backup Strategy

#### Database Backup

```bash
# Create backup script
sudo nano /usr/local/bin/backup-filament-db.sh
```

```bash
#!/bin/bash

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/filament-starter"
DB_NAME="filament_starter"
DB_USER="filament"
DB_PASS="your_password"

mkdir -p $BACKUP_DIR

# MySQL backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete

echo "Database backup completed: db_backup_$DATE.sql.gz"
```

#### File Backup

```bash
# Full application backup
sudo nano /usr/local/bin/backup-filament-files.sh
```

```bash
#!/bin/bash

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/filament-starter"
APP_DIR="/var/www/filament-starter"

mkdir -p $BACKUP_DIR

# Backup storage and uploads
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz -C $APP_DIR storage public/uploads

# Keep only last 30 days
find $BACKUP_DIR -name "files_backup_*.tar.gz" -mtime +30 -delete

echo "File backup completed: files_backup_$DATE.tar.gz"
```

#### Automated Backups

```bash
# Add to crontab
crontab -e
```

```cron
# Daily database backup at 2 AM
0 2 * * * /usr/local/bin/backup-filament-db.sh

# Weekly file backup on Sunday at 3 AM
0 3 * * 0 /usr/local/bin/backup-filament-files.sh
```

### 2. Updates and Maintenance

```bash
# Create maintenance script
sudo nano /usr/local/bin/update-filament.sh
```

```bash
#!/bin/bash

cd /var/www/filament-starter

# Enable maintenance mode
php artisan down

# Pull latest changes
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader

# Update frontend assets
npm ci --production
npm run build

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
php artisan queue:restart

# Disable maintenance mode
php artisan up

echo "Application updated successfully!"
```

### 3. SSL Certificate Renewal

#### Using Let's Encrypt with Certbot

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Test renewal
sudo certbot renew --dry-run

# Auto-renewal is set up automatically
```

---

## Related Documentation

- [Architecture Guide](ARCHITECTURE.md) - System design and patterns
- [Development Guide](DEVELOPMENT.md) - Development workflow
- [API Reference](API.md) - Component reference
- [Main README](../README.md) - Project overview