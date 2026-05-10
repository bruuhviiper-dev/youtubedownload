web: php artisan serve --host=0.0.0.0 --port=$PORT
worker: php artisan queue:work --tries=3 --timeout=300
release: php artisan migrate --force && php artisan downloads:clean
