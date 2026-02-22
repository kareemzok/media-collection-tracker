<?php
if (defined('APP_LOGGER_BOOTSTRAPPED')) {
    return;
}
define('APP_LOGGER_BOOTSTRAPPED', true);

function app_get_error_log_path()
{
    return __DIR__ . '/../storage/error.log';
}

function app_ensure_storage_directory()
{
    $storageDirectory = dirname(app_get_error_log_path());
    if (!is_dir($storageDirectory)) {
        @mkdir($storageDirectory, 0775, true);
    }
}

function app_write_error_log($level, $message, array $context = [])
{
    static $isWriting = false;
    if ($isWriting) {
        return;
    }

    $isWriting = true;

    try {
        app_ensure_storage_directory();

        $timestamp = date('Y-m-d H:i:s');
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'CLI';

        $logLine = sprintf(
            '[%s] [%s] %s | %s %s',
            $timestamp,
            $level,
            $message,
            $requestMethod,
            $requestUri
        );

        if (!empty($context)) {
            $jsonContext = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if ($jsonContext !== false) {
                $logLine .= ' | context=' . $jsonContext;
            }
        }

        $logLine .= PHP_EOL;
        @file_put_contents(app_get_error_log_path(), $logLine, FILE_APPEND | LOCK_EX);
    } finally {
        $isWriting = false;
    }
}

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    app_write_error_log('PHP_ERROR', $message, [
        'severity' => $severity,
        'file' => $file,
        'line' => $line,
    ]);

    return false;
});

set_exception_handler(function (Throwable $exception) {
    app_write_error_log('UNCAUGHT_EXCEPTION', $exception->getMessage(), [
        'type' => get_class($exception),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString(),
    ]);

    restore_exception_handler();
    throw $exception;
});

register_shutdown_function(function () {
    $lastError = error_get_last();
    if ($lastError === null) {
        return;
    }

    $fatalErrorTypes = [
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_COMPILE_ERROR,
        E_USER_ERROR,
        E_RECOVERABLE_ERROR,
    ];

    if (!in_array($lastError['type'], $fatalErrorTypes, true)) {
        return;
    }

    app_write_error_log('FATAL_ERROR', $lastError['message'], [
        'severity' => $lastError['type'],
        'file' => $lastError['file'],
        'line' => $lastError['line'],
    ]);
});

app_ensure_storage_directory();
