<?php
/**
 * Created by PhpStorm.
 * author: huanglele
 * Date: 2016/5/21
 * Time: 14:42
 * Description: 普通用户的个人中心
 */

namespace Mobile\Controller;


use Think\Controller;

class UserController extends Controller
{
    /**
     * 判断是否登录了
     */
    public function _initialize(){
        $uid = session('uid');
        if(!$uid){
            $this->redirect('index/login');
        }
    }

    public function index(){

    }

    public function myTask(){
        $map = array();
        $map['from_uid'] = session('uid');
        $map['status'] = array('in',array(1,2));
        $this->assign('leftTaskNum',M('task')->where($map)->where($map)->count('tid'));
        $this->display('myTask');
    }

    /**
     * ajax 拉取自己的任务列表
     */
    public function getTaskList(){
        $filter = I('filter');
        $p = I('p',1,'number_int');
        $M = M('task');
//        $map['from_uid'] = session('uid');
        switch($filter){
            case 'undo':$map['status'] = array('in',array(1,2));break;
            case 'done':$map['status'] = array('in',array(3,4));break;
            case 'all':$map['status'] = array('in',array(1,2,3,4));break;
            default: $map['status'] = array('in',array(1,2,3,4));
        }
        $count = M('task')->where($map)->where($map)->count();
        $Page = new \Think\Page($count,10);
        $list = M('task')->where($map)->limit($Page->firstRow,$Page->listRows)->field('tid,title,status,create_time as ctime')->select();
        $num = count($list);
        if($num){
            $TaskStatus = C('TaskStatus');
            for($i=0;$i<$num;$i++){
                $list[$i]['ctime'] = taskTime($list[$i]['ctime']);  //发布时间
                $list[$i]['status'] = $TaskStatus[$list[$i]['status']]; //当前状态
            }
        }
        $ret['status'] = 'success';
        $ret['num'] = $num;
        $ret['list'] = $list;
        if($p==10)  $p++;
        $ret['page'] = $p;
        $this->ajaxReturn($ret);
    }

    public function taskDetail(){
        $id = I('id');
        $uid = session('uid');
        $info = M('task')->find($id);
        if($uid==$info['from_uid']){
            $this->assign('info',$info);
            $this->display('taskDetail');
        }else{
            $this->error('任务不存在');
        }
    }

    /**
     * 设置中心
     */
    public function setting(){

    }

}