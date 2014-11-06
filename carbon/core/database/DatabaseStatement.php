<?php

namespace carbon\core\database;

use carbon\core\util\StringUtils;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

abstract class DatabaseStatement {

    // TODO: Choose proper characters for the constants bellow!

    /** string Prefix for dynamic fields */
    const DYNAMIC_PREFIX = '{{';
    /** string Suffix for dynamic fields */
    const DYNAMIC_SUFFIX = '}}';
    /** string Dynamic table identifier */
    const DYNAMIC_TABLE_IDENTIFIER = '#';
    /** string Dynamic parameter identifier */
    const DYNAMIC_PARAM_IDENTIFIER = ':';

    /** @var array $params Array containing all bound parameters */
    private $params = Array();

    /**
     * Bind a variable as parameter. The value of the variable will be evaluated when the query is executed.
     * This method allows you to supply an array of parameters for $param to bind multiple parameters at once that
     * should have the same variable. If an array of parameter identifiers is supplied for $param, $var will be bound to
     * all parameters. Parameters that already exists will be overwritten.
     *
     * @param string|int|array $param Parameter identifier, or array of parameter identifiers.
     * @param mixed &$var Parameter variable.
     *
     * @return int Amount of parameters that where bound successfully.
     */
    public function bindParam($param, &$var) {
        // Make sure $param isn't null
        if($param === null)
            return 0;

        // Check whether $param should be handled as an array
        if(!is_array($param)) {
            // Make sure the parameter identifier is valid
            if(!self::isValidParamIdentifier($param))
                return false;

            // Store the parameter variable reference
            $this->params[$param] = &$var;
            return 1;

        } else {
            // Count the amount of parameters that where bound successfully
            $count = 0;

            // Bind each parameter
            for($i = 0; $i < sizeof($param); $i++)
                // Bind the parameter and increase $count if the parameter was bound successfully
                $count += $this->bindParam($param[$i], $var);

            // Return the amount of parameters that where bound successfully
            return $count;
        }
    }

    /**
     * Bind a value as parameter. This method allows you to supply an array of parameters for $param and a array of
     * values for $var to bind multiple parameters or values at once. If an array of parameter identifiers is supplied
     * for $param and $var is a single value, the value will be bound to all parameters. If an array of parameter
     * identifiers is supplied for $param and an array of values is supplied for $var each parameter will get it's own
     * value. If the $val array has less elements than the $param value, the first value will be bound to all
     * parameters. Parameters that already exists will be overwritten.
     *
     * @param string|int|array $param Parameter identifier, or array of parameter identifiers.
     * @param mixed|array $val Parameter variable, or an array of parameter values.
     *
     * @return int Amount of parameters that where bound successfully.
     */
    public function bindParamValue($param, $val) {
        // Make sure $param isn't null
        if($param === null)
            return 0;

        // Check whether $param should be handled as an array
        if(!is_array($param)) {
            // Make sure the parameter identifier is valid
            if(!self::isValidParamIdentifier($param))
                return false;

            // Make sure $val isn't an array, use the first element of the array if this is the case, null will be used
            // if the array was empty
            if(is_array($val)) {
                if(sizeof($val) > 0)
                    $val = $val[0];
                else
                    $val = null;
            }

            // Store the parameter value
            $this->params[$param] = $val;
            return 1;

        } else {
            // Count the amount of parameters that where bound successfully
            $count = 0;

            // Determine whether $val should be handled as array
            $valAsArray = false;
            if(is_array($val))
                if(sizeof($val) >= sizeof($param))
                    $valAsArray = true;

            // Bind each parameter
            for($i = 0; $i < sizeof($param); $i++) {
                // Get the current parameter entry
                $entry = $param[$i];

                // Get the value for the current parameter entry
                if($valAsArray)
                    $entryVal = $val[$i];
                else
                    $entryVal = $val;

                // Bind the parameter and increase $count if the parameter was bound successfully
                $count += $this->bindParamValue($entry, $entryVal);
            }

            // Return the amount of parameters that where bound successfully
            return $count;
        }
    }

    /**
     * Get the value of a parameter. Variable parameters will return their value and will be evaluated at the time this
     * method is being called. This method also allows you to supply an array of parameter values, in this case an array
     * with parameter values will be returned.
     *
     * @param string|int|array $param Parameter identifier or an array of parameter identifiers.
     *
     * @return mixed|array|null The value of the parameter. An array with all parameter values will be returned if an
     * array of parameters was supplied, some of these values may be null if the corresponding parameter identifier was
     * invalid or unknown. Null will be returned if the parameter identifier is invalid or unknown.
     */
    public function getParam($param) {
        // Make sure $param isn't null
        if($param === null)
            return null;

        // Check whether $param should be handled as a parameter or an array of parameters
        if(!is_array($param)) {
            // Make sure this parameter is bound, if not, return null
            if(!$this->isParam($param))
                return null;

            // Get and return the parameter value
            return $this->params[$param];

        } else {
            // Create an array to store the output in
            $output = Array();

            // Get the value of each parameter
            foreach($param as $entry)
                // Get the value for this parameter and push it into the output array
                array_push($output, $this->getParam($entry));

            // Return the output array
            return $output;
        }
    }

    /**
     * Get a list of all bound parameters
     *
     * @return array Array of all bound parameters
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Get the count of bound parameters.
     *
     * @return int Count of bound parameters
     */
    public function getParamCount() {
        return sizeof($this->params);
    }

    /**
     * Check whether a parameter is bound
     *
     * @param string|int $param Parameter identifier
     *
     * @return bool True if this parameter has been bound, false otherwise
     */
    public function isParam($param) {
        // Make sure the parameter identifier is valid
        if(!self::isValidParamIdentifier($param))
            return false;

        // Check whether this param or value is bound, return the result
        return array_key_exists($param, $this->params);
    }

    /**
     * Determine whether a parameter identifier is valid.
     * The parameter identifier must be a string or integer. A string identifier must have at least one character,
     * and may not contain any whitespace characters. An integer identifier must be 1 or greater.
     *
     * @param string|int $param Parameter identifier to check.
     *
     * @return bool True if the parameter identifier is valid, false otherwise
     */
    public function isValidParamIdentifier($param) {
        // Check whether the parameter key is a string, if so, make sure it doesn't contain any whitespace characters
        if(is_string($param))
            return !StringUtils::containsWhitespaces($param, false);

        // Check whether the parameter key is an integer, if so, make sure the integer is 1 or greater
        if(is_int($param))
            return ($param > 0);

        // The parameter key doesn't seem to be a string or integer, return false
        return false;
    }

    /**
     * Check whether an object is a valid Database Statement. A comprehensive check also ensures the contents of the
     * database statement are valid. This method might be expensive if a comprehensive check is done.
     *
     * @param mixed $statement Database statement object.
     * @param bool $comprehensiveCheck True to do a comprehensive check which ensures the contents of the database
     * statement are valid, false to skip this check. Using comprehensive checks might be expensive.
     *
     * @return bool True if the $statement object is a valid Database Statement, false otherwise. False will be returned
     * if $statement equals null.
     */
    public static function isValid($statement, $comprehensiveCheck = false) {
        // Make sure the statement is valid
        if($statement === null)
            return false;
        if(!($statement instanceof DatabaseStatement))
            return false;

        // Check whether we should do a comprehensive check
        if($comprehensiveCheck) {
            // TODO: Do a comprehensive check by checking whether the contents of the database statement are valid!
        }

        // The database statement seems to be valid, return the result
        return true;
    }












    /*public static function parse() {

    }

    // TODO: Execute method, to run the (prepared) statement, allow additional parameters as function arguments!
    public abstract function execute();

    // TODO: Rename this to getStatement?
    public abstract function getStatement();*/









    private $statement = '';

    public function __construct($statement = '') {
        $this->statement = $statement;
    }
}
