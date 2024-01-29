<?php

namespace Phphleb\Spreader\Src;

interface TransferInterface
{
    /**
     * Getting data for a given type of configuration
     * storage by library name.
     * Other types of data are also synchronously
     * saved to the default settings.
     *
     * Получение данных для заданного типа хранения
     * конфигурации по названию библиотеки.
     * Данные других типов синхронно сохраняются
     * также в дефолтные настройки.
     *
     * @param string $lib - the name of the library, for example `phphleb/demo-updater`.
     *                    - название библиотеки, например `phphleb/demo-updater`.
     *
     * @param string $name - the name of the configuration block (file), for example `config`.
     *                     - название конфигурационного блока (файла), например `config`.
     */
    public function get(string $lib, string $name): ?array;

    /**
     * Saving data for a given type of configuration
     * storage by library name.
     * Other types of data are also synchronously
     * saved to the default settings.
     *
     * Сохранение данных для заданного типа хранения
     * конфигурации по названию библиотеки.
     * Данные других типов синхронно сохраняются
     * также в дефолтные настройки.
     *
     * @param string $lib - the name of the library, for example `phphleb/demo-updater`.
     *                    - название библиотеки, например `phphleb/demo-updater`.
     *
     * @param string $name - the name of the configuration block (file), for example `config`.
     *                     - название конфигурационного блока (файла), например `config`.
     *
     * @param array $config - data to save to the configuration.
     *                      - данные для сохранения в конфигурацию.
     */
    public function save(string $lib, string $name, array $config): void;

    /**
     * Synchronization of data between the default
     * configuration and the selected one.
     * Data is taken from the selected one
     * and transferred to the default one.
     * If the selected one is missing, then data
     * from the default one will be transferred to it.
     *
     * Синхронизация данных между дефолтной
     * конфигурацией и выбранной.
     * Данные берутся из выбранной и переносятся в дефолтную.
     * Если выбранная отсутствует, то в неё будут
     * перенесены данные из дефолтной.
     *
     * @param string $lib - the name of the library, for example `phphleb/demo-updater`.
     *                    - название библиотеки, например `phphleb/demo-updater`.
     *
     * @param string $name - the name of the configuration block (file), for example `config`.
     *                     - название конфигурационного блока (файла), например `config`.
     */
    public function sync(string $lib, string $name): void;

    /**
     * The data is taken from the default configuration
     * and transferred to the selected one.
     *
     * Данные берутся из дефолтной конфигурации и переносятся в выбранную.
     *
     * @param string $lib - the name of the library, for example `phphleb/demo-updater`.
     *                    - название библиотеки, например `phphleb/demo-updater`.
     *
     * @param string $name - the name of the configuration block (file), for example `config`.
     *                     - название конфигурационного блока (файла), например `config`.
     */
    public function update(string $lib, string $name): void;

    /**
     * Complete cleaning of the data store for configurations.
     *
     * Полная очистка хранилища данных для конфигураций.
     */
    public function clear();
}