
### Удалённое конфигурирование для библиотек фреймворка HLEB2

[![HLEB2](https://img.shields.io/badge/HLEB-2-darkcyan)](https://github.com/phphleb/hleb) ![PHP](https://img.shields.io/badge/PHP-^8.2-blue) [![License: MIT](https://img.shields.io/badge/License-MIT%20(Free)-brightgreen.svg)](https://github.com/phphleb/hleb/blob/master/LICENSE)

Позволяет создать общую конфигурацию для библиотек, в том числе использующих
настраиваемую пользователем конфигурацию.
Это может понадобиться при распределении нагрузки на несколько одинаковых клонов проекта,
в таком случае у них должна быть общая внешняя конфигурация.

Например, в библиотеке Hlogin через веб-интерфейс администратором изменён тип дизайна
регистрации, это изменение должно быть применено ко всем клонам проекта одновременно.
Библиотека phphleb/spreader добавляет общий тип хранения конфигурации в базе данных.
Для этого вам нужно переключить настройку фреймворка в тип 'DB' и указать идентификатор базы данных.


Для сохранения конфигурации в базу данных используйте следующие настройки:
```php
// File /config/common.php

/*
 │-----------------------------------------------------------------------------
 │ Selecting the type of configs storage ("File" or "DB")
 │-----------------------------------------------------------------------------
 │ Выбор типа хранения конфигов ("File" или "DB")
 │-----------------------------------------------------------------------------
 */
'spread.config.type' => 'DB',

```

```php

// File /config/database.php

return [
'spread.db.type' => 'mysql.name',
// ,,, //
];
```
Для начальной синхронизации всех конфигураций можно использовать специальную консольную команду.
Установка команды в проект:
```bash
php console phphleb/spreader add
```
Перенос конфигурации из файлов в выбранный тип (`DB`):
```bash
php console spreader/sync
```
Эта команда может пригодиться при первоначальном развертывании проекта,
она делает конфигурацию текущего проекта общей для всех его клонов.

Предварительно можно вывести данные для переноса следующей командой:
```bash
php console spreader/report
```

В случае, если вы хотите использовать этот механизм для своей библиотеки,
то нужно получение/сохранение конфигурации в ней реализовать через
класс Phphleb\Spreader\Transfer.


При выборе типа `File` будет использовано хранение конфигурации по умолчанию, в файлах по пути /storage/lib/.
Для типа `DB` данные будут перенесены в таблицу `hleb_spreader_conf`.
Проверена поддержка  __MySQL__ / __MariaDB__ / __PostgreSQL__.

