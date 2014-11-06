<?php

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_ROOT') or die('Access denied!');

?>

<h1>Login</h1>

<form action="" method="POST">
    <label>Username:</label> <input type="text" name="username" /><br />
    <label>Password:</label> <input type="password" name="password" /><br />
    <label></label><input type="submit" />
</form>