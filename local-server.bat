@ECHO OFF
:: path to php.exe
SET PHP=C:\php7\php.exe
:: port if different than 80 you have to open http://localhost:<yourPort> in browser
SET PORT=80
:: %~dp0 is the full path where this batch file is located

start %PHP% -S 0.0.0.0:%PORT% -t %~dp0public %~dp0public\index.php
exit