SET PHP_EXE=C:\php7.0\php.exe
SET PORT=80
start %PHP_EXE% -S localhost:%PORT% -t %~dp0public
exit
