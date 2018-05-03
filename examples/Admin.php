<?php

/**
 * Examples for working with Streams:
 *
 * 1. Retrieve a Stream
 * 2. Create a Stream
 * 3. Update a Stream
 * 4. Delete a Stream
 * 5. List Stream
 * 6. Get a single Stream Execution
 * 7. Create a new Stream Execution
 * 8. List Stream Executions
 * 9. Upload a Data Part to an Execution
 * 10. Commit a Stream Execution
 * 11. Abort a Stream Execution
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