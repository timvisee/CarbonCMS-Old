<?php

/**
 * header.php
 * Main header file for views for Carbon CMS.
 * @author Tim Visée
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visée 2012-2013, All rights reserved.
 */

// Prevent users from accessing this file directly
defined('CARBON_ROOT') or die('Access denied!');

?>

<!DOCTYPE HTML>
<html>
<head>
    
    <title>Carbon CMS &middot; Pre-Alpha</title>
    
    <link href="/carbon_cms/theme/default/style/style.css" rel="stylesheet" type="text/css" />
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <?php
        if(isset($this->js)) {
            $site_url = $GLOBALS['carbon_config']->getValue('general', 'site_url');
            foreach($this->js as $js) {
                echo '<script type="text/javascript" src="'.$site_url.'view/'.$js.'"></script>';
            }
        }
    ?>
    
</head>
<body>
    
    <div id="menubar">
        <a href="/app/carbon_cms/">Index</a> <a href="help">Help</a> <a href="login">Login</a> <a href="dashboard">Dashboard</a> <a href="error">Error</a><br />
    </div>
    
    <div id="page">
