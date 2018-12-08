# Another ToDo List Web App:

Online Demo: http://bit.ly/2ngN0rB

Help Commands:

``` bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console server:start
```

(sudo)


Config file example: app/config/parameters.yml

```
parameters:
    database_host: localhost
    database_port: null
    database_name: todo-list
    database_user: root
    database_password: 
```
