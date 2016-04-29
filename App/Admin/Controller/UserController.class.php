<?php
/**
 * Author: huanglele
 * Date: 2016/4/29
 * Time: 下午 03:29
 * Description:
 */

namespace Admin\Controller;


class UserController extends CommonController
{
    /**
     * 显示所有的用户
     */
    public function index(){

    }

    /**
     * 显示普通用户
     */
    public function user(){
        $status = I('get.status',-1,'number_int');
        if($status!='-1'){
            $map['user_status'] = $status;
        }
        $this->assign('status',$status);
        $map['person_status'] = 0;
        $map['company_status'] = 0;
        $field = 'uid,nickname,headimgurl,user_status as status';
        $this->listUser($map,$field);
        $this->assign('UserStatus',C('UserStatus'));
        $this->display('user');
    }

    public function person(){
        $status = I('get.status',-1,'number_int');
        if($status!='-1'){
            $map['person_status'] = $status;
        }
        $this->assign('status',$status);
        $map['company_status'] = 0;
        $field = 'uid,nickname,headimgurl,user_status as status';
        $this->listUser($map,$field);
        $this->assign('UserStatus',C('UserStatus'));
        $this->assign('PersonStatus',C('PersonStatus'));
        $this->display('person');
    }

    public function company(){
        $status = I('get.status',-1,'number_int');
        if($status!='-1'){
            $map['company_status'] = $status;
        }
        $this->assign('status',$status);
        $map['person_status'] = 0;
        $field = 'uid,nickname,headimgurl,user_status as status';
        $this->listUser($map,$field);
        $this->assign('UserStatus',C('UserStatus'));
        $this->assign('CompanyStatus',C('CompanyStatus'));
        $this->display('company');
    }

    public function listUser($map,$field){
        $this->getData(M('user'),$map,'uid desc',$field);
    }


}