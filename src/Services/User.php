<?php

namespace WoganMay\DomoPHP\Services;

/**
 * DomoPHP User Service.
 *
 * Utility methods for working with users
 *
 * @author     Wogan May <wogan.may@gmail.com>
 * @license    MIT
 * @link       https://github.com/woganmay/domo-php
 */
class User
{
    private $Client = null;

    /**
     * oAuth Client ID.
     *
     * The Client ID obtained from developer.domo.com
     *
     * @param \WoganMay\DomoPHP\Client $APIClient An instance of the API Client
     */
    public function __construct(\WoganMay\DomoPHP\Client $APIClient)
    {
        $this->Client = $APIClient;
    }

    public function getUser($id = null)
    {
        if ($id == null)
            throw new \Exception("Need a valid User ID!");

        return $this->Client->getJSON("v1/users/$id?fields=all");

    }

    /**
     * @param string $name User's full name
     * @param string $email User's primary email address
     * @param string $role Admin, Privileged or Participant (default)
     * @param array $additionalFields (title, alternateEmail, phone, location, timezone, locale, employeeNumber)
     * @param bool $sendInvite Send an email invitation
     * @return \WoganMay\DomoPHP\json The JSON result of the create call
     * @throws \Exception
     */
    public function createUser($name, $email, $role = "Participant", $additionalFields = [], $sendInvite = false)
    {
        $url = '/v1/users' . (($sendInvite) ? "?sendInvite=true" : '');

        return $this->Client->postJSON($url, array_merge([
            'name' => $name,
            'email' => $email,
            'role' => $role
        ], $additionalFields));
    }

    /**
     * @param integer $id The User ID
     * @param string $email The User's email address
     * @param array $updates The array of updates to make
     * @return mixed
     */
    public function updateUser($id, $email, $updates = [])
    {
        return $this->Client->putJSON('/v1/users/'.$id, array_merge([ 'email' => $email ], $updates));
    }

    /**
     * @param integer $id User ID to delete
     * @return bool Whether the deletion was successful or not
     * @throws \Exception
     */
    public function deleteUser($id = null)
    {
        if ($id == null)
            throw new \Exception("Need a valid User ID!");

        $result = $this->Client->WebClient->delete("/v1/users/".$id, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->Client->getToken(),
            ],
        ]);

        return $result->getStatusCode() == 204;

    }

    /**
     * Get a List of Users.
     *
     * @param int $limit (Default 10) The number of users to return
     * @param int $offset (Default 0) Used for pagination
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getList($limit = 10, $offset = 0)
    {
        $url = sprintf('/v1/users?offset=%s&limit=%s', $offset, $limit);

        return $this->Client->getJSON($url);
    }

    /**
     * @param $input The array of input to validate
     * @param $required A list of required fields to check for
     * @return bool Whether or not the array contains all the keys
     */
    private function validate($input, $required)
    {
        $valid = true;

        foreach($required as $key => $value)
        {
            if (!isset($input[$key])) $valid = false;
        }

        return $valid;
    }

}
