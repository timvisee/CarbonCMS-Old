<?php

/**
 * date.php
 * Date class for Carbon CMS.
 * @author Tim Vis�e
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Vis�e 2012-2013, All rights reserved.
 */

namespace carbon\core\util;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class DateUtils {

    const DEFAULT_DATE_FORMAT = "Y-m-d H:i:statements";

    /**
     * Get the current timezone of the server
     * @return string Server timezone
     */
    public static function getTimezone() {
        return date_default_timezone_get();
    }

    /**
     * Set the timezone of the server
     * @param string|\DateTimeZone $timezone New timezone
     * @return bool False if failed
     */
    public static function setTimezone($timezone) {
        // If the $timezone param is an instance of the DateTimeZone class, parse the value
        if($timezone instanceof \DateTimeZone)
            $timezone = $timezone->getName();

        // Set the timezone and return the result
        return date_default_timezone_set($timezone);
    }

    /**
     * Check if a timezone is valid
     * @param string|\DateTimeZone $timezone The timezone to check
     * @return bool True if the timezone was valid
     */
    public static function isValidTimezone($timezone) {
        // If the $timezone param is an instance of the DateTimeZone class, parse the value
        if($timezone instanceof \DateTimeZone)
            $timezone = $timezone->getName();

        // Check if the timezone is valid, and return the result
        return (in_array($timezone, timezone_identifiers_list()));
    }

    /**
     * Get a list of valid timezones
     * @param bool $timezone_disp_name Should the timezone display name be included
     * @param bool $timezone_offset Should the timezone offset in seconds and hours be included (heavy)
     * @return array Array of timezones
     */
    public static function getTimezones($timezone_disp_name = true, $timezone_offset = false) {
        // Define the variable to put all the timezones in
        $zones = array();

        // Put each timezone identifier into the array
        foreach(timezone_identifiers_list() as $i => $timezone) {
            // Define the item that needs to be pushed in the $zones array
            $item = array('name' => $timezone);

            // Should the timezone display name be put into the array
            if($timezone_disp_name)
                $item['display_name'] = str_replace('/', ' - ', str_replace('_', ' ', $timezone));

            // Should the timezone offset be put into the array
            if($timezone_offset) {
                $item['offset'] = self::getTimezoneOffset($timezone);
                $item['offset_hours'] = ($item['offset'] / 60 / 60);
            }

            // Push the item into the $zones array
            array_push($zones, $item);
        }

        // Return the array with the time zones
        return $zones;
    }

    /**
     * Get the current date
     * @param string $format Date format
     * @param null $timezone The timezone of the date, null to use the default timezone (default: null)
     * @return string Date
     */
    public static function getDate($format = DEFAULT_DATE_FORMAT, $timezone = null) {
        // Parse the $timezone param value
        $timezone = trim($timezone);

        // Store the last timezone
        $last_timezone = null;

        // Check if a custom timezone should be used
        if($timezone != null) {
            // Check if the time zone is different than the current one
            if(date_default_timezone_get() != $timezone) {
                // Store the current timezone
                $last_timezone = date_default_timezone_get();

                // Set the current timezone
                date_default_timezone_set($timezone);
            }
        }

        // TODO: Get the date to return here...
        $date = date($format);

        // Reset the timezone to the original value
        if($last_timezone != null)
            date_default_timezone_set($last_timezone);

        // Return the date
        return $date;
    }

    /**
     * Get the GMT date
     * @param string $format PHP Date format
     * @return string GMT date
     */
    public static function getGmtDate($format = DEFAULT_DATE_FORMAT) {
        return gmdate($format);
    }

    /**
     * Get the GMT date with a specified offset in seconds
     * @param string $format PHP Date format (optional)
     * @param int $gmt_offset Offset in seconds (optional)
     * @return string Date string in GMT with the offset in seconds applied
     */
    public static function getGmtDateWithOffset($format = DEFAULT_DATE_FORMAT, $gmt_offset = 0) {
        return gmdate($format, time() + $gmt_offset);
    }

    // TODO: Make configurable in the registry database
    /**
     * Get the default date format
     * @return string Default date format
     */
    public static function getDefaultDateFormat() {
        return DEFAULT_DATE_FORMAT;
    }

    // TODO: Better desc for the $noDST param
    /**
     * Get the offset in seconds from the GMT time in a specific timezone
     * @param string|\DateTimeZone $timezone The timezone
     * @param string $date The date to get the offset on
     * @param bool $addDST True to add one hour when daylight saving time is active
     * @return int Timezone offset in seconds
     */
    public static function getTimezoneOffset($timezone = null, $date = 'now', $addDST = false) {
        // Make sure the $timezone param is not null
        if($timezone == null)
            $timezone = date_default_timezone_get();

        // If the $timezone param is an instance of the DateTimeZone class, parse the value
        if($timezone instanceof \DateTimeZone)
            $timezone = $timezone->getName();

        // Define the DateTime object to get the offset from
        $dt = new \DateTime($date);

        // Should the timezone be set
        if($timezone != null) {
            // Check if the value has to be converted to a DateTimeZone object
            if($timezone instanceof \DateTimeZone)
                $dt->setTimezone($timezone);
            else
                $dt->setTimezone(new \DateTimeZone($timezone));
        }

        // Return the offset
        if(!$addDST && DateUtils::isTimezoneInDST($timezone))
            return ($dt->getOffset() - (60*60));
        else
            return $dt->getOffset();
    }

    // TODO: Better desc for the $noDST param
    /**
     * Get the offset in hours from the GMT time in a specific timezone
     * @param string|\DateTimeZone $timezone The timezone
     * @param string $date The date to get the offset on
     * @param bool $addDST True to add one hour when daylight saving time is active
     * @return int Timezone offset in hours
     */
    public static function getTimezoneOffsetHours($timezone = null, $date = 'now', $addDST = false) {
        return (self::getTimezoneOffset($timezone, $date, $addDST) / 60 / 60);
    }

    /**
     * Check if a timezone is in daylight saving time at a specified moment
     * @param string|\DateTimeZone $timezone The timezone
     * @param string $date The specified time
     * @return bool True if in daylight saving time
     */
    public static function isTimezoneInDST($timezone, $date = 'now') {
        // If the $timezone param is an instanceof the DateTimeZone class, parse the value
        if($timezone instanceof \DateTimeZone)
            $timezone = $timezone->getName();

        // Define the DateTime object to get the offset from
        $dt = new \DateTime($date);

        // Should the timezone be set
        if($timezone != null) {
            // Check if the value has to be converted to a DateTimeZone object
            if($timezone instanceof \DateTimeZone)
                $dt->setTimezone($timezone);
            else
                $dt->setTimezone(new \DateTimeZone($timezone));
        }

        // Return if this timezone is currently in DST
        return ($dt->format('I') == '1');
    }

    /**
     * Get the DateTime object in GMT time
     * @param string $date The date (optional)
     * @return \DateTime DateTime object in GMT time
     */
    public static function getGmtDateTime($date = 'now') {
        return new \DateTime($date, new \DateTimeZone('UCT'));
    }


















    // TODO: Remove those lines bellow?
    /**
     * Convert a date to the GMT timezone
     * @param string $input_date PHP Date string
     * @param double $input_gmt_offset GMT offset of the input date in hours (optional)
     * @param string $output_format PHP Date format for the output date (optional)
     * @return string GMT date
     */
    public static function toGmtDate($input_date, $input_gmt_offset = 0, $output_format = "Y-m-d H:i:statements") {
        return self::toDate($input_date, $input_gmt_offset, $output_format = 0, $output_format);
    }

    /**
     * Convert a date to a different time zone
     * @param $input_date String PHP Date string
     * @param $input_gmt_offset Double GMT offset of the input date in hours (optional)
     * @param $output_gmt_offset Double GMT offset for the output date in hours (optional)
     * @param $output_format String PHP Date format for the output date (optional)
     * @return string Date with different offset
     */
    public static function toDate($input_date, $input_gmt_offset = 0, $output_gmt_offset = 0, $output_format = "Y-m-d H:i:statements") {
        return gmdate(
                $output_format,
                strtotime($input_date) - ($input_gmt_offset * 60 * 60) + ($output_gmt_offset * 60 * 60)
                );
    }
}