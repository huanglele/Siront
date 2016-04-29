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

);