<?php

declare(strict_types=1);

namespace Phphleb\Spreader;

use Hleb\Static\Settings;
use Hleb\Static\System;
use Phphleb\Spreader\Src\TransferInterface;

class Transfer implements TransferInterface
{
    public const FILE_TYPE = "File";

    public const DB_TYPE = "DB";

    public const TYPES = [self::FILE_TYPE, self::DB_TYPE];

    private const ERROR = 'The type of config saving in constant spread.config.type is specified incorrectly, possible values:';

    private TransferInterface $transferMethod;

    private static ?string $queryId = null;

    private string $type = self::FILE_TYPE;

    private static bool $cacheOn = true;

    private static array $cache = [];

    /**
     * Parameter initialization
     *
     * Инициализация параметров.
     */
    public function __construct(bool $isDefault = false)
    {
        if ($isDefault === false) {
            $this->type = Settings::getParam('common', 'spread.config.type') ?: self::FILE_TYPE;
        }
        if (!\in_array($this->type, self::TYPES)) {
            throw new \DomainException(self::ERROR . ' ' . \implode(",", self::TYPES));
        }
        $class = "Phphleb\Spreader\Src\\" . $this->type . "Transfer";

        $this->transferMethod = new $class();
    }

    /**
     * @inheritDoc
     *
     * The data can be obtained as:
     *
     * Данные можно получить как:
     *
     * // (/storage/lib/phphleb/demo-updater/config.js)
     * (new Transfer())->get('phphleb/demo-updater', 'config');
     */
    #[\Override]
    public function get(string $lib, string $name): ?array
    {
        $this->checkLib($lib);
        $cache = $this->getFromCache($lib, $name);
        if ($cache !== false) {
            return $cache;
        }
        $data = $this->transferMethod->get($lib, $name);
        $this->saveToCache($lib, $name, $data);

        return $data;
    }

    /**
     * @inheritDoc
     *
     * The data can be saved as:
     *
     * Данные можно сохранить как:
     *
     * // (/storage/lib/phphleb/demo-updater/config.js)
     * (new Transfer())->save('phphleb/demo-updater', 'config', ['design' => 'base']);
     */
    #[\Override]
    public function save(string $lib, string $name, array $config): void
    {
        $this->checkLib($lib);
        $this->saveToCache($lib, $name, $config);
        $this->transferMethod->save($lib, $name, $config);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function sync(string $lib, string $name): void
    {
        $this->checkLib($lib);
        $this->transferMethod->sync($lib, $name);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function update(string $lib, string $name): void
    {
        $this->checkLib($lib);
        $this->transferMethod->sync($lib, $name);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function clear(): void
    {
        $this->transferMethod->clear();
    }

    /**
     * Returns true if the current configuration storage
     * type is the default (in files).
     *
     * Возвращает true если текущий тип хранения конфигурации
     * является дефолтным (в файлах).
     */
    public function isDefault(): bool
    {
        return $this->type === self::FILE_TYPE;
    }

    /**
     * Disable data caching.
     *
     * Отключение кеширования данных.
     */
    public function disableCache(): void
    {
        self::$cacheOn = false;
    }

    /**
     * Preliminary check of the format of the transferred value.
     *
     * Предварительная проверка формата переданного значения.
     */
    private function checkLib(string $lib): void
    {
        if (!\str_contains(\trim($lib, '/'), '/') || str_contains($lib, '.')) {
            throw new \InvalidArgumentException('Incorrect target library specified:' . $lib);
        }
    }

    /**
     * Returns data from the cache if it was found.
     * It is assumed that the cached data will be unchanged
     * throughout the request.
     * Therefore, when changing the state,
     * you must disable the cache.
     *
     * Возвращает данные из кеша, если они были найдены.
     * Предполагается, что кешируемые данные будут неизменны
     * на протяжении всего запроса.
     * Поэтому при изменениях состояния необходимо отключать кеш.
     */
    private function getFromCache(string $lib, string $name): null|false|array
    {
        // If at least one action was performed without caching, then the cache is disabled.
        // Если хотя бы одно действие выполнялось без кеширования, то кеш отключен.
         if (!self::$cacheOn) {
             self::$cache = [];
             return false;
         }
         // In asynchronous mode, you need to delete the cache after the request is completed.
         // В асинхронном режиме нужно удалять кеш после завершения запроса.
         if (Settings::isAsync()) {
             $reqId = System::getRequestId();
             if ($reqId && self::$queryId !== $reqId) {
                 self::$queryId = $reqId;
                 self::$cache = [];
                 self::$cacheOn = true;
                 return false;
             }
         }
         // Возвращается кеш (при этом может быть вариант, что он равен null).
        if (isset(self::$cache[$lib]) && \array_key_exists($name, self::$cache[$lib])) {
            return self::$cache[$lib][$name];
        }
         return false;
    }

    /**
     * Saving (or overwriting) data to the cache.
     *
     * Сохранение (или перезапись) данных в кеш.
     */
    private function saveToCache(string $lib, string $name, ?array $config): void
    {
        if (!self::$cacheOn) {
            self::$cache = [];
            return;
        }
        self::$cache[$lib][$name] = $config;
    }

}


