<?php
namespace Mobile\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        $uid = session('uid');
        if($uid){
            $this->assign('isLogin',1);
        }else{
            $this->assign('isLogin',0);
        }

        $this->assign('CatMap',getCat());
        $this->display();
    }

    public function cat(){
        $Cat = S('CatMap');
        var_dump($Cat);
    }



    /**
     * 用户注册
     */
    public function reg(){
        $this->display('reg');
    }


    /**
     *普通用户登录
     */
    public function login(){
        $this->display('login');
    }



    /**
     * 找回密码
     */
    public function findPwd(){
        $this->display('findPwd');
    }

}