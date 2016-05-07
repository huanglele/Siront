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
            '0' => '',
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
     *普通用户
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
     * 查看一个个体户
     */
    public function personInfo(){
        $info = $this->getInfo();
    }

    /**
     * 获取用户基本信息
     */
    private function getInfo($uid = false){
        if(false==$uid){
            $uid = I('get.uid',0,'number_int');
        }
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
            if(!is_null($user_status)){
                $data['user_status'] = $user_status;
            }
            if(!is_null($person_status)){
                $data['person_status'] = $person_status;
                $this->updatePersonStatus($uid,$person_status);
            }
            if(!is_null($company_status)){
                $data['company_status'] = $company_status;
                $this->updateCompanyStatus($uid,$company_status);
            }
            if(count($data)){
                M('user')->where($map)->save($data);
            }
            $this->success('更新成功');
        }else{
            $this->error('页面不存在');
        }
    }

    /**
     * 更新个体户的状态
     * 如果之前不是个体户则新增一个个体户
     */
    private function updatePersonStatus($uid,$s){
        $map['uid'] = $uid;
        $Per = M('person_info');
        $personInfo = $Per->find($uid);
        //判断用户状态
        if($s==0 && is_null($personInfo)){  //更新为非个体户且不来就不存在个体户
            //什么都不做就好
        }elseif($s==0 && !is_null($personInfo)){ //更新为非个体户，但是之前是个体户
            $Per->where($map)->setField('status',$s);
        }elseif($s>0 && is_null($personInfo)){  //第一次创建个体户
            $data['uid'] = $uid;
            $data['lon'] = $data['lat'] = $data['score'] = 0;
            $data['cid'] = $data['phone'] = $data['sfzurl'] = '';
            $data['status'] = $s;
            $Per->save($data);
        }elseif($s>0 && !is_null($personInfo)){ //已经存在个体户，现在更新状态
            $Per->where($map)->setField('status',$s);
        }
    }

    /**
     * 更新一个企业用户的信息
     * 如果没有则创建一个企业用户
     */
    private function updateCompanyStatus($uid,$s){
        $map['uid'] = $uid;
        $Company = M('company_info');
        $companyInfo = $Company->find($uid);
        //判断用户状态
        if($s==0 && is_null($companyInfo)){  //更新为非企业用户且不来就不存在企业用户
            //什么都不做就好
        }elseif($s==0 && !is_null($companyInfo)){ //更新为非企业用户，但是之前是企业用户
            $Company->where($map)->setField('status',$s);
        }elseif($s>0 && is_null($companyInfo)){  //第一次创建企业用户
            $data['uid'] = $uid;
            $data['lon'] = $data['lat'] = $data['score'] = 0;
            $data['cid'] = $data['phone'] = $data['sfzurl'] = '';
            $data['status'] = $s;
            $Company->save($data);
        }elseif($s>0 && !is_null($companyInfo)){ //已经存在企业用户，现在更新状态
            $Company->where($map)->setField('status',$s);
        }
    }


}