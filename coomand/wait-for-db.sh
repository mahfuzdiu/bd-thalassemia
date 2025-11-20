#!/bin/bash
#until php -r "new PDO('pgsql:host=timescaledb;port=5432;dbname=gps_data','postgres','postgres');"; do
#  echo "Waiting for TimescaleDB..."
#  sleep 2
#done
#
#
#for docker setup and waiting for mysql
until mysqladmin ping -h mysql_host -P 3306 -u root -p'your_password' --silent; do
  echo "Waiting for MySQL..."
  sleep 2
done
