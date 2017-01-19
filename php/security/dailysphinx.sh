#!/bin/bash

#Start (lock the cycle update of the delta index until the end of this script)
echo "on" > /usr/local/etc/maintenance_status
printf "Starting maintenance script in 50 seconds...\n";
sleep 50

#Update vote_left
printf "Reset the daily vote quota\n";
php /var/www/html/warez/php/security/dvote.php
sleep 4

#Adjust the stats of topic / post per categorie
printf "Adjust the stats topic/post for each categorie\n";
php /var/www/html/warez/php/security/rebuild_stats.php
sleep 2

#Delete rules WAF
printf "Delete rules WAF and file cache\n";
rm /var/www/html/warez/cache/waf-botcaptcha.html
rm /var/www/html/warez/cache/waf-botreply.html
rm -rf /var/www/html/warez/cache/topcache/12*
rm -rf /var/www/html/warez/cache/topcache/11*
rm -rf /var/www/html/warez/cache/topcache/22*
rm -rf /var/www/html/warez/cache/topcache/21*
rm -rf /var/www/html/warez/cache/pm/*
sleep 2

#Put the web display in maintenance (only static html for avoid any request SQL witch can break the reindex)
printf "Maintenance mode WEB\n";
killall nginx || true
mv /var/www/html/warez /var/www/html/orig
mv /var/www/html/maint /var/www/html/warez
/etc/init.d/php-fpm stop
find /tmp -name "sess*" -mtime +1440 -delete || true
killall -9 php-cgi || true
/etc/init.d/php-fpm start || true
/usr/local/nginx/./nginx || true
sleep 5

#Kill the searchd deamon in process of the reindex and delete all index file used by sphinx (idx / spx...)
printf "Clean index files of Sphinxsearch\n";
killall searchd || true
sleep 3
# Delete every index / file used by sphinxsearch
printf "Clean index files of Sphinxsearch\n";
rm -f /usr/local/var/data/* || true
sleep 2

#Start the reindex of the index
printf "Sphinx rebuild idx...\n";
/usr/local/etc/./fix.sh
sleep2

#Start the deamon sphinx and the delta index (for make sure everything is ok, the delta idx is managed in another script)
printf "Starting searchd deamon\n";
/usr/local/bin/searchd -c /usr/local/etc/sphinx.conf
sleep 1
/usr/local/etc/./idx.sh
sleep 1

#Set off the lock in order to allow the delta index to be reindex every minute and put back the forum in da place
echo "off" > /usr/local/etc/maintenance_status
mv /var/www/html/warez /var/www/html/maint
mv /var/www/html/orig /var/www/html/warez
printf "Automatic maintenance done\n";
