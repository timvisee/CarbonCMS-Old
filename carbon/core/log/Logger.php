<?php

namespace carbon\core\log;

use carbon\core\filesystem\file\accessmode\FileAccessModeFactory;
use carbon\core\filesystem\file\File;
use carbon\core\filesystem\file\FileWriter;
use carbon\core\filesystem\FilesystemObject;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Logger class. This class provides basic logging functionality, which makes it easier to log to files.
 *
 * @package carbon\core\log
 * @author Tim Visee
 */
class Logger {

    /** @var Array An array containing all logged messages. */
    private $log = Array();
    /** @var \carbon\core\filesystem\file\File|null The file which is written to by the logger, if set */
    private $file;
    /** @var FileWriter|null The file writer instance, which writes to the file */
    private $fileWriter;
    /** @var bool True if the logger should append to the log file, false otherwise */
    private $fileAppend;
    /** @var bool True to print the log messages on the page as soon as they're being logged, false otherwise. */
    private $printLog = false;

    /** @var bool True to prefix all log messages with the current date and time. */
    private $logTime = true;
    /** @var bool True to log info messages, false otherwise. */
    private $logInfo = true;
    /** @var bool True to log debug messages, false otherwise. */
    private $logDebug = true;
    /** @var bool True to log warning messages, false otherwise. */
    private $logWarning = true;
    /** @var bool True to log error messages, false otherwise. */
    private $logError = true;

    /** Prefix for info log messages. */
    const LOG_INFO_PREFIX = '[INFO]';
    /** Prefix for debug log messages. */
    const LOG_DEBUG_PREFIX = '[DEBUG]';
    /** Prefix for warning log messages. */
    const LOG_WARNING_PREFIX = '[WARNING]';
    /** Prefix for error log messages. */
    const LOG_ERROR_PREFIX = '[ERROR]';

    /**
     * Constructor.
     *
     * @param FilesystemObject|string|null $file File system object instance or the path of a file as a string of the
     * file to log to. Null to disable file logging.
     * @param bool $fileAppend True to append to the log file, false to overwrite the file if it exists already.
     *
     * @throws \Exception Throws an exception on error.
     */
    public function __construct($file, $fileAppend = true) {
        // Get $file as File instance
        $file = File::asFile($file);

        // Set if we're appending to the log file
        $this->fileAppend = $fileAppend;

        // Set the file, and instantiate the file writer if needed
        if(!$this->setFile($file))
            // TODO: Throw a better exception!
            throw new \Exception('Failed to access log file!');
    }

    /**
     * Destructor.
     * Ensures that the file writer closes properly.
     */
    public function __destruct() {
        // Close the file writer
        if($this->fileWriter !== null)
            $this->fileWriter->close();
    }

    /**
     * Get the file which is used and being written to by the logger.
     *
     * @return \carbon\core\filesystem\file\File|null The file which is used by the logger. Null if no file is set.
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Set the file to write to.
     *
     * @param \carbon\core\filesystem\file\File|FilesystemObject|string|null $file File instance or the path of a file as a string of the file
     * to write to. Null to disable file logging.
     *
     * @return bool True on success, false on failure.
     */
    public function setFile($file) {
        // Get and set $file as File instance
        $file = File::asFile($file);

        // Make sure the file has changed
        if($this->file === $file)
            return true;

        // Update the file
        $this->file = $file;

        // Check whether file logging has been disabled
        if($this->file === null) {
            // Close the file writer and destroy it's instance
            if($this->fileWriter !== null)
                $this->fileWriter->close();
            $this->fileWriter = null;

            // Return the result
            return true;
        }

        // Set the file on the file writer if the writer is instantiated already
        if($this->fileWriter !== null)
            return $this->fileWriter->setFile($this->file);

        // Instantiate a new file writer, return the result
        // TODO: Customizable file access mode!
        $this->fileWriter = new FileWriter($this->file, FileAccessModeFactory::createAppendMode(false));
        return true;
    }

    /**
     * Check whether the logger is logging to a file.
     *
     * @return bool True if the logger is logging to a file, false otherwise.
     */
    public function isUsingFile() {
        return $this->file !== null;
    }

    /**
     * Check whether the logger is appending to the file.
     *
     * @return bool True if the logger is appending to the file, false otherwise.
     */
    public function isAppending() {
        return $this->fileAppend;
    }

    /**
     * Set whether the logger should append to the log file. The log file will automatically be reopened with the proper
     * appending mode if it's opened already.
     *
     * @param bool $append True if the logger needs to append to the log file, false otherwise.
     *
     * @return bool True on success, false on failure.
     */
    public function setAppending($append) {
        // Set if we're appending to the log file
        $this->fileAppend = $append;

        // Make sure the file writer is available
        if($this->fileWriter === null)
            return true;

        // Set the appending mode on the file writer, return the result
        return $this->fileWriter->setAppending($this->fileAppend);
    }

    /**
     * Check whether logged messages are being printed on the page.
     *
     * @return bool True if logged messages are being printed on the page.
     */
    public function isPrintingLogs() {
        return $this->printLog;
    }

    /**
     * Set whether logged messages should be printed on the page.
     *
     * @param bool $printLogs True if logged messages should be printed on the page, false otherwise.
     */
    public function setPrintLogs($printLogs) {
        $this->printLog = $printLogs;
    }

    /**
     * Get a list of logged messages.
     *
     * @return Array An array with a list of logged messages.
     */
    public function getLog() {
        return $this->log;
    }

    /**
     * Check whether all log messages are prefixed with the current date and time.
     *
     * @return bool True if all log messages are prefixed with the current date and time, false otherwise.
     */
    public function isTimeEnabled() {
        return $this->logTime;
    }

    /**
     * Set whether all log messages should be prefixed with the current date and time.
     *
     * @param bool $logTime True to prefix all log messages with the current date and time, false otherwise.
     */
    public function setTimeEnabled($logTime) {
        $this->logTime = $logTime;
    }

    /**
     * Log a message, with an optional prefix.
     *
     * @param string $msg The message to log.
     * @param string|null $prefix [optional] A log message prefix. Null to ignore the prefix.
     */
    public function log($msg, $prefix = null) {
        // Check whether $prefix should be used
        if(!empty($prefix))
            $msg = $prefix . ' ' . $msg;

        // Prefix the message with the current date and time
        $msg = self::getLogTimePrefix() . ' ' . $msg;

        // Add the message to the log list
        array_push($this->log, $msg);

        // Print the message on the page
        if($this->printLog)
            echo $msg . '<br />';

        // Log to the file if a log file is set
        if($this->isUsingFile())
            $this->fileWriter->writeLine($msg);
    }

    /**
     * Log an info message.
     *
     * @param string $msg The info message to log.
     *
     * @return bool True if the message was successfully logged,
     * false if the message wasn't logged because info logging is disabled.
     */
    public function info($msg) {
        // Check whether info messages should be logged
        if(!$this->logInfo)
            return false;

        // Log the info message, return the result
        $this->log($msg, self::LOG_INFO_PREFIX);
        return true;
    }

    /**
     * Check whether info messages are being logged.
     *
     * @return bool True if info messages are being logged, false otherwise.
     */
    public function isInfoLoggingEnabled() {
        return $this->logInfo;
    }

    /**
     * Set whether info messages should be logged.
     *
     * @param bool $logInfo True if info messages should be logged, false otherwise.
     */
    public function setInfoLoggingEnabled($logInfo) {
        $this->logInfo = $logInfo;
    }

    /**
     * Log a debug message.
     *
     * @param string $msg The debug message to log.
     *
     * @return bool True if the message was successfully logged,
     * false if the message wasn't logged because debug logging is disabled.
     */
    public function debug($msg) {
        // Check whether debug messages should be logged
        if(!$this->logDebug)
            return false;

        // Log the debug message, return the result
        $this->log($msg, self::LOG_DEBUG_PREFIX);
        return true;
    }

    /**
     * Check whether debug messages are being logged.
     *
     * @return bool True if debug messages are being logged, false otherwise.
     */
    public function isDebugLoggingEnabled() {
        return $this->logDebug;
    }

    /**
     * Set whether info messages should be logged.
     *
     * @param bool $logDebug True if debug messages should be logged, false otherwise.
     */
    public function setDebugLoggingEnabled($logDebug) {
        $this->logDebug = $logDebug;
    }

    /**
     * Log a warning message.
     *
     * @param string $msg The warning message to log.
     *
     * @return bool True if the message was successfully logged,
     * false if the message wasn't logged because warning logging is disabled.
     */
    public function warning($msg) {
        // Check whether warning messages should be logged
        if(!$this->logWarning)
            return false;

        // Log the warning message, return the result
        $this->log($msg, self::LOG_WARNING_PREFIX);
        return true;
    }

    /**
     * Check whether warning messages are being logged.
     *
     * @return bool True if warning messages are being logged, false otherwise.
     */
    public function isWarningLoggingEnabled() {
        return $this->logWarning;
    }

    /**
     * Set whether warning messages should be logged.
     *
     * @param bool $logWarning True if warning messages should be logged, false otherwise.
     */
    public function setWarningLoggingEnabled($logWarning) {
        $this->logWarning = $logWarning;
    }

    /**
     * Log an error message.
     *
     * @param string $msg The error message to log.
     *
     * @return bool True if the message was successfully logged,
     * false if the message wasn't logged because error logging is disabled.
     */
    public function error($msg) {
        // Check whether error messages should be logged
        if(!$this->logError)
            return false;

        // Log the error message, return the result
        $this->log($msg, self::LOG_ERROR_PREFIX);
        return true;
    }

    /**
     * Check whether error messages are being logged.
     *
     * @return bool True if error messages are being logged, false otherwise.
     */
    public function isErrorLoggingEnabled() {
        return $this->logInfo;
    }

    /**
     * Set whether error messages should be logged.
     *
     * @param bool $logError True if error messages should be logged, false otherwise.
     */
    public function setErrorLoggingEnabled($logError) {
        $this->logError = $logError;
    }

    /**
     * Get the current date and time as a string to prefix the log messages with.
     *
     * @return string The current date and time as a string.
     */
    private static function getLogTimePrefix() {
        return date("Y-m-d H:i:s");
    }

    /**
     * Get the file writer instance, which is used to write to a log file.
     * This method may return null if no file was specified.
     *
     * @return FileWriter|null The file writer instance which is used by the logger,
     * null if the file writer isn't instantiated.
     */
    public function getFileWriter() {
        return $this->fileWriter;
    }
}
 