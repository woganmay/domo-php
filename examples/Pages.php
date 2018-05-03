<?php

/**
 * Examples for working with Pages:
 *
 * 1. Retrieve a Page
 * 2. Create a Page
 * 3. Update a Page
 * 4. Delete a Page
 * 5. List Pages
 * 6. Get Collections on a Page
 * 7. Create a new Page Collection
 * 8. Update a Page Collection
 * 9. Delete a Page Collection
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