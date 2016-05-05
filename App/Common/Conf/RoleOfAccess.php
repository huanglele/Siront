<?php
/**
 * Author: huanglele
 * Date: 2016/4/29
 * Time: 下午 03:16
 * Description: 定义权限数组
 */
return array(
    //控制器.方法 => array(需要的用户权限)
    'index.index' => array(0,1,2,4,5,6),

    /*用户管理部分*/
//    'user.index' => array(),        //显示所有的用户
//    'user.person' => array(),        //显示所有的个体户
//    'user.company' => array(),        //显示所有的企业
//    'user.userinfo' => array(),       //单个用户的详细信息
//    'user.personinfo' => array(),       //单个用户的详细信息
//    'user.companyinfo' => array(),       //单个用户的详细信息
//    'user/addcompany' => array(),     //添加一个企业用户

    /*任务管理部分*/
    //'task.index' => array(),      //显示所有的任务

);