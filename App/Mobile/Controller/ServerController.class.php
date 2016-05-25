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
    private $InfoM = null;      //服务商的详细信息数据库对象

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
            if($type=='c'){
                $this->InfoM = M('company_info');
            }else{
                $this->InfoM = M('person_info');
            }
        }else{
            $this->login();die;
        }
    }

    public function index(){
//        var_dump(session());
        $uid = session('uid');
        $info = M('user')->find($uid);
        $M = $this->InfoM;
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

    /**
     * 许愿池
     * 匹配标签
     * 距离排序
     */
    public function listHelp(){
        //获取服务商的标签
        $type = session('type');
        $uid = session('uid');
        $M = $this->InfoM;
        $userInfo = $M->find($uid);
        if($userInfo['cid']){
            $cidArr = json_decode($userInfo['cid'], true);
        }else{
            $cidArr = array();
        }
        $lon = $userInfo['lon'];
        $lat = $userInfo['lat'];

        $map['status'] = 1;
//        $map['cid'] = array('in',$cidArr);
        $filed = "(POW(`lon`-".$lon.",2)+POW(`lat`-".$lat.",2)) as dis,tid,lon,lat,desc,title,tid,create_time as ctime,operate_time as otime";
        $list = M('task')->where($map)->order('dis asc')->field($filed)->select();
        $origins = '';
        foreach($list as $v){
            $origins .= $v['lon'].','.$v['lat'].'|';
        }
        $origins = rtrim($origins,'|');
        $destination = $lon.','.$lat;
        $key = '4d6777df67a2c81ec8ec6a8480821a73';
        $url = 'http://restapi.amap.com/v3/distance?origins='.$origins.'&destination='.$destination.'&output=json&key='.$key;
        $res = myCurl($url);
        $res = json_decode($res,true);
        $data = array();
        if($res['info']=='OK'){
            foreach($res['results'] as $k=>$v){
                $t = $list[$k];
                $t['distance'] = $v['distance'];
                $t['time'] = $v['duration'];
                $data[] = $t;
            }
        }
        $this->assign('data',$data);
        $this->assign('mePosition',array('lon'=>$lon,'lat'=>$lat));
        $this->display('listHelp');
    }

    /**
     * ajax接单
     */
    public function sureTask(){
        $tid = I('post.tid');
        $M = M('task');
        $tStatsu = $M->where(array('tid'=>$tid))->getField('status');
        if($tStatsu==1){
            $data['tid'] = $tid;
            $data['status'] = 2;
            $data['work_uid'] = session('uid');
            $data['sure_time'] = time();
            if($M->save($data)){
                $this->success('接单成功',U('server/taskDetail',array('tid'=>$tid)));
            }else{
                $this->error('操作失败，请重试');
            }
        }else{
            $this->error('任务不存在');
        }
    }

    /**
     * 接单详情
     */
    public function taskDetail(){
        $tid = I('get.tid');
        $M = M('task');
        $tInfo = $M->find($tid);
        if($tInfo && $tInfo['work_uid']==session('uid')){
            $this->assign('info',$tInfo);
            $this->display('taskDetail');
        }else{
            $this->error('任务不存在');
        }
    }


    /**
     * 任务列表
     */
    public function myTask(){
        $map = array();
        $map['work_uid'] = session('uid');
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
        $map['work_uid'] = session('uid');
        switch($filter){
            case 'undo':$map['status'] = array('in',array(1,2));break;
            case 'done':$map['status'] = array('in',array(3,4));break;
            case 'all':$map['status'] = array('in',array(1,2,3,4));break;
            default: $map['status'] = array('in',array(1,2,3,4));
        }
        $count = $M->where($map)->where($map)->count();
        $Page = new \Think\Page($count,10);
        $list = $M->where($map)->limit($Page->firstRow,$Page->listRows)->field('tid,title,status,create_time as ctime')->select();
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

    /**
     * 更新自己的位置设备信息
     */
    public function updateInfo(){
        $lon = I('lon');
        $lat = I('lat');
        $deviceId = I('deviceId');
        $time = I('time');
        $uid = session('uid');
        $M = $this->InfoM;
        $data['uid'] = $uid;
        if($lon) $data['lon'] = $lon;
        if($lat) $data['lat'] = $lat;
        if($deviceId) $data['jPushDeviceId'] = $deviceId;
        if($time) $data['time'] = $time;
        $M->save($data);
        echo $M->getLastSql();
    }


}