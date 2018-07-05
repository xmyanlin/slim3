<?php
namespace App\Middlewares;

use App\Libs\Bechat;
use Monolog\Handler\AbstractProcessingHandler as Handler;

/**
 * Class Logger
 *
 * @package App\Middlewares
 */
class Logger extends Handler {
    protected function formatter($record) {
        return isset($record['formatted']) ? $record['formatted'] : (isset($record['message']) ? $record['message'] : '');
    }

    function write(array $record) {
        $record = $this->formatter($record);
        Bechat::instance()->run($record);
    }
}