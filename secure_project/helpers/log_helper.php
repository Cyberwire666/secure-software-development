<?php

/**
 * Logs messages to the specified log file.
 *
 * @param string $level Log level (e.g., INFO, ERROR).
 * @param string $message The log message.
 * @return void
 */
function log_message($level, $message)
{
    // Define the log file path.
    $log_file = __DIR__ . '/../logs/app.log';

    // Ensure the log directory exists.
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }

    // Format the log entry.
    $time_stamp = date('Y-m-d H:i:s');
    $log_entry = "[{$time_stamp}] {$level}: {$message}\n";

    // Write the log entry to the file.
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

?>
