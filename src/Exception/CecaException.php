<?php

namespace Sergiosanchezalvarez\CecaPlugin\Exception;

use Payum\Core\Exception\Http\HttpException;

class CecaException extends HttpException {
    const LABEL = 'CecaException';

    public static function newInstance($status) {
        $message = implode(PHP_EOL, [self::LABEL . ' ' . $status]);

        $e = new static($message);

        return $e;
    }
}