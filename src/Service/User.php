<?php

namespace WoganMay\DomoPHP\Service;

use WoganMay\DomoPHP\Client;
use WoganMay\DomoPHP\Util;

class User
{

    const ALLOWED_USER_FIELDS = [
        'name',
        'email',
        'role',
        'sendInvite',     // (boolean) Send an email invite to created user
        'title',          // (string) User's job title
        'alternateEmail', // (string) User's secondary email in profile
        'phone',          // (string) Primary phone number of user
        'location',       // (string) Free text that can be used to define office location (e.g. City, State, Country)
        'timezone',       // (string) Time zone used to display to user the system times throughout Domo application
        'locale',         // (string) Locale used to display to user the system settings throughout Domo application
        'employeeNumber'  // (string) Employee number within company
    ];

    public function __construct(private Client $client) { }

    public function list(int $limit = 50, int $offset = 0) : array
    {
        return $this->client->connector()->getJSON('/v1/users', [
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    /**
     * @param string $name The name of the user
     * @param string $email The user's email address
     * @param string $role One of Participant, Privileged or Admin
     * @param array|null $extra An array of extra properties to set when creating the user
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(string $name, string $email, string $role, ?array $extra = []) : \stdClass
    {

        // The Role can only be one of three values
        if (!in_array($role, ['Admin', 'Privileged', 'Participant']))
            throw new \Exception("Invalid role: $role - must be one of: Admin, Privileged, Participant");

        // The $extra array is only allowed to have specific fields - for safety's sake, we'll remove anything that the
        // API can't support before attempting the request.
        return $this->client->connector()->postJSON('/v1/users', array_merge([
            'name' => $name,
            'email' => $email,
            'role' => $role
        ], Util::trimArrayKeys($extra, self::ALLOWED_USER_FIELDS)));

    }

    public function get(int $id) : \stdClass
    {
        return $this->client->connector()->getJSON("/v1/users/{$id}");
    }

    public function update(int $id, ?array $update = []) : \stdClass
    {

        if (!array_key_exists('email', $update))
        {
            // 2028-07-08: Known limitation of the Domo API: the email field is required on this call, even if it is
            // not being changed. As a result, you'll either need to pass in the email when calling this update() method,
            // but if not, we'll look up the existing user's email before trying the update.

            $existingUser = $this->get($id);
            $update['email'] = $existingUser->email;
        }

        return $this->client->connector()->putJSON("/v1/users/{$id}", Util::trimArrayKeys($update, self::ALLOWED_USER_FIELDS));
    }

    public function delete(int $id) : bool
    {
        return $this->client->connector()->delete("/v1/users/{$id}");
    }
}