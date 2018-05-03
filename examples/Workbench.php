<?php

require "../vendor/autoload.php";

// $id = "bbe4e96b-69b0-4784-93b0-be9674843775";
// $secret = "bfc406f66bfa1f96a4789a1ea1b703e982f78167089e4945b7f6ed998be1d81d";

// $client = new \WoganMay\DomoPHP\DomoPHP($id, $secret);

// $user = $client->API->User->getUser(2096906735);
// echo $user;

//$newUser = new \WoganMay\DomoPHP\Types\User;

//$newUser->name = "Test Invite User";
//$newUser->email = "wogan@amberstone.digital";
//$newUser->role = "Participant";

//$result = $client->API->User->createUser($newUser, false);

//var_dump($result);

//$user = $client->API->User->getUser(1634755944);

//$user->name = "New Name";

//$result = $client->API->User->updateUser($user);

// $user = $client->API->User->getUser(1634755944);
// $result = $client->API->User->deleteUser($user);

// $result = $client->API->User->getList();

// var_dump($result);

$builder = new \WoganMay\DomoPHP\PDPFilterBuilder();

$builder->equals("Fruit", [ "Apple", "Banana" ]);
$builder->notGreaterThan("Sales", 1000);
$builder->lessThanEqual("Units", 5000);

print_r($builder->render());