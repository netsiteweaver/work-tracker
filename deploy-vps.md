### VPS (NGINX + PHP-FPM, e.g. Ubuntu)

- **1. Deploy Laravel application**
  - On your server:
    ```bash
    cd /var/www
    git clone <your-repo-url> work-tracker
    cd work-tracker/backend
    composer install --no-dev --optimize-autoloader
    cp .env.example .env   # then edit .env for production DB, APP_URL, etc.
    php artisan key:generate
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache
    npm install && npm run build  # Build frontend assets (CSS/JS)
    ```
  - Set proper permissions:
    ```bash
    chown -R www-data:www-data /var/www/work-tracker/backend
    chmod -R 775 storage bootstrap/cache
    ```

- **2. Configure NGINX**
  - Example NGINX server block:
    ```nginx
    server {
        listen 80;
        listen [::]:80;
        server_name your-domain.com;
        root /var/www/work-tracker/backend/public;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";

        index index.php;

        charset utf-8;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        error_page 404 /index.php;

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ /\.(?!well-known).* {
            deny all;
        }
    }
    ```
  - Enable the site:
    ```bash
    sudo ln -s /etc/nginx/sites-available/work-tracker /etc/nginx/sites-enabled/
    sudo nginx -t
    sudo systemctl reload nginx
    ```

- **3. Configure SSL (optional but recommended)**
  - Using Let's Encrypt:
    ```bash
    sudo certbot --nginx -d your-domain.com
    ```

- **4. Configure PHP-FPM (if needed)**
  - Ensure PHP-FPM is running:
    ```bash
    sudo systemctl status php8.2-fpm
    sudo systemctl enable php8.2-fpm
    sudo systemctl start php8.2-fpm
    ```

- **5. Supervisor / systemd for queues (optional)**
  - If you use Laravel queues, set up Supervisor to run queue workers:
    ```bash
    sudo nano /etc/supervisor/conf.d/work-tracker-worker.conf
    ```
    ```ini
    [program:work-tracker-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /var/www/work-tracker/backend/artisan queue:work --sleep=3 --tries=3 --max-time=3600
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    user=www-data
    numprocs=1
    redirect_stderr=true
    stdout_logfile=/var/www/work-tracker/backend/storage/logs/worker.log
    stopwaitsecs=3600
    ```
    ```bash
    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start work-tracker-worker:*
    ```

## Benefits of Blade-only deployment

- ✅ **Simpler NGINX config** - Standard Laravel setup, no SPA routing needed
- ✅ **Single codebase** - No separate frontend deployment
- ✅ **Better performance** - Server-side rendering
- ✅ **Easier maintenance** - One deployment process
