@ECHO OFF
:start
php C:\wamp64/www/upwork/oscar/ig-scraper/Cron/scan-follower-3.php -s ASC -t handle
goto start