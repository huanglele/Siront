<?php
namespace Mobile\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function _initialize(){
        header('Content-Type:text/html; charset=utf-8;');
        if($_SERVER['HTTP_HOST']=='xyc.91yiso.com'){
            C('SHOW_PAGE_TRACE',false);
        }
    }

    public function index(){
        $uid = session('uid');
        if($uid){
            $this->assign('isLogin',1);
        }else{
            $this->assign('isLogin',0);
        }
        $this->assign('timeJson',json_encode($this->time()));
        $this->assign('CatMap',getCat());
        $this->display();
    }

    public function cat(){
        $Cat = S('CatMap');
        var_dump($Cat);
    }

    public function time(){
        $data = array();
        $today['text'] = '今天';
        $today['value'] = date('Y-m-d');
        $today['children'] = createTime(date('H'),date('s'));
        $data[] = $today;
        $nextday['text'] = '明天';
        $nextday['value'] = date('Y-m-d',time()+24*3600);
        $nextday['children'] = createTime(0,0);
        $data[] = $nextday;
        return $data;
    }

    public function test(){
        $s = '546521146';
        var_dump(isTel($s));
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

    /**
     * 查看一个任务
     * 只有发布人者和接受任务的人能够看到
     */
    public function item(){
        $id = I('id');
        $uid = session('uid');
        if($uid){
            $info = M('task')->find($id);

        }else{  //没有登录
            $this->error('请先登录',U('index/login'));
        }
    }

}