<?php
namespace App\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function _initialize(){
        header('Content-Type:text/html; charset=utf-8;');
        header("Cache-Control: no-cache, must-revalidate");
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
        $today['children'] = $this->createTime(date('H'),date('s'));
        $data[] = $today;
        $nextday['text'] = '明天';
        $nextday['value'] = date('Y-m-d',time()+24*3600);
        $nextday['children'] = $this->createTime(0,0);
        $data[] = $nextday;
        return $data;
    }

    public function test(){
        $data = $this->time();
        var_dump($data);
//        echo date('H');
    }

    public function createTime($house,$second){
        $second = intval($second/5)*5;
        $day = array();
        $h['text'] = $h['value'] = $house.' ';
        for($j=$second;$j<61;$j+=5){
            $m['text'] = $m['value'] = $j.' ';
            $h['children'][] = $m;
        }
        $day[] = $h;
        $house++;
        for($house;$house<24;$house++){
            $h['text'] = $h['value'] = $house.' ';
            for($j=0;$j<61;$j+=5){
                $m['text'] = $m['text'] = $j.' ';
                $h['children'][] = $m;
            }
            $day[] = $h;
        }
        return $day;
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