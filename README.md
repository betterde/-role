## 使用说明

#### 发布配置文件到项目
```
php artisan vendor:publish --tag=role
```
#### 配置文件

文件所在目录：项目根目录下的 config/role.php

```php

<?php

return [
    // 自定义模型
    'model' => Betterde\Role\Models\Role::class,
    // 自定义数据表
    'table' => 'roles',
    // 自定义缓存
    'cache' => [
        // 是否开启缓存
        'enable' => true,
        // 缓存命名空间前缀
        'prefix' => 'betterde',
        // 缓存的数据库配置
        'database' => 'cache'
    ]
];
```

#### 常用命令
```
php artisan role:create CODE NAME GUARD
```
CODE: 角色编码
NAME：角色名称
GUARD: Guard name (请差参考config/auth配置文件的guard)

```
// 缓存系统角色到Redis
php artisan role:cache
```

```
// 清空缓存
php artisan role:flush
```

#### 自定义模型

如果需要自定义模型，只需要替换配置文件中 `model` 的指向，新的模型需要实现 `Betterde\Role\Contracts\RoleContract` 这个接口中的方法！

如果需要自定义表，只需要替换配置文件中的 `table` 即可!