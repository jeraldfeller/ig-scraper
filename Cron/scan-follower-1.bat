@ECHO OFF
:start
php C:\wamp64/www/upwork/oscar/ig-scraper/Cron/scan-follower-1.php
timeout 1 >nul
goto start


