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

    public function _initialize(){
        parent::_initialize();
        if(strtolower('index')==ACTION_NAME){
            $this->user();      //默认显示所有的普通用户
        }
    }

    /**
     * 处理搜索
     */
    private function searchToMap(){
        $userStatusColor =array(
            '1' => 'bg-yellow',
            '2' => 'bg-green',
            '3' => 'bg-navy',
        );
        $this->assign('UserStatusColor',$userStatusColor);

        $map = array();
        $type = I('get.searchType');
        $q = I('get.q');
        if($type=='phone'){//通过手机号搜人
            $q = is_numeric($q)?$q:false;
        }elseif($type=='nickname'){//通过昵称搜人
            $q = $q;
        }else{
            $type = 'phone';
            $q = '';
        }
        $this->assign('searchType',$type);
        $this->assign('q',$q);

        if($type && $q){
            $map[$type] = array('like','%'.$q.'%');
        }
        return $map;
    }

    /**
     * 显示所有用户
     */
    public function user(){
        $map = $this->searchToMap();
        $status = I('get.status',-1,'number_int');
        if($status!='-1'){
            $map['user_status'] = $status;
        }

        $this->assign('status',$status);
        $field = 'uid,nickname,phone,headimgurl as img,user_status as status,person_status as statusP,company_status as statusCp';
        $this->listUser($map,$field);
        $this->assign('UserStatus',C('UserStatus'));
        $this->assign('PersonStatus',C('PersonStatus'));
        $this->assign('CompanyStatus',C('CompanyStatus'));
        $this->display('user');
    }

    /**
     * 只显示个体户
     */
    public function person(){
        $map = $this->searchToMap();
        $status = I('get.status');
        $status = is_numeric($status)?$status:-1;
        if($status!='-1'){
            $map['person_status'] = $status;
        }else{
            $map['person_status'] = array('neq',0);
        }
        $this->assign('status',$status);
        $map['company_status'] = 0;
        $field = 'uid,nickname,phone,headimgurl as img,user_status as status';
        $this->listUser($map,$field);
        $this->assign('UserStatus',C('UserStatus'));
        $this->assign('PersonStatus',C('PersonStatus'));
        $this->display('person');
    }

    /**
     * 只显示企业用户
     */
    public function company(){
        $map = $this->searchToMap();
        $status = I('get.status');
        $status = is_numeric($status)?$status:-1;
        if($status!='-1'){
            $map['company_status'] = $status;
        }else{
            $map['company_status'] = array('neq',0);
        }
        $this->assign('status',$status);
        $map['person_status'] = 0;
        $field = 'uid,nickname,phone,headimgurl as img,user_status as status';
        $this->listUser($map,$field);
        $this->assign('UserStatus',C('UserStatus'));
        $this->assign('CompanyStatus',C('CompanyStatus'));
        $this->display('company');
    }

    private function listUser($map,$field){
        $this->getData(M('user'),$map,'uid desc',$field);
    }

    /**
     *
     */
    public function userInfo(){
        $info = $this->getInfo();

        $this->assign('info',$info);
        $this->display('userInfo');
    }

    /**
     * 获取用户基本信息
     */
    public function getInfo(){
        $uid = I('get.uid',0,'number_int');
        return M('user')->find($uid);
    }
}