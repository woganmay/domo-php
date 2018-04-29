<?php

/**
 * Examples for working with Users:
 *
 * 1. Retrieve a User
 * 2. Create a User
 * 3. Update a User
 * 4. Delete a User
 * 5. List Users
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

//
// 1. Retrieve a single user
//
$user = $client->API->User->getUser(1234567890);

//
// 2. Create a User
//
$fields = [

    // Required
    'name'           => '',
    'email'          => '',
    'role'           => '', // Admin, Privileged, Participant

    // Optional
    'title'          => '',
    'alternateEmail' => '',
    'phone'          => '',
    'location'       => '',
    'timezone'       => '',
    'locale'         => '',
    'employeeNumber' => ''
];

$sendInvite = true;

$user = $client->API->User->createUser($fields, $sendInvite);

//
// 3. Update a User
//
$userId = 1234567890;

$fields = [
    // Optional
    'name'           => '',
    'email'          => '',
    'role'           => '', // Admin, Privileged, Participant
    'title'          => '',
    'alternateEmail' => '',
    'phone'          => '',
    'location'       => '',
    'timezone'       => '',
    'locale'         => '',
    'employeeNumber' => ''
];

$user = $client->API->User->updateUser($userId, $fields);

//
// 4. Delete a user
//

$result = $client->API->User->deleteUser(1234567890);

//
// 5. List Users
//

$users = $client->API->User->getList(100, 0);