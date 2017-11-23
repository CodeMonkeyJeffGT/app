<?php
return array(

    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'app',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '3306',        // 端口

    /* 日志设置 */
    'LOG_RECORD'            =>  true,   // 默认不记录日志
    'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR',// 允许记录的日志级别
    'LOG_EXCEPTION_RECORD'  =>  true,    // 是否记录异常信息日志

    /* SESSION设置 */
    'SESSION_AUTO_START'    =>  false,    // 是否自动开启Session
    
    /* URL设置 */
    'URL_DENY_SUFFIX'       =>  'ico|png|gif|jpg', // URL禁止访问的后缀设置
    'URL_ROUTER_ON'         =>  true,   // 是否开启URL路由
    'URL_ROUTE_RULES'       =>  array(  // 默认路由规则 针对模块
        'dynamicPages' => 'Api/DynamicPages/index',
        'dynamics'     => 'Api/Dynamics/index',
        'login'        => 'Api/Login/index',
        'signup'       => 'Api/Signup/index',
        'comments'     => 'Api/Comments/index',
        'users'        => 'Api/Users/index',
        'follows'      => 'Api/Follows/index',
        'pics'         => 'Api/Pics/index',
        'dynamicLike'  => 'Api/DynamicLike/index',
        'commentLike'  => 'Api/CommentLike/index'
    )
);