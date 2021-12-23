<?php
declare(strict_types=1);

namespace Phphleb\Spreader;


use Phphleb\Spreader\Src\TransferInterface;

class ConfigTransfer implements TransferInterface
{
    private const FILE_TYPE = "File";

    private const DB_TYPE = "Db";

    private const ALL_TYPES = [self::FILE_TYPE, self::DB_TYPE];

    private static string $type;

    private TransferInterface $transferMethod;

    /**
     * @throws \ErrorException
     */
    public function __construct()
    {
        self::$type = defined("HLEB_CONFIG_SPREADER_TYPE") ? HLEB_CONFIG_SPREADER_TYPE : self::FILE_TYPE;
        if (!in_array(self::$type, self::ALL_TYPES)) {
            throw new \ErrorException("The type of config saving in constant HLEB_CONFIG_SPREADER_TYPE is specified incorrectly, possible values: " . implode(",", self::ALL_TYPES));
        }
        $class = "Phphleb\Spreader\Src\\" . self::$type . "ConfigTransfer";

        $this->transferMethod = new $class();
    }

    public function saveIfNotExists(array $config): bool
    {
        return $this->transferMethod->saveIfNotExists($config);
    }

    public function get($isUnaltered = false): ?array
    {
        return $this->transferMethod->get($isUnaltered);
    }

    public function save(array $config): bool
    {
        return $this->transferMethod->save($config);
    }

    public function remove(): bool
    {
        return $this->transferMethod->remove();
    }

    /**
     * @param string $path - path to the saved file.
     * @param string $target - the name of the library that requests saving.
     * @return TransferInterface
     */
    public function setTarget(string $path, string $target): TransferInterface
    {
        $this->transferMethod->setTarget($path, $target);

        return $this;
    }

}


