#!/bin/sh

# 1. Garante que a pasta e o banco de dados existem
mkdir -p database
touch database/database.sqlite
chmod a+rx bin/yt-dlp 2>/dev/null || true
php artisan migrate --force

# 2. Liga o Lixeiro em segundo plano (Cron)
php artisan schedule:work &

# 3. Liga o Trabalhador de downloads em segundo plano (Worker)
php artisan queue:work --tries=3 --timeout=300 &

# 4. Liga o Servidor Web em primeiro plano (para o Railway não desligar a máquina)
php artisan serve --host=0.0.0.0 --port=$PORT
