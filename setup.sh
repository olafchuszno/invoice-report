#!/bin/bash

echo "Waiting for MySQL to start..."
sleep 10

# Create db tables (from create_db file)
echo "Creating database tables..."
php /var/www/html/create_db.php

# Keep the container running
apache2-foreground
