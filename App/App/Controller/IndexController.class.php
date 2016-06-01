<?php
namespace App\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        $Mem = new \Memcache();
        var_dump($Mem);
    }

    /**
     * 获取可以发布任务的类别
     */
    public function getPostTaskCat(){
        $this->ajaxReturn(getCat());
    }

    /**
     * 获取可以发布任务的时间
     */
    public function getPostTaskTime(){
        $data = array();
        $today['text'] = '今天';
        $today['value'] = date('Y-m-d');
        $today['children'] = createTime(date('H'),date('s'));
        $data[] = $today;
        $nextday['text'] = '明天';
        $nextday['value'] = date('Y-m-d',time()+24*3600);
        $nextday['children'] = createTime(0,0);
        $data[] = $nextday;
        $this->ajaxReturn($data);
    }

}