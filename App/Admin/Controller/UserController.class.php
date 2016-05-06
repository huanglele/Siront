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
        $userStatusColor =array(
            '1' => 'bg-yellow',
            '2' => 'bg-green',
            '3' => 'bg-navy',
        );
        $this->assign('UserStatusColor',$userStatusColor);
    }

    public function index(){
        $this->user();
    }

    /**
     * 处理搜索
     */
    private function searchToMap(){
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
        $field = 'uid,nickname,phone,headimgurl as img,user_status as status,person_status as statusP,company_status as statusCp,create_time as time';
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
        $field = 'uid,nickname,phone,headimgurl as img,user_status as status,create_time as time';
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
        $field = 'uid,nickname,phone,headimgurl as img,user_status as status,create_time as time';
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

        $this->assign('UserStatus',C('UserStatus'));
        $this->assign('PersonStatus',C('PersonStatus'));
        $this->assign('CompanyStatus',C('CompanyStatus'));

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

    /**
     * 添加一个企业用户
     */
    public function addCompany(){
        if(isset($_POST['submit'])){
            var_dump($_POST);
        }else{
            $catMenu = S('CatMap');
            $this->assign('catMenuF',$catMenu['0']);
            unset($catMenu['0']);
            $this->assign('catMenuS',$catMenu);
            $this->assign('includeFiles','User/chooseMapModal');
            $this->display('addCompany');
        }
    }

    /**
     * 更新用户的状态
     */
    public function updateUserStatus(){
        if(isset($_POST['submit'])){
            $uid = I('post.uid');
            $user_status = I('post.user_status',null,'number_int');
            $person_status = I('post.person_status',null,'number_int');
            $company_status = I('post.company_status',null,'number_int');
            $map['uid'] = $uid;
            $data = array();
            if($user_status){
                $data['user_status'] = $user_status;
            }
            if($person_status){
                $data['person_status'] = $person_status;
            }
            if($company_status){
                $data['company_status'] = $company_status;
            }
            if(count($data)){
                M('user')->where($map)->save($data);
            }
            if($person_status){
                M('person_info')->where($map)->save('status',$person_status);
            }
            if($company_status){
                M('company_info')->where($map)->setField('status',$company_status);
            }
            $this->success('更新成功');
        }else{
            $this->error('页面不存在');
        }
    }

    /**
     * 开通一个个体户
     */
    public function openPerson(){
        $uid = I('get.uid');
        $info = $this->getInfo($uid);

    }
}