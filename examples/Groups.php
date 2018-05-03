<?php

/**
 * Examples for working with Groups:
 *
 * 1. Retrieve a Group
 * 2. Create a Group
 * 3. Update a Group
 * 4. Delete a Group
 * 5. List Group
 * 6. Add a User to a Group
 * 7. List Users in a Group
 * 8. Remove a User from a Group
 *
 */

//
// Start
//
//
require "../vendor/autoload.php";

// Obtain from developer.domo.com
$id = "";
$secret = "";

$client = new \WoganMay\DomoPHP\DomoPHP($id, $secret);