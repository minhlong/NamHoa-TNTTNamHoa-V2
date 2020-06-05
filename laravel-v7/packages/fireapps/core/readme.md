# Fireapps Core

### Install

```sh
$ composer install
$ composer require fireapps/core
```

### Configuration
```shell script
# Generate key
php artisan key:generate

# Publish config file
php artisan vendor:publish --provider="Fireapps\Core\FireappsProvider"

# Update config file
$ vim \source-path\config\fireapps.php

...
'core_db_connections' => 'pgsql_core',
...
```
### Migrate Database

```shell script
php artisan migrate

# if you only want to migrate some special databases such as pgsql_core, you can do: 
php artisan migrate --database=pgsql_core
```
