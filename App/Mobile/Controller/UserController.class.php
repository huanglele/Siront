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

    /**
     * 任务列表
     */
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
        $map['from_uid'] = session('uid');
        switch($filter){
            case 'undo':$map['status'] = array('in',array(1,2));break;
            case 'done':$map['status'] = array('in',array(3,4));break;
            case 'all':$map['status'] = array('in',array(1,2,3,4));break;
            default: $map['status'] = array('in',array(1,2,3,4));
        }
        $count = $M->where($map)->where($map)->count();
        $Page = new \Think\Page($count,10);
        $list = $M->where($map)->order('tid desc')->limit($Page->firstRow,$Page->listRows)->field('tid,title,status,create_time as ctime')->select();
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
        if($num==10)  $p++;
        $ret['page'] = $p;
        $this->ajaxReturn($ret);
    }

    /**
     * 任务详情
     */
    public function taskDetail(){
        $id = I('id');
        $uid = session('uid');
        $info = M('task')->find($id);
        if($uid==$info['from_uid']){
            $this->assign('info',$info);
            $this->assign('CatName',S('CatName'));
            $this->display('taskDetail');
        }else{
            $this->error('任务不存在');
        }
    }

    /**
     * 获取任务商家信息
     */
    public function getTaskWorker(){
        $tid = I('post.tid');
        $tInfo = M('task')->field('from_uid,work_uid,status,sure_time,done_time,money')->find($tid);
        $ret['status'] = 'ok';
        if($tInfo['status']>1 && $tInfo['status']<5 && $tInfo['from_uid']==session('uid')){
            //获取商家的信息
            $uInfo = M('user')->field('nickname,headimgurl,person_status as p_status,company_status as c_status,phone')->find($tInfo['work_uid']);
            if($uInfo['c_status']==2){//企业用户
                $wInfo = M('compay_info')->field('lon,lat,score,tel')->find($uInfo['uid']);
                $info['type'] = '企业用户';
            }elseif($uInfo['p_status']==2){
                $wInfo = M('company_info')->field('lon,lat,score,tel')->find($uInfo['uid']);
                $info['type'] = '个体户';
            }else{
                $ret['info']['status'] = 0;
                $this->ajaxReturn($ret);die;
            }
            $info['status'] = 1;
            $info['img'] = headImgUrl($uInfo['headimgurl']);
            $info['sure_time'] = taskTime($tInfo['sure_time']);
            $info['done_time'] = taskTime($tInfo['done_time']);
            $info['nickname'] = $uInfo['nickname'];
            $info['score'] = $wInfo['score'];
            $info['lon'] = $wInfo['lon'];
            $info['lat'] = $wInfo['lat'];
            $info['tel'] = $wInfo['tel'];
            $info['money'] = $tInfo['money'];
            $info['phone'] = $uInfo['phone'];
            $ret['info'] = $info;
        }else{
            $ret['info']['status'] = $tInfo['status'];
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 再次发送通知到用户
     */
    public function postTaskAgain(){
        $tid = I('tid');
        $M = M('task');
        $tInfo = $M->where(array('tid'=>$tid))->field('status,from_uid,del')->find();
        if($tInfo['del']==0 && $tInfo['from_uid']==session('uid')){
            if($tInfo['status']==1){
                $Tool = A('Tool');
                $num = $Tool->matchServer($tid);
                $this->success('已经通知了'.$num.'位附近商家');
            }else{
                $this->error('任务已匹配到商家');
            }
        }else{
            $this->error('任务不存在');
        }
    }

    /**
     * 删除某个任务
     */
    public function delTask(){
        $tid = I('post.tid');
        $M = M('task');
        $tInfo = $M->where(array('tid'=>$tid))->field('status,from_uid,del')->find();
        if($tInfo['del']==0 && $tInfo['from_uid']==session('uid')){
            if($M->where(array('tid'=>$tid))->setField('del',1)){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
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