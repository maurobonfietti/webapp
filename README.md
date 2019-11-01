# Another ToDo List Web App:

This is another ToDo List app ;-)

## Application Stack:

* Frontend: Angular 4 + Material Design. [Go to repository](https://github.com/maurobonfietti/todo-list-front).
* Backend: Symfony PHP + MySQL DB.


## Configure:

Config file example: app/config/parameters.yml

```
parameters:
    database_host: localhost
    database_port: null
    database_name: todo-list
    database_user: root
    database_password: 
```

## Execute Commands:

``` bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console server:start
```

## Demo:

[Try Live Demo.](http://bit.ly/2ngN0rB) *[Hosted Using Heroku Free.]*

### Enjoy ;-)
