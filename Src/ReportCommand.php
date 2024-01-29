<?php

declare(strict_types=1);

namespace Phphleb\Spreader\Src;

use Hleb\Static\Settings;
use Phphleb\Spreader\Transfer;

class ReportCommand
{
    /**
     * Displaying preliminary information
     * about the upcoming data transfer.
     *
     * Вывод предварительной информации
     * о предстоящем переносе данных.
     */
    public function run(): void
    {
        $type = Settings::getParam('common', 'spread.config.type');
        if (!$type) {
            echo 'Attention! `spread.config.type` parameter not assigned for config transfer.' . PHP_EOL;
        } else {
            echo 'Parameter selected for data transfer: ' . $type . PHP_EOL;
        }
        if ($type === Transfer::DB_TYPE) {
            $db = Settings::getParam('database', 'spread.db.type');
            if (!$db) {
                echo 'Attention! `spread.db.type` (database.php) parameter not assigned for config transfer.' . PHP_EOL;
            } else {
                echo 'The database settings will be taken from the value of `spread.db.type`: ' . $db . PHP_EOL;
            }
        }
        $map = (new TransferMap())->get();
        if (!$map) {
            echo 'No data found to transfer report.' . PHP_EOL;
            return;
        }
        $transfer = new Transfer();
        $transfer->disableCache();
        foreach ($map as $lib => $names) foreach ($names as $name) {
            $data = $transfer->get($lib, $name);
            $mark = $data === null ? '[ADD]' : '[UPD]';
            $remark = $data === null ? '- will be added.' : '- will be updated.';
            echo $mark . ' ' . $lib . ' `' . $name . '` ' . $remark . PHP_EOL;
        }
    }
}