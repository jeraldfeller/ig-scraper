@ECHO OFF
:start
php C:\wamp64/www/upwork/oscar/ig-scraper/Cron/scan-followed.php
timeout 5 >nul
goto start