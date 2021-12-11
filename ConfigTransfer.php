<?php
declare(strict_types=1);

namespace Phphleb\Spreader;


use Phphleb\Spreader\Src\TransferInterface;

class ConfigTransfer implements TransferInterface
{
    private const FILE_TYPE = "File";

    private const DB_TYPE = "Db";

    private const ALL_TYPES = [self::FILE_TYPE, self::DB_TYPE];

    private const DEFAULT_NAME = 'global';

    private static ?string $name = null;

    private static string $type;

    private TransferInterface $transferMethod;

    public function __construct(string $path, string $target)
    {
        self::$name = self::$name ?: (defined("HLEB_CONFIG_SPREADER_NAME") ? htmlentities(HLEB_CONFIG_SPREADER_NAME) : self::DEFAULT_NAME);
        self::$type = defined("HLEB_CONFIG_SPREADER_TYPE") ? HLEB_CONFIG_SPREADER_TYPE : self::FILE_TYPE;
        if (!in_array(self::$type, self::ALL_TYPES)) {
            throw new \ErrorException("The type of config saving in constant HLEB_CONFIG_SPREADER_TYPE is specified incorrectly, possible values: " . implode(",", self::ALL_TYPES));
        }
        $class = "Phphleb\Spreader\Src\\" . self::$type . "ConfigTransfer";

        $this->transferMethod = new $class(self::$type === self::FILE_TYPE ? $path : self::$name, $target);
    }

    public function saveIfNotExists(array $config): bool
    {
        return $this->transferMethod->saveIfNotExists($config);
    }

    public function get(): array
    {
        return $this->transferMethod->get();
    }

    public function save(array $config): bool
    {
        return $this->transferMethod->save($config);
    }

    public function remove(): bool
    {
        return $this->transferMethod->remove();
    }

    public function getName(): string
    {
        return self::$name;
    }

    public function getType(): string
    {
        return self::$type;
    }

}


