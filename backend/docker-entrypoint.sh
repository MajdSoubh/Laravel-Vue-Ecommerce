#!/bin/sh

echo "Waiting for db to start ...";
./wait-for.sh db:3306;

echo "Migrating database and seeding";
php artisan migrate --seed --force;

echo "Run reverb websocket";
nohup php artisan reverb:start > reverb.log 2>&1  &

echo "Run apache server";
apache2-foreground;





