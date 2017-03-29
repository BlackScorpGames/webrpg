# webrpg
a simple Online RPG for PHP newbies. The goal of the project is to see if it is possible to create an RPG with simple sourcecode.


# installation

1. download the script from github
2. unzip eg in C:\webrpg
3. a) start php built-in [webserver](http://php.net/features.commandline.webserver)

```php -S localhost:8080 -t C:\webrpg\public```

or define the folder public as document root

 b) change the path to your local php.exe in local-server.bat and run the local-server.bat

4. copy config/database.example.php to config/database.php 
5. create a database and import the install.sql
6. apply the database configs
7. open http://localhost:8080 click on "create dummy user" button
8. login

#contribute

Please check the [issues](https://github.com/BlackScorp/webrpg/issues?q=is%3Aissue+is%3Aopen+label%3A%22help+wanted%22) 