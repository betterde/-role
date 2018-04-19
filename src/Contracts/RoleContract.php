<?php

namespace Betterde\Role\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface RoleContract
 * @package Betterde\Role\Contracts
 * Date: 18/04/2018
 * @author George
 */
interface RoleContract
{
    /**
     * 获取所有角色信息
     *
     * Date: 19/04/2018
     * @author George
     * @return Collection
     */
    public static function fetchAll();

    /**
     * 根据主键获取角色信息
     *
     * Date: 19/04/2018
     * @author George
     * @param string $code
     * @return mixed
     */
    public static function findByCode(string $code);

    /**
     * 创建角色信息
     *
     * Date: 19/04/2018
     * @author George
     * @param array $attributes
     * @return mixed
     */
    public static function store(array $attributes);

    /**
     * 修改角色信息
     *
     * Date: 19/04/2018
     * @author George
     * @param string $code
     * @param array $attributes
     * @return mixed
     */
    public static function modify(string $code, array $attributes);

    /**
     * 删除角色信息
     *
     * Date: 19/04/2018
     * @author George
     * @param string $code
     * @return mixed
     */
    public static function remove(string $code);
}