<?php

namespace app\admin\entity;

class LoginUser
{
    /**
     * @var int 用户ID
     */
    private $id;
    /**
     * @var string 用户名
     */
    private $name = '';
    /**
     * @var string 用户头像
     */
    private $avatar = '';
    /**
     * @var array 角色列表
     */
    private $roleList = [];
    /**
     * @var int 角色ID
     */
    private $roleId = 0;
    /**
     * @var array 菜单列表
     */
    private $menuList = [];
    /**
     * @var array 权限列表
     */
    private $permissionList = [];
    /**
     * @var int 超级管理员角色ID
     */
    private static $superAdministratorId = 1;
    /**
     * @var self 登录用户对象实例
     */
    private static $instance;

    /**
     * LoginUser constructor.
     * @param int    $id
     * @param string $name
     * @param array  $roleList
     * @param int    $roleId
     * @param array  $menuList
     * @param array  $permissionList
     * @param string $avatar
     */
    public function __construct(
        int $id,
        string $name,
        array $roleList,
        int $roleId,
        array $menuList,
        array $permissionList,
        string $avatar = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->avatar = $avatar;
        $this->roleList = $roleList;
        $this->roleId = $roleId;
        $this->menuList = $menuList;
        $this->permissionList = $permissionList;
    }

    /**
     * @return LoginUser
     */
    public static function getInstance(): LoginUser
    {
        return self::$instance;
    }

    /**
     * @title setInstance
     * @param int    $id
     * @param string $name
     * @param array  $roleList
     * @param int    $roleId
     * @param array  $menuList
     * @param array  $permissionList
     * @param string $avatar
     * @return LoginUser
     */
    public static function setInstance(
        int $id,
        string $name,
        array $roleList,
        int $roleId,
        array $menuList,
        array $permissionList,
        string $avatar = ''
    ): LoginUser {
        self::$instance = new self($id, $name, $roleList, $roleId, $menuList, $permissionList, $avatar);
        return self::$instance;
    }

    /**
     * @title resetInstance
     * @param LoginUser $instance
     */
    public static function resetInstance(self $instance)
    {
        self::$instance = $instance;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar(string $avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return array
     */
    public function getRoleList(): array
    {
        return $this->roleList;
    }

    /**
     * @param array $roleList
     */
    public function setRoleList(array $roleList)
    {
        $this->roleList = $roleList;
    }

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->roleId;
    }

    /**
     * @param int $roleId
     */
    public function setRoleId(int $roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     * @return array
     */
    public function getMenuList(): array
    {
        return $this->menuList;
    }

    /**
     * @param array $menuList
     */
    public function setMenuList(array $menuList)
    {
        $this->menuList = $menuList;
    }

    /**
     * @return array
     */
    public function getPermissionList(): array
    {
        return $this->permissionList;
    }

    /**
     * @param array $permissionList
     */
    public function setPermissionList(array $permissionList)
    {
        $this->permissionList = $permissionList;
    }

    /**
     * @return bool
     */
    public static function isSuperAdministrator(): bool
    {
        return self::getInstance()->getRoleId() === self::$superAdministratorId;
    }

    /**
     * @return int
     */
    public static function getSuperAdministratorId(): int
    {
        return self::$superAdministratorId;
    }

    /**
     * @title getUserId
     * @return int
     */
    public static function getUserId(): int
    {
        return self::getInstance()->getId();
    }

    /**
     * @title toArray
     * @return array
     */
    public function toArray(): array
    {
        return [
            'avatar' => $this->avatar,
            'name' => $this->name,
            'roles' => $this->roleList,
            'menu' => $this->menuList,
            'permission' => $this->permissionList
        ];
    }
}
