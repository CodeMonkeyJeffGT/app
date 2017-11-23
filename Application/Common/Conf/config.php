<?php
return array(

    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',                    // 数据库类型
    'DB_HOST'               =>  '127.0.0.1',                // 服务器地址
    'DB_NAME'               =>  'app',                      // 数据库名
    'DB_USER'               =>  'root',                     // 用户名
    'DB_PWD'                =>  'GT338570',                 // 密码
    'DB_PORT'               =>  '3306',                     // 端口

    /* 日志设置 */
    'LOG_RECORD'            =>  true,                       // 默认不记录日志
    'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR',     // 允许记录的日志级别
    'LOG_EXCEPTION_RECORD'  =>  true,                       // 是否记录异常信息日志

    /* SESSION设置 */
    'SESSION_AUTO_START'    =>  false,                      // 是否自动开启Session
    
    /* URL设置 */
    'URL_DENY_SUFFIX'       =>  'ico|png|gif|jpg',          // URL禁止访问的后缀设置
    'URL_ROUTER_ON'         =>  true,                       // 是否开启URL路由
    'URL_ROUTE_RULES'       =>  array(                      // 默认路由规则 针对模块
        /* 用户管理(全在UserControl控制器内) */
        'login'        => 'Api/UserControl/login',          //登陆
        'signup'       => 'Api/UserControl/signup',         //注册
        'transToken'   => 'Api/UserControl/transToken',     //解释token的payload

        /* 用户 */
        'users'        => 'Api/Users/index',                //用户信息
        'follows'      => 'Api/Follows/index',              //关注管理

        /* 动态 */
        'dynamics'     => 'Api/Dynamics/index',             //动态管理
        'dynamicLike'  => 'Api/DynamicLike/index',          //动态点赞
        'comments'     => 'Api/Comments/index',             //评论管理
        'commentLike'  => 'Api/CommentLike/index',          //评论点赞
        'pics'         => 'Api/Pics/index',                 //图片管理

        /* 聊条记录 */
        'chatHistory'  => 'Api/ChatHistory/index',          //聊天记录

        /* 地图 */
        //TODO...
    )
);