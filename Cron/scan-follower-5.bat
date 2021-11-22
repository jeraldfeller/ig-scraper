@ECHO OFF
:start
php C:\wamp64/www/upwork/oscar/ig-scraper/Cron/scan-follower-3.php -s DESC -t handle
goto start