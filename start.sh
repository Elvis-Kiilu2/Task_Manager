#!/bin/bash

# Initialize MySQL
echo "Initializing MySQL..."
mkdir -p /var/lib/mysql /run/mysqld
chown -R mysql:mysql /var/lib/mysql /run/mysqld
mysqld --initialize-insecure --user=mysql --datadir=/var/lib/mysql

# Start MySQL in the background
echo "Starting MySQL..."
mysqld --user=mysql --datadir=/var/lib/mysql &

# Wait for MySQL to start
echo "Waiting for MySQL to start..."
sleep 10

# Create the database and configure root to allow password login
echo "Configuring MySQL permissions..."
mysql -e "CREATE DATABASE IF NOT EXISTS task_manager;"
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED BY 'root';"
mysql -e "CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY 'root';"
mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;"
mysql -e "FLUSH PRIVILEGES;"

# Run migrations and seeders
echo "Running migrations and seeders..."
php artisan migrate --force
php artisan db:seed --force

# Start Laravel
echo "Starting Laravel server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
