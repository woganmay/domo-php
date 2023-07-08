<?php

use PHPUnit\Framework\TestCase;
use WoganMay\DomoPHP\Client;


/**
 * User-API.yaml : /v1/users/{id} : parameters, get, put, delete
 * User-API.yaml : /v1/users : post, parameters, get
 */

final class UserServiceTest extends TestCase
{
    public function testListUsers()
    {
        $client = new Client();

        $listUsers = $client->user()->list();

        $this->assertIsArray($listUsers);

        // Assumes you have at least 1 user in your Domo instance, which should always be true,
        // since every instance is created with, by default, at least 1 service user.
        $this->assertGreaterThan(0, count($listUsers));

    }

    /**
     * @throws Exception
     */
    public function testCreateNewUser()
    {
        $client = new Client();

        // Ensure that the user we're about to create is unique
        $hash = uniqid();

        $userName = "User {$hash}";
        $userEmail = "{$hash}@example.org";
        $userAlternateEmail = "{$hash}-alt@example.org";

        $extra = [
            'sendInvite' => false,
            'title' => "Title {$hash}",
            'alternateEmail' => $userAlternateEmail,
            'phone' => '0123456789',
            'location' => "Location {$hash}",
            'timezone' => null,
            'locale' => null,
            'employeeNumber' => 12345
        ];

        $newUser = $client->user()->create($userName, $userEmail, "Participant", $extra);

        // Did the new user create with all the values we expect?
        $this->assertEquals("Title {$hash}", $newUser->title);
        $this->assertEquals($userEmail, $newUser->email);
        $this->assertEquals($userAlternateEmail, $newUser->alternateEmail);
        $this->assertEquals("Participant", $newUser->role);
        $this->assertEquals('0123456789', $newUser->phone);
        $this->assertEquals($userName, $newUser->name);
        $this->assertEquals("Location {$hash}", $newUser->location);
        $this->assertEquals(12345, $newUser->employeeNumber);

    }

    public function testFetchUser()
    {
        // We'll create a user, then try fetching the specific ID as a test
        $client = new Client();

        $hash = uniqid();
        $name = "FetchTest {$hash}";
        $email = "{$hash}-fetch@example.org";

        $newFetchUser = $client->user()->create($name, $email, "Participant");

        $newUserId = $newFetchUser->id;

        $this->assertIsInt($newUserId);

        // Now we should be able to fetch this one user
        $retrievedUser = $client->user()->get($newUserId);

        // If any field matches, we've fetched the user successfully
        $this->assertEquals($email, $retrievedUser->email);

    }

    public function testUpdateUser()
    {
        // We'll create a user, then try updating the specific ID as a test
        $client = new Client();

        $hash = uniqid();
        $name = "UpdateTest {$hash}";
        $email = "{$hash}-update@example.org";

        $newUpdateUser = $client->user()->create($name, $email, "Participant");

        $this->assertIsInt($newUpdateUser->id);
        $this->assertEquals($name, $newUpdateUser->name);

        // Now let's update it!
        $hash2 = str_shuffle($hash);

        $update = [
            'name' => "UpdateTest1 {$hash2}"
        ];

        // Update the user on the server
        $client->user()->update($newUpdateUser->id, $update);

        // Fetch it from scratch to ensure the change took effect
        $retrievedUser = $client->user()->get($newUpdateUser->id);

        // If this passes, the API has saved this change
        $this->assertEquals("UpdateTest1 {$hash2}", $retrievedUser->name);

    }

    public function testDeleteUser()
    {
        // We'll create a user, then try deleting it
        $client = new Client();

        $hash = uniqid();
        $name = "DeleteTest {$hash}";
        $email = "{$hash}-delete@example.org";

        $newDeleteUser = $client->user()->create($name, $email, "Participant");

        // Checks for HTTP Code == 204 under the hood, which is how the API responds to successful deletions
        $this->assertTrue($client->user()->delete($newDeleteUser->id));

    }
}