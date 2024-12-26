<?php
// Log Helper

/**
 * Write log messages to a log file.
 *
 * @param string $message The log message.
 */
function write_log($message) {
    // Path to the log file
    $log_file = __DIR__ . '/../logs/app.log';
    
    // Append date, time, and the message to the log file
    file_put_contents($log_file, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX);
}
?>
