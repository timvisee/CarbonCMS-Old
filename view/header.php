<?php

/**
 * header.php
 * Main header set_file for views for Carbon CMS.
 * @author Tim Visée
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visée 2012-2013, All rights reserved.
 */

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_ROOT') or die('Access denied!');

use carbon\core\Core;

?>

<!DOCTYPE HTML>
<html>
<head>
    
    <title>Carbon CMS v<?=Core::getVersionName(); ?></title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <link href="/carbon_cms/theme/default/style/style.css" rel="stylesheet" type="text/css" />
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <?php
        if(isset($this->js)) {
            $site_url = $GLOBALS['carbon_config']->getValue('general', 'site_url');
            foreach($this->js as $js)
                echo '<script type="text/javascript" src="'.$site_url.'view/'.$js.'"></script>';
        }
    ?>
    
</head>
<body>
    
    <div id="menubar">
        <a href="/app/carbon_cms/">Index</a> <a href="help">Help</a> <a href="login">Login</a> <a href="dashboard">Dashboard</a> <a href="error">Error</a><br />
    </div>
    
    <div id="page">
