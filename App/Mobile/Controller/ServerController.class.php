<?php
/**
 * Created by PhpStorm.
 * User: Sun
 * Date: 2016/5/14
 * Time: 10:59
 */

namespace Mobile\Controller;
use Think\Controller;

class ServerController extends Controller
{
    public function _initialize(){
        header('Content-Type:text/html; charset=utf-8;');
        if($_SERVER['HTTP_HOST']=='xyc.91yiso.com'){
            C('SHOW_PAGE_TRACE',false);
        }
        $noAcName = array('login');
        $acName = strtolower(ACTION_NAME);
        $uid = session('uid');
        $type = session('type');
        if(($type && $uid) || in_array($acName,$noAcName)){
            $this->assign('uid',$uid);
        }else{
            $this->login();die;
        }
    }

    public function index(){
//        var_dump(session());
        $uid = session('uid');
        $type = session('type');
        $info = M('user')->find($uid);
        if($type=='c'){
            $M = M('company_info');
        }else{
            $M = M('person_info');
        }
        $sInfo = $M->find($uid);
        $info = array_merge($info,$sInfo);
        $this->assign('info',$info);
        $this->display('index');
    }

    /**
     * 登录
     */
    public function login(){
        $this->display('login');
    }


}