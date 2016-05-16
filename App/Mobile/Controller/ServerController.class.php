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

    /**
     * 许愿池
     * 匹配标签
     * 距离排序
     */
    public function listHelp(){
        //获取服务商的标签
        $type = session('type');
        $uid = session('uid');
        if($type=='p'){
            $M = M('person_info');
        }else{
            $M = M('company_info');
        }
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
        $filed = "(POW(`lon`-".$lon.",2)+POW(`lat`-".$lat.",2)) as dis,tid,lon,lat,title";
        $list = M('task')->where($map)->order('dis asc')->field($filed)->select();
//        var_dump($list);
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
//        var_dump($data);
        $this->assign('data',json_encode($data));
        $this->assign('mePosition',array('lon'=>$lon,'lat'=>$lat));
        $this->display('listHelp');
    }

}