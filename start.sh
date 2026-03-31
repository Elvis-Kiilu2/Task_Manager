#!/bin/sh

# Ensure MariaDB directories exist and have proper permissions
mkdir -p /run/mysqld /var/lib/mysql
chown -R mysql:mysql /run/mysqld /var/lib/mysql

# Initialize MariaDB data directory if empty
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "Initializing MariaDB data directory..."
    mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null
fi

# Start MariaDB in the background
echo "Starting MariaDB..."
/usr/bin/mysqld --user=mysql --datadir=/var/lib/mysql --skip-networking=0 &

# Wait for MariaDB to be ready
echo "Waiting for MariaDB to start..."
timeout=30
while ! mysqladmin ping -h localhost --silent; do
    timeout=$((timeout - 1))
    if [ $timeout -le 0 ]; then
        echo "MariaDB failed to start"
        exit 1
    fi
    sleep 1
done

# Create database and user if they don't exist
echo "Configuring database..."
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE:-task_manager}\`;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USERNAME:-root}'@'localhost' IDENTIFIED BY '${DB_PASSWORD:-root}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_DATABASE:-task_manager}\`.* TO '${DB_USERNAME:-root}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Run Laravel migrations and seeders
echo "Running migrations and seeders..."
php artisan migrate --force
php artisan db:seed --force

# Start Laravel using artisan serve
echo "Starting Laravel server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
