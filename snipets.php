<?php

/*
This file contains some code snipets which are going to be used later on
*/

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

// TODO: Use to enable GZip if enabled in settings
/**
 * Enable or disable GZip compression
 */
function enableGzipCompression() {
    // Get all supported encoding
    $supported_encoding = explode(', ', $_SERVER['HTTP_ACCEPT_ENCODING']);

    // Check if GZip compression is supported
    $gzip_supported = in_array('gzip', $supported_encoding);
    if($gzip_supported) {
        // Enable GZip compression
        ob_start("ob_gzhandler");
    }
}

// TODO: Use for IP blocking/tracking/site usage statistics
/**
 * Get the IP address of the client
 * @return string IP address of the client, returns 0.0.0.0 when the address wasn't detected
 */
function getClientIp() {
    if(isset($_SERVER["REMOTE_ADDR"]))
        return $_SERVER["REMOTE_ADDR"];
    else if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    else if(isset($_SERVER["HTTP_CLIENT_IP"]))
        return $_SERVER["HTTP_CLIENT_IP"];
    else
        return '0.0.0.0';
}


function write_ini_file($file, array $options){
    $tmp = '';
    foreach($options as $section => $values){
        $tmp .= "[$section]\n";
        foreach($values as $key => $val){
            if(is_array($val)){
                foreach($val as $k =>$v){
                    $tmp .= "{$key}[$k] = \"$v\"\n";
                }
            }
            else
                $tmp .= "$key = \"$val\"\n";
        }
        $tmp .= "\n";
    }
    file_put_contents($file, $tmp);
    unset($tmp);
}