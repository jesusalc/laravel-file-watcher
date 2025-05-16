<?php

// app/Support/Logging/CustomLogFormatter.php

namespace App\Support\Logging;

use Monolog\Formatter\LineFormatter;

class CustomLogFormatter extends LineFormatter
{
    public function __construct()
    {
        $app = config('app.name', 'FileWatcher');
        $version = config('app.version', 'v1.0.0');

        $format = "[{$app}{$version} {date}] %level_name%: %message%\n";
        parent::__construct($format, 'Ymd H:i:s', true, true);
    }
}
