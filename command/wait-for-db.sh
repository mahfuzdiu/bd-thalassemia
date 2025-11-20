#!/bin/bash
set -e

echo "Waiting for MySQL to be ready..."

until mysql -h db -P 3306 -u root -p"" -e "SELECT 1;" >/dev/null 2>&1; do
  echo "Waiting for MySQL..."
  sleep 2
done

echo "MySQL is up!"
