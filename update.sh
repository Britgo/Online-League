#! /bin/sh

cd /var/www/onlineleague
if [ -f nomatchreminder ]
then exit 0
fi
php reminder.php
