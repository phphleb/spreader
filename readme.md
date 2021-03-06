### Sets the method to save various configs* for Micro-Framework HLEB

By default, saving to files does not require configuration.

To save to the database and use one config for several project replications.
```php
// File ./start.hleb.php

/*
 |-----------------------------------------------------------------------------
 | Selecting the type of configs storage ("File" or "Db")
 |-----------------------------------------------------------------------------
 | Выбор типа хранения конфигов ("File" или "Db")
 |-----------------------------------------------------------------------------
 */
define("HLEB_CONFIG_SPREADER_TYPE", "Db");

/*
 |-----------------------------------------------------------------------------
 | The name of the current connection, matches will be grouped
 | with the overall configuration. Максимальное количество символов - 100.
 |-----------------------------------------------------------------------------
 | Имя текущего подключения, совпадения будут сгруппированы
 | с общей конфигурацией. Maximum character limit - 100.
 |-----------------------------------------------------------------------------
 */
define("HLEB_CONFIG_SPREADER_NAME", "connection-name");
```
```php
// File ./console

define("HLEB_CONFIG_SPREADER_TYPE", "Db");
define("HLEB_CONFIG_SPREADER_NAME", "connection-name");

```
```php
// File ./database/dbase.config.php

// Optionally, you can select the type of connection from the resource /database/dbase.config.php
define("HLEB_SPREADER_TYPE_DB", "mysql.myname");

```
The name of the table being created in the database is `spreader_configs`.

When changing the storage type, you must run the `php console phphleb/hlogin --add` command.

Supported  __MySQL__ / __MariaDB__ / __PostgreSQL__.


\* - Сonfiguration files for libraries "hlogin", "ucaptcha", "muller" and others.

------------------------------

[![License: MIT](https://img.shields.io/badge/License-MIT%20(Free)-brightgreen.svg)](https://github.com/phphleb/draft/blob/main/LICENSE) ![PHP](https://img.shields.io/badge/PHP-^7.4.0-blue) ![PHP](https://img.shields.io/badge/PHP-8-blue) 

