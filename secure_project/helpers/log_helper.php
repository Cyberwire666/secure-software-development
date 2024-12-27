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
    $log_file = __DIR__ . '/../logs/app.log'; // This creates a log file in the 'logs' directory

    // Ensure the log directory exists.
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true); // Create the directory if it doesn't exist
    }

    // Format the log entry with a timestamp and level (INFO, ERROR, etc.)
    $time_stamp = date('Y-m-d H:i:s'); // Get the current time in a readable format
    $log_entry = "[{$time_stamp}] {$level}: {$message}\n"; // Format: [timestamp] level: message

    // Write the log entry to the file.
    file_put_contents($log_file, $log_entry, FILE_APPEND); // Append the log entry to the log file
}

?>
