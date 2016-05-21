<?php
return array(
    //加载扩展配置文件
    'LOAD_EXT_CONFIG' => 'site',

	//定义数据库
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'siront',
    'DB_USER' => 'siront',
    'DB_PWD' => 'siront123',
    'DB_CHARSET'=> 'utf8',
    'DB_PREFIX' => 'siront_',

    'SHOW_PAGE_TRACE' => true,
    //定义模板信息
    'TMPL_L_DELIM' => '<{',
    'TMPL_R_DELIM' => '}>',

    //定义模块
    'MODULE_ALLOW_LIST'    =>    array('Home','Admin'),
    'DEFAULT_MODULE'       =>    'Home',

    //设置session的方式
//    'SESSION_TYPE'=>'File',

    'IconCssLink' => '//at.alicdn.com/t/font_1463812658_166627.css',

    //任务状态
    'TaskStatus' => array(
        '-1' => '全部',
        '1' => '发布中',
        '2' => '已接单',
        '3' => '已完成',
        '4' => '已评价',
        '5' => '放弃',
        '6' => '删除',
    )
);