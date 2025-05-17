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

        // Use plain placeholders; we'll inject color manually
        $format = "[{$app} {$version} %datetime%] %level_name%: %message%\n";

        parent::__construct($format, 'Ymd H:i:s', true, true);
    }

    public function format(LogRecord $record): string
    {
        $output = parent::format($record);

        $gray = "\033[38;5;242m";
        $white = "\033[38;5;251m";

        // Color [ ... ] parts: make brackets white, contents gray
        $output = preg_replace_callback('/\[(.*?)\]/', function ($matches) use ($gray, $white) {
            return "{$white}[{$gray}{$matches[1]}{$white}]";
        }, $output);

        // Color level name (e.g. INFO, WARN, ERROR)
        $output = str_replace(
            $record->level->getName(),
            $this->colorizeLevel($record->level->getName()),
            $output
        );

        return $output;
    }

    protected function colorizeLevel(string $level): string
    {
        return match ($level) {
            'DEBUG'     => "\033[1;37mDEBUG\033[0m",
            'INFO'      => "\033[0;32mINFO\033[0m",
            'NOTICE'    => "\033[0;36mNOTICE\033[0m",
            'WARNING'   => "\033[1;33mWARN\033[0m",
            'ERROR'     => "\033[0;31mERROR\033[0m",
            'CRITICAL'  => "\033[1;31mCRITICAL\033[0m",
            'ALERT'     => "\033[1;35mALERT\033[0m",
            'EMERGENCY' => "\033[1;41mEMERGENCY\033[0m",
            default     => $level,
        };
    }
}
