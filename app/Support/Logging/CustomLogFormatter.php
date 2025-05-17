<?php

// app/Support/Logging/CustomLogFormatter.php

namespace App\Support\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;

class CustomLogFormatter extends LineFormatter
{

    public function __construct()
    {
        $app = config('app.name', 'FileWatcher');
        $version = config('app.version', 'v1.0.0');
        $format = "[{$app}{$version} %datetime%] %level_name%: %message%\n";

        parent::__construct($format, 'Ymd H:i:s', true, true);
    }

    public function format(LogRecord $record): string
    {
        $level = $record->level->getName();

        $color = match ($level) {
            'DEBUG'     => "\033[37m",
            'INFO'      => "\033[32m",
            'NOTICE'    => "\033[34m",
            'WARNING'   => "\033[33m",
            'ERROR'     => "\033[31m",
            'CRITICAL', 'ALERT', 'EMERGENCY' => "\033[1;31m",
            default     => "\033[0m",
        };

        $reset = "\033[0m";

        return parent::format(
            $record->with(message: "{$color}{$record->message}{$reset}")
        );
    }
}
