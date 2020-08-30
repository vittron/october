<?php namespace Clake\UserExtended\Classes;

use Clake\Userextended\Models\GroupsExtended;
use Clake\Userextended\Models\Role;
use October\Rain\Support\Collection;
use Illuminate\Support\Facades\Validator;

/**
 * User Extended by Shawn Clake
 * Class RoleManager
 * User Extended is licensed under the MIT license.
 *
 * @author Shawn Clake <shawn.clake@gmail.com>
 * @link https://github.com/ShawnClake/UserExtended
 *
 * @license https://github.com/ShawnClake/UserExtended/blob/master/LICENSE MIT
 *
 * Handles all interactions with roles on a group level (Global level)
 * @method static RoleManager with($groupCode) RoleManager
 * @package Clake\UserExtended\Classes
 */
class RoleManager extends StaticFactory
{
    /**
     * The group instance
     * @var
     */
    private $group;

    /**
     * A list of roles in that group
     * @var
     */
    private $roles;

    /**
     * Returns a list of roles not currently related to a group.
     * @return mixed
     */
    public static function getUnassignedRoles()
    {
        return Role::where('group_id', 0)->get();
    }

    /**
     * Creates a role and returns it after saving
     * @param $name
     * @param $description
     * @param $code
     * @param int $groupId
     * @return Role
     */
    public static function createRole($name, $description, $code, $groupId = 0)
    {
        if(empty($code))
            $code = $name;

        $code = str_slug($code, "-");

        $validator = Validator::make(
            [
                'name' => $name,
                'description' => $description,
                'code' => $code,
            ],
            [
                'name' => 'required|min:3',
                'description' => 'required|min:8',
                'code' => 'required',
            ]
        );

        if($validator->fails())
        {
            return $validator;
        }

        if(Role::code($code)->count() > 0)
            return false;

        $role = new Role();
        $role->name = $name;
        $role->description = $description;
        $role->code = $code;
        $role->group_id = $groupId;
        $role->save();
        return $validator;
    }

    /**
     * Deletes a role
     * TODO: Reset the role_id in UsersGroups associations back to 0 where this role was used. I believe this has been done now but should be checked before removing this TODO
     * @param $roleCode
     */
    public static function deleteRole($roleCode)
    {
        $role = RoleManager::findRole($roleCode);

        if(!isset($role))
            return;

        $role->delete();
    }

    /**
     * Updates a role
     * @param $roleCode
     * @param null $sortOrder
     * @param null $name
     * @param null $description
     * @param null $code
     * @param null $groupId
     * @param bool $ignoreChecks
     * @return bool|Validator
     */
    public static function updateRole($roleCode,
                                      $sortOrder = null,
                                      $name = null,
                                      $description = null,
                                      $code = null,
                                      $groupId = null,
                                      $ignoreChecks = false
    ) {
        $role = RoleManager::findRole($roleCode);

        if(isset($sortOrder)) $role->sort_order = $sortOrder;
        if(isset($name)) $role->name = $name;
        if(isset($description)) $role->description = $description;
        if(isset($code)) $role->code = str_slug($code, "-");
        if(isset($groupId)) $role->group_id = $groupId;

        $validator = Validator::make(
            [
                'name' => $role->name,
                'description' => $role->description,
                'code' => $role->code,
            ],
            [
                'name' => 'required|min:3',
                'description' => 'required|min:8',
                'code' => 'required',
            ]
        );

        if($validator->fails())
        {
            return $validator;
        }

        if(Role::code($role->code)->where('id', '<>', $role->id)->count() > 0)
            return false;

        if($role->group_id == 0)
            $ignoreChecks = true;

        $role->ignoreChecks = $ignoreChecks;
        $role->save();
        $role->ignoreChecks = false;

        return $validator;
    }

    /**
     * Finds and returns a role via RoleCode
     * @param $roleCode
     * @return mixed
     */
    public static function findRole($roleCode)
    {
        return Role::where('code', $roleCode)->first();
    }

    /**
     * Fills the class with a group model and role models for the group code passed in.
     * @param $groupCode
     * @return $this
     */
    public function withFactory($groupCode)
    {
        $this->group = GroupsExtended::where('code', $groupCode)->first();
        if($this->group != null)
            $this->roles = $this->group->roles;

        return $this;
    }

    /**
     * Returns a role model by passing a role code in.
     * This will only return a role if the role exists in the group.
     * @param $roleCode
     * @return bool
     */
    public function getRole($roleCode)
    {
        foreach($this->roles as $role)
        {
            if ($role->code == $roleCode)
                return $role;
        }
        return false;
    }

    /**
     * Filling the class with roles for the group code passed to this function
     * @param $code
     * @return $this
     */
    public function setGroup($code)
    {
        $this->group = GroupsExtended::where('code', $code)->first();
        if($this->group != null)
            $this->roles = $this->group->roles;
        return $this;
    }

    /**
     * Returns all the roles inside of a group
     * @return Role
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Goes through the roles attached to this instance and runs ->save() on each
     */
    public function saveRoles()
    {
        foreach($this->roles as $role)
            $role->save();
    }

    /**
     * Returns a count of roles in the selected group
     * @return mixed
     */
    public function countRoles()
    {
        return $this->roles->count();
    }

    /**
     * Moves a role higher up in the heirarchy for that group
     * @param $roleSortOrder
     */
    public function sortUp($roleSortOrder)
    {
        if($roleSortOrder < 2)
            return;

        $sorted = $this->getSortedGroupRoles();

        $movingUp = $sorted[$roleSortOrder];
        $movingDown = $sorted[$roleSortOrder - 1];

        $movingUp->sort_order = $roleSortOrder - 1;
        $movingDown->sort_order = $roleSortOrder;

        $movingUp->save();
        $movingDown->save();
    }

    /**
     * Moves a role lower down in the heirarchy for that group
     * @param $roleSortOrder
     */
    public function sortDown($roleSortOrder)
    {
        if($roleSortOrder > $this->countRoles() - 1)
            return;

        $sorted = $this->getSortedGroupRoles();

        $movingUp = $sorted[$roleSortOrder + 1];
        $movingDown = $sorted[$roleSortOrder];

        $movingUp->sort_order = $roleSortOrder;
        $movingDown->sort_order = $roleSortOrder + 1;

        $movingUp->save();
        $movingDown->save();
    }

    /**
     * Sorts the Collection of Roles by sort_order
     * @return $this
     */
    public function sort()
    {
        $sorted = $this->getSortedGroupRoles();

        $roles = new Collection();

        foreach($sorted as $role)
        {
            $roles->push($role);
        }

        $this->roles = $roles;

        return $this;
    }

    /**
     * Gets a list of roles in a group and sorts it by sort_order
     * Useful for promoting, demoting, and getting a sense of hierarchy.
     * @return array
     */
    public function getSortedGroupRoles()
    {
        $groupRoles = [];

        if(empty($this->roles))
            return [];

        foreach($this->roles as $role)
        {
            $groupRoles[$role["sort_order"]] = $role;
        }

        ksort($groupRoles);

        return $groupRoles;
    }

    /**
     * Fixes the role sort order
     */
    public function fixRoleSort()
    {
        $roles = RoleManager::with($this->group->code)->getSortedGroupRoles();

        $count = 0;
        foreach($roles as $role)
        {
            $count++;
            $role->sort_order = $count;
            $role->save();
        }

    }

}