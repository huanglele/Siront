<?php
/**
 * Created by PhpStorm.
 * author: huanglele
 * Date: 2016/5/21
 * Time: 10:18
 * Description:
 */

namespace Mobile\Model;

use Think\Model;

class UserModel extends Model
{
    //自动验证
    protected $_validate = array(
        //array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
        array('phone','require','手机号不能为空'),
        array('phone','','手机号已经存在！',0,'unique',1), // 在新增的时候验证手机号字段是否唯一

    );


}