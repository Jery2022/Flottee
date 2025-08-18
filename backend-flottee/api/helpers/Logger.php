<?php
// backend-flottee/api/helpers/Logger.php

namespace App\Helpers;

class Logger
{
    private static string $logFile = __DIR__ . '/../../logs/suivi_log.log';

    public static function log(string $message, string $level = 'INFO'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;

        file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
    }

    // 🔧 Optionnel : méthode pour changer dynamiquement le fichier de log
    public static function setLogFile(string $path): void
    {
        self::$logFile = $path;
    }
}
