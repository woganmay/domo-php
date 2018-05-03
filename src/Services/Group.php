<?php

namespace WoganMay\DomoPHP\Services;

/**
 * DomoPHP Group.
 *
 * Utility methods for working with groups
 *
 * @author     Wogan May <wogan.may@gmail.com>
 * @link       https://github.com/woganmay/domo-php
 */
class Group
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

    /**
     * Get a List of Groups.
     *
     * @param int $limit (Default 10) The number of groups to return
     * @param int $offset (Default 0) Used for pagination
     * @return mixed
     * @throws \Exception
     */
    public function getList($limit = 10, $offset = 0)
    {
        return $this->Client->getJSON("/v1/groups?offset=$offset&limit=$limit");
    }

    /**
     * @param integer $id Group ID
     * @return mixed The Group
     * @throws \Exception
     */
    public function getGroup($id)
    {
        return $this->Client->getJSON("v1/groups/$id");
    }

    /**
     * @param $name Group Name
     * @return string
     */
    public function createGroup($name)
    {
        return $this->Client->postJSON('/v1/groups', [ 'name' => $name ]);
    }

    /**
     * @param $id The Group ID to update
     * @param $name The new name to set
     * @return mixed
     */
    public function renameGroup($id, $name)
    {
        return $this->Client->putJSON("/v1/groups/$id", [
            'name' => $name,
            'active' => true,
            'default' => false
        ]);
    }

    /**
     * @param $id The Group ID to activate
     * @return mixed
     */
    public function activateGroup($id)
    {
        $group = $this->getGroup($id);

        return $this->Client->putJSON("/v1/groups/$id", [
            'name' => $group->name,
            'active' => true,
            'default' => false
        ]);
    }

    /**
     * @param $id The Group ID to activate
     * @return mixed
     */
    public function deactivateGroup($id)
    {
        $group = $this->getGroup($id);

        return $this->Client->putJSON("/v1/groups/$id", [
            'name' => $group->name,
            'active' => false,
            'default' => false
        ]);
    }

    /**
     * @param integer $id The Group ID to delete
     * @return bool Whether or not the group was deleted
     * @throws \Exception
     */
    public function deleteGroup($id)
    {
        return $this->Client->delete("/v1/groups/$id");
    }

    /**
     * @param integer $id Group ID
     * @param integer $user_id User ID to add
     * @return object
     * @throws \Exception
     */
    public function addUser($id, $user_id)
    {
        return $this->Client->putJSON("/v1/groups/$id/users/$user_id");
    }

    /**
     * @param integer $id The group ID to get users for
     * @return object
     * @throws \Exception
     */
    public function listUsers($id)
    {
        return $this->Client->getJSON("/v1/groups/$id/users");
    }

    /**
     * @param integer $id The Group ID to remove a user from
     * @param integer $user_id The User ID to remove
     * @return bool
     * @throws \Exception
     */
    public function removeUser($id, $user_id)
    {
        return $this->Client->delete("/v1/groups/$id/users/$user_id");
    }


}