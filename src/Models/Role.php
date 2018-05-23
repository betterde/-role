<?php

namespace Betterde\Role\Models;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
use Betterde\Role\Contracts\RoleContract;
use Betterde\Role\Exceptions\RoleException;

/**
 * 系统角色模型
 *
 * Date: 18/04/2018
 * @author George
 * @property string $code
 * @method static where($column, $operator = null, $value = null, $boolean = 'and')
 * @package Betterde\Role\Models
 */
class Role extends Model implements RoleContract
{
    /**
     * 定义主键字段
     *
     * @var string
     * Date: 18/04/2018
     * @author George
     */
    protected $primaryKey = 'code';

    /**
     * 禁用主键自增
     *
     * @var bool
     * Date: 19/04/2018
     * @author George
     */
    public $incrementing = false;

    /**
     * 定义可填充字段
     *
     * @var array
     * Date: 18/04/2018
     * @author George
     */
    protected $fillable = ['code', 'name', 'guard'];

    /**
     * Role constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('role.table'));
        $attributes['guard'] = $attributes['guard'] ?? config('auth.defaults.guard');
    }

    /**
     * 获取所有角色
     *
     * Date: 19/04/2018
     * @author George
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function fetchAll()
    {
        if (config('role.cache.enable')) {
            $roles = collect(Redis::connection(config('role.cache.database'))->hvals(config('role.cache.prefix') . ':roles'))->map(function ($role) {
                return json_decode($role);
            });
            if ($roles->isNotEmpty()) {
                return $roles;
            }
            $roles = static::all();
            foreach ($roles as $role) {
                Redis::connection(config('role.cache.database'))->hset(config('role.cache.prefix') . ':roles', $role->code, $role);
            }
            return $roles;
        }

        return static::all();
    }

    /**
     * 根据编码查询角色
     *
     * Date: 19/04/2018
     * @author George
     * @param string $code
     * @return Role|\Illuminate\Database\Eloquent\Collection|Model|mixed
     */
    public static function findByCode(string $code)
    {
        if (config('role.cache.enable')) {
            $result = Redis::connection(config('role.cache.database'))->hget(config('role.cache.prefix') . ':roles', $code);
            $role = json_decode($result);
            if (! $role) {
                $role = static::findOrFail($code);
            }
        } else {
            $role = static::findOrFail($code);
        }

        return $role;
    }

    /**
     * 创建角色
     *
     * Date: 19/04/2018
     * @author George
     * @param array $attributes
     * @return $this|Model
     * @throws RoleException
     */
    public static function store(array $attributes)
    {
        try {
            $attributes['guard'] = $attributes['guard'] ?? config('auth.defaults.guard');
            $role = static::create($attributes);
            Redis::connection(config('role.cache.database'))->hset(config('role.cache.prefix') . ':roles', $role->code, $role);
            return $role;
        } catch (Exception $exception) {
            throw new RoleException($exception->getMessage(), 500);
        }
    }

    /**
     * 修改角色属性
     *
     * Date: 19/04/2018
     * @author George
     * @param string $code
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Collection|Model
     * @throws RoleException
     */
    public static function modify(string $code, array $attributes)
    {
        try {
            $role = static::findOrFail($code);
            $role->update($attributes);

            if (! $new_code = array_get($attributes, 'code') === $code) {
                DB::table(config('authorization.relation.role_permission'))->where('role_code', $code)->update(['role_code' => $new_code]);
                $value = Redis::connection(config('role.cache.database'))->hget(config('role.cache.prefix') . ':role_permissions', [$code]);
                Redis::connection(config('role.cache.database'))->hdel(config('role.cache.prefix') . ':role_permissions', [$code]);
                Redis::connection(config('role.cache.database'))->hset(config('role.cache.prefix') . ':role_permissions', array_get($attributes, 'code', $code), $value);
            }

            Redis::connection(config('role.cache.database'))->hdel(config('role.cache.prefix') . ':roles', [$code]);
            Redis::connection(config('role.cache.database'))->hset(config('role.cache.prefix') . ':roles', array_get($attributes, 'code', $code), $role);
            return $role;
        } catch (Exception $exception) {
            throw new RoleException('更新角色失败', 500);
        }
    }

    /**
     * 删除角色
     *
     * Date: 19/04/2018
     * @author George
     * @param string $code
     * @return bool
     * @throws RoleException
     */
    public static function remove(string $code)
    {
        try {
            $role = self::findOrFail($code);
            $role->delete();
            DB::table(config('authorization.relation.role_permission'))->where('role_code', $code)->delete();
            Redis::connection(config('role.cache.database'))->hdel(config('role.cache.prefix') . ':roles', [$code]);
            Redis::connection(config('role.cache.database'))->hdel(config('role.cache.prefix') . ':role_permissions', [$code]);
            return true;
        } catch (Exception $exception) {
            throw new RoleException('删除角色失败', 500);
        }
    }

    /**
     * 获取角色的所有权限
     *
     * Date: 19/04/2018
     * @author George
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(config('permission.model'), config('authorization.relation.role_permission'), 'role_code', 'permission_code', 'code', 'code');
    }

    /**
     * 获取角色的权限属性
     *
     * Date: 19/04/2018
     * @author George
     * @return array|mixed
     */
    public function getPermissionsAttribute()
    {
        if (config('authorization.cache.enable')) {
            $permissions = json_decode(Redis::connection(config('authorization.cache.database'))
                ->hget(config('authorization.cache.prefix') . ':role_permissions', $this->code));
        } else {
            $permissions = DB::table(config('authorization.relation.role_permission'))->select('permission_code')->get()->toArray();
            if (! empty($permissions) && config('authorization.cache.enable')) {
                Redis::connection(config('authorization.cache.database'))->hset(config('authorization.cache.prefix' . ':role_permissions'), $this->code, json_encode($permissions));
            }
        }

        return $permissions;
    }
}