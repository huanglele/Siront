<?php
/**
 * Created by PhpStorm.
 * author: huanglele
 * Date: 2016/5/18
 * Time: 9:47
 * Description:
 */

namespace Mobile\Controller;


use Think\Controller;

class ApiController extends Controller
{
    private $isLogin = false;
    private $deviceId = '';
    private $uid = '';

    public function _initialize(){
        C('SHOW_PACE_TRACE',false);
        $this->uid = I('post.uid');
        $this->deviceId = I('post.deviceId');
        $token = I('post.token');
        if($this->uid && $this->token){
            $Mem = new \Memcache();
            $info = $Mem->get($this->uid);
            if($info){
                if($info['token']==$token && $info['deviceId']==$this->deviceId){

                }else{
                    //登录失效
                    $Mem->set($this->uid,null);
                }
            }
        }
    }

    public function cache(){
        $k = I('get.k');
        var_dump(S($k));
    }

    /**
     * 登录
     */
    public function login(){
        $phone = I('phone');
        $pwd = I('password');
        $res['status'] = 'error';
        $info = M('user')->where(array('phone'=>$phone))->field('uid,nickname,password,user_status as status')->find();
        S('post',$_POST);
        if($info){
            if($info['password']==md5($pwd)){
                if($info['status']!=3){
                    $data['msg'] = '登录成功';
                    if($info['nickname']){
                        $res['user'] = $info['nickname'];
                    }else{
                        $res['user'] = hidePhoneNum($phone);
                    }
                    $data['nickname'] = $info['nickname'];
                    $data['phone'] = $phone;
                    $data['uid'] = $info['uid'];
                    $data['deviceId'] = $this->deviceId;
                    $this->uid = $info['uid'];
                    $data['token'] = session('id');
                    $Mem = new \Memcache();
                    $Mem->set($info['uid'],$data);
                    $res['status'] = 'success';
                    $res['data'] = $data;
                }else{
                    $res['msg'] = '账户被限制';
                }
            }else{
                $res['msg'] = '密码不正确';
            }
        }else{
            $res['msg'] = '用户不存在';
        }
        $res['phone'] = I('phone');
        $this->ajaxReturn($res);
    }

}