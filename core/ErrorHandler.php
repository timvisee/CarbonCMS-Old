<?php

/**
 * ErrorHandler.php
 *
 * The ErrorHandler class handles all the errors and exceptions.
 * The ErrorHandler class shows a nice, informative error page when the debug mode enabled.
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace core;

use core\exception\CarbonException;
use core\util\ArrayUtils;
use core\util\StringUtils;

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

// TODO: Add feature to send the error to administrators
// TODO: Clean up the code, improve everything
// TODO: Fix fatal errors not being handled (repoduce using: requrie("sdfjasopdifaspoid");)

/**
 * Handles all the errors and exceptions.
 * @package core
 * @author Tim Visee
 */
class ErrorHandler {

    /** @var bool $debug True to enable debug mode, sensitive data will be shown when this mode is enabled */
    private static $debug = false;

    /**
     * Initialize the error handler
     * @param bool $handle_exceptions [optional]True to handle all exceptions
     * @param bool $handle_errors [optional] True to handle all errors
     * @param bool $handle_shutdown [optional] True to handle PHP Fata Errors and shutdown functions like die(); and exit();
     * @param bool $debug [optional] True to enable the debug mode, sensitive data will be shown of this mode is enabled
     */
    public static function init($handle_exceptions = true, $handle_errors = true, $handle_shutdown = true, $debug = false) {
        // Set whether the debug mode should be enabled or not
        self::$debug = $debug;

        // Set the error, exception and shutdown handlers
        if($handle_errors)
            set_error_handler(__CLASS__ . '::handleError', E_ALL);
        /*if($handle_shutdown)
            register_shutdown_function(__CLASS__ . '::handleShutdown');*/
        if($handle_exceptions)
            set_exception_handler(__CLASS__ . '::handleException');
    }

    /**
     * Get whether the debug mode is enabled or not
     * @return bool True if the debug mode is enabled, false otherwise
     */
    public static function getDebug() {
        return self::$debug;
    }

    /**
     * Set whether the debug mode is enabled or not. Sensitive data will be shown if this mode is enabled.
     * @param bool $debug True to enable the debug mode, false otherwise
     */
    public static function setDebug($debug) {
        self::$debug = $debug;
    }

    /**
     * Handles all errors. Converts all errors to ErrorExceptions
     * @param int $err_code Error code
     * @param string $err_msg Error message
     * @param string $err_file Error source file
     * @param int $err_line Line the error source is at in the source file
     * @throws \ErrorException Throws exception based on the error
     */
    public static function handleError($err_code, $err_msg, $err_file, $err_line) {
        // Ignore errors with error code 8 (E_NOTICE, could also be called when there's no problem at all)
        if($err_code === 8)
            return;

        // Throw an ErrorException based on the error
        throw new \ErrorException($err_msg, 0, $err_code, $err_file, $err_line);

        // TODO: Return false?
    }

    // Sample code to handle shutdown events
    /* public static function handleShutdown() {
        $err_file = "unknown file";
        $err_msg  = "shutdown";
        $err_code   = E_CORE_ERROR;
        $err_line = 0;

        $error = error_get_last();

        if($error !== null) {
            $err_code   = $error["type"];
            $err_file = $error["file"];
            $err_line = $error["line"];
            $err_msg  = $error["message"];
        }

        if(StringUtils::equals($err_msg, 'shutdown', true, true))
            return;

        throw new \ErrorException($err_msg, 0, $err_code, $err_file, $err_line);
    } */

    /**
     * Handles all PHP exceptions
     * @param \Exception $ex Exception instance
     */
    public static function handleException(\Exception $ex) {
        // End and clean any nesting of the output buffering mechanism
        while(ob_get_level() > 0)
            ob_end_clean();

        // Get the exception type
        $ex_type = get_class($ex);

        // Get the page title, make sure no sensitive data is being shown
        if(self::getDebug())
            $page_title = $ex_type . ': ' . $ex->getMessage();
        else
            $page_title = 'Error!';

        // Print the top of the page
        self::printPageTop($page_title);

        // Show the information message
        ?>
        <div id="page">
            <h1>Whoops!</h1>
            <p>
                We're sorry, an error occurred while loading the page, please go back and try it again.<br />
                The site administrators have been informed about this error.
                <?php
                if(self::getDebug()) {
                    ?>
                    More information is shown bellow.<br />
                    <br />
                    <b>Warning: </b>The error information displayed bellow is sensitive, it's a big security risk to show this information to public.
                    You can disable this information by disabling the debug mode of Carbon CMS.
                    You can disable the debug mode at any time by changing the '<i>carbon.debug</i>' setting to '<i>false</i>' in the configuration file.
                    <?php
                } else {
                    ?>
                    <br /><br />
                    The debug mode of Carbon CMS is currently disabled. Detailed error information is not being displayed for security reasons.<br />
                    You can enable the debug mode of Carbon CMS at any time by changing the '<i>carbon.debug</i>' setting to '<i>true</i>' in the configuration file.<br />
                    Please note it's a high security risk to show debug information to public.
                    <?php
                }
                ?>
            </p>
        </div>
        <?php

        // Make sure it's allowed to show sensitive data
        if(self::getDebug()) {
            // Get the exception type in proper format
            $ex_type = get_class($ex);
            if(strrpos($ex_type, '\\'))
                $ex_type = '<span style="color: #666;">' . substr($ex_type, 0, strrpos($ex_type, '\\') + 1) . '</span>' . substr($ex_type, strrpos($ex_type, '\\') + 1);

            // Show the error information
            ?>
            <div id="page">
            <h1>Error Information</h1>
            <table>
                <tr><td>Type:</td><td><?=$ex_type; ?></td></tr>
                <tr><td>Message:</td><td><?=$ex->getMessage(); ?></td></tr>
                <tr><td>File:</td><td><span class="file"><?=$ex->getFile(); ?></span></td></tr>
                <tr><td>Code:</td><td><?=$ex->getCode(); ?></td></tr>
            </table>
            <?php

            // Show possible solutions, if available
            if($ex instanceof CarbonException) {
                if($ex->hasSolutions()) {
                    ?>
                    <h1>Possible Solutions</h1>
                    <p>
                        <ul><li><?=implode('</li><li>', $ex->getSolutions()); ?></li></ul>
                    </p>
                    <?php
                }
            }

            // Show the trace of the exception
            self::showTrace($ex);

        }

        // Print the bottom of the page
        self::printPageBottom();

        // TODO: Return false?
    }

    /**
     * Print the top of the error page
     * @param string $page_title Page title to use
     */
    private static function printPageTop($page_title) {
        $site_path = '';
        if(Core::getConfig() != null)
            $site_path = Core::getConfig()->getValue('general', 'site_path', $site_path);

        // TODO: Make sure the correct stylesheet is being used

        ?>
        <html>
        <head>
            <title>Carbon CMS - <?=$page_title; ?></title>
            <link rel="stylesheet" type="text/css" href="<?=$site_path; ?>/style/error.css">
        </head>
        <body>
            <div id="page-wrap">
        <?php
    }

    /**
     * Print the bottom of the error page
     */
    private static function printPageBottom() {
        // TODO: Show a proper footer with the correct version info
        ?>
                </div>
            </div>
            <div id="footer-wrap">
                <div class="footer-left">
                    <a href="http://timvisee.com/" title="About Carbon CMS" target="_new" >Carbon CMS</a>&nbsp;&nbsp;&middot;&nbsp;&nbsp;Version 0.1
                </div>
                <div class="footer-right">
                    Carbon CMS by <a href="http://timvisee.com/" title="About Tim Vis&eacute;e" target="_new" >Tim Vis&eacute;e</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }

    /**
     * Shows the trace of the error/exception
     * @param \Exception $ex Exception instance
     */
    public static function showTrace($ex) {
        // Get the trace of the exception
        $trace = $ex->getTrace();

        $start = 0;

        if(get_class($ex) === "ErrorException")
            $start++;

        // Make sure the first trace step isn't being shown twice
        if($ex->getFile() === $trace[$start]['file'] && $ex->getLine() === $trace[$start]['line'])
            $start++;

        // Print the top of the error trace
        ?>
        <h1>Error Trace</h1>
        <div id="trace">
        <?php

        // Show the first trace step (the Exception itself)
        self::showTraceStep('Source', null, null, null, null, $ex->getLine(), $ex->getFile());

        // Show a message if any trace is skipped
        if($start == 1)
            echo '<i style="color: #666;">Skipped 1 identical trace...</i><br /><br />';
        else if($start > 1)
            echo '<i style="color: #666;">Skipped ' . $start . ' identical traces...</i><br /><br />';

        // Put each trace step on the page
        for($i = $start; $i < count($trace); $i++) {
            // Get the information about the current trace step
            $t_class = @$trace[$i]['class'];
            $t_type = @$trace[$i]['type'];
            $t_function = @$trace[$i]['function'];
            if(isset($trace[$i]['line']))
                $t_line = $trace[$i]['line'];
            else
                $t_line = @$trace[$i]['args'][3];
            if(isset($trace[$i]['file']))
                $t_file = $trace[$i]['file'];
            else
                $t_file = @$trace[$i]['args'][2];
            $t_args = @$trace[$i]['args'];

            // Show the trace step
            self::showTraceStep($i + 1, $t_class, $t_type, $t_function, $t_args, $t_line, $t_file);
        }

        ?>
        </div>
        <?php

        // Show the error context (if available) for PHP errors
        if(isset($trace[0]['args']) && is_array($trace[0]['args'][4])) {
            // Print the header
            ?>
            <h1>Error Context</h1>
            <?php

            // Show the context
            self::showContext($trace[0]['args'][4]);
        }
    }

    /**
     * Show a trace step
     * @param mixed|null $t_id Trace identifier or index, or null to hide the trace identifer
     * @param string $t_class Trace class
     * @param string $t_type Trace type
     * @param string $t_function Trace function
     * @param array|null $t_args Trace arguments, null for no arguments (default: null)
     * @param int|null $t_line Trace line, null if the line is unknown (default: null)
     * @param string|null $t_file Trace file, null if the file is unknown (default: null)
     */
    public static function showTraceStep($t_id, $t_class, $t_type, $t_function, $t_args = null, $t_line = null, $t_file = null) {
        // Get the proper function name
        if($t_function != null) {
            if($t_class != null && $t_type != null)
                $func = $t_class . $t_type . $t_function . '(' . self::joinArguments($t_args) . ');';
            else
                $func = $t_function . '(' . self::joinArguments($t_args) . ');';

        } else {
            if($t_file != null && $t_line != null) {
                if(!file_exists($t_file))
                    return;

                $file_contents = file($t_file);

                if(!isset($file_contents[$t_line - 1]))
                    return;

                $func = trim($file_contents[$t_line - 1]);

            } else
                return;
        }

        ?>
        <div class="step">
        <h2>
        <?php

        // Print the trace index if set
        if($t_id !== null)
            echo $t_id . ' &nbsp;&middot;&nbsp';

        // Print the trace function
        ?>
        <?=$func; ?></h2>
        <table>
            <tr><td>Function:</td><td><span class="function"><?=self::highlight($func); ?></span></td></tr>
        <?php

        // Print the line
        if($t_line != null)
            echo '<tr><td>Line:</td><td>' . $t_line . '</td></tr>';
        else
            echo '<tr><td>Line:</td><td><i>Unknown</i></td></tr>';

        // Print the file
        if($t_file != null)
            echo '<tr><td>File:</td><td><span class="file">' . $t_file . '</span></td></tr>';
        else
            echo '<tr><td>File:</td><td><i>Unknown</i></td></tr>';

        // Print the code, if the file and line are known
        if($t_line != null && $t_file != null)
            echo '<tr><td>Code:</td><td style="padding-top: 4px;">' . self::getCode($t_file, $t_line) . '</td></tr>';

        ?>
        </table>
        </div>
        <?php
    }

    /**
     * Joins function arguments to they can be displayed properly
     * @param array $args $args Arguments to join
     * @return string Joined arguments as HTML
     */
    public static function joinArguments($args) {
        if(!is_array($args))
            return '';

        $out = '';
        $sep = '';
        foreach($args as $arg) {
            if(is_numeric($arg))
                $out .= $sep . $arg;

            else if(is_string($arg))
                $out .= $sep . '"' . $arg . '"';

            else if(is_bool($arg)) {
                if($arg)
                    $out .= $sep . 'true';
                else
                    $out .= $sep . 'false';

            } else if(is_object($arg))
                $out .= $sep . get_class($arg);

            else if(is_array($arg))
                $out .= $sep . 'Array(' . count($arg) . ')';

            else
                $out .= $sep . $arg;

            $sep = ', ';
        }

        // Return the $out contents
        return $out;
    }

    /**
     * Get the code of a file to display
     * @param string $file File to display the code of
     * @param int $line Line to show the code of
     * @return string Code frame as HTML
     */
    public static function getCode($file, $line) {
        // Read the file
        $lines = file($file);
        $out = '';

        $out .= '<div id="code">';
        $out .= '<div class="lines">';

        // Add the line numbers
        for($i = $line - 5; $i < $line + 4; $i++) {
            if(isset($lines[$i])) {
                if($i + 1 != $line)
                    $out .= '<code>' . ($i + 1) . '</code><br />';
                else
                    $out .= '<code style="font-weight: bold;">' . ($i + 1) . '</code><br />';
            }
        }

        $out .= '</div>';
        $out .= '<div class="code">';

        // Show the file lines
        for($i = $line - 5; $i < $line + 4; $i++) {
            if(isset($lines[$i])) {
                if($i + 1 != $line)
                    $out .= self::highlight($lines[$i]);
                else
                    $out .= '<span style="background: yellow; width: 100%; font-weight: bold; display: block;">' . self::highlight($lines[$i]) . '</span>';
            }
        }

        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }

    /**
     * Highlight PHP code in HTML format
     * @param string $str String to highlight
     * @return string Highlighted string
     */
    public static function highlight($str) {
        // Check if this line starts with the PHP opening tag, if not highlight the text
        if(strpos($str, '<?php') !== false)
            return highlight_string($str, true);

        // Highlight the PHP opening tag
        return preg_replace('/&lt;\?php&nbsp;/', '', highlight_string('<?php ' . $str, true));
    }

    /**
     * Show the context of a trace step
     * @param array $context Trace context
     */
    public static function showContext($context) {
        ?>
        <div id="code">
            <div class="code">
        <?php

        $i = 0;
        foreach($context as $name => $value) {
            // If the item is not the first item, a line break should be added
            if($i > 0)
                echo '<br /><br />';

            ob_start();
            echo '$' . $name . ' = ';
            self::printVariable($value);
            echo ';';
            $code = ob_get_clean();
            echo self::highlight($code);
            $i++;
        }

        ?>
            </div>
        </div>
        <?php
    }

    /**
     * Print a variable as HTML
     * @param mixed $value Variable value
     * @param int $tabs Amount of tabs to intent (default: 2)
     */
    public static function printVariable($value, $tabs = 2) {
        if(is_numeric($value)) {
            // Print a numeric value
            echo $value;

        } elseif(is_bool($value)) {
            // Render a boolean value
            if ($value)
                echo 'true';
            else
                echo 'false';

        } elseif(is_string($value)) {
            // Render a string value
            // TODO: Should use a sanatize method here?
            echo '"' . htmlspecialchars($value) . '"';

        } elseif(is_array($value)) {
            // Render an array
            echo 'Array(';
            if(empty($value)) {
                echo ")";
                return;
            }

            // Check whether the array is associative
            if(ArrayUtils::isAssoc($value)) {
                $first = true;
                foreach($value as $key => $val) {
                    if(!$first) {
                        echo ",";
                        $first = false;
                    }

                    echo "\n";
                    echo str_pad('', ($tabs + 1) * 4);
                    printf("\"%s\" => ", $key);
                    self::printVariable($val, $tabs + 1);
                }

            } else {
                // Ordinary array
                $first = true;
                foreach($value as $val) {
                    if(!$first) {
                        echo ",";
                        $first = false;
                    }
                    print "\n";
                    echo str_pad('',($tabs + 1) * 4);
                    self::printVariable($val, $tabs + 1);
                }
            }
            echo "\n";
            echo str_pad('', ($tabs) * 4);
            echo ")";

        } elseif(is_object($value)) {
            // Render an object
            $vars = get_object_vars($value);
            if (count($vars) === 0) {
                echo get_class($value) . '()';
                return;
            }
            echo get_class($value) . "(\n";
            foreach(get_object_vars($value) as $key => $val) {
                echo str_pad('', ($tabs + 1) * 4);
                printf("$%s = ", $key);
                self::printVariable($val, $tabs + 1);
                echo ";\n";
            }
            echo ")";

        } else {
            // Unsupported variable type, print the plain variable
            echo $value;
        }
    }

    /**
     * Destroy and unregister the error handler
     * @param bool $restore_error_handler [optional] False to keep the error handler registered
     * @param bool $restore_exception_handler [optional] False to keep the exception handler registered
     */
    public static function destroy($restore_error_handler = true, $restore_exception_handler = true) {
        // Restore the error and exception handlers
        if($restore_error_handler)
            restore_error_handler();
        if($restore_exception_handler)
            restore_exception_handler();
    }
}