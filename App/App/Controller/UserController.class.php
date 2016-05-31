<?php
/**
 * Created by PhpStorm.
 * author: huanglele
 * Date: 2016/5/31
 * Time: 14:00
 * Description:
 */

namespace App\Controller;
use Think\Controller;

class UserController extends Controller
{
    /**
     * 获取用户的基本信息
     * status(success,error),uid,nickname,headImgUrl,phone,phoneHide,type(u,c,p),
     */
    public function getUserInfo(){
        $uid = session('uid');
        $deviceId = I('deviceId');
        if($uid){
            S($uid.'deviceId',$deviceId);
            $info = M('user')->field('uid,nickname,phone,headimgurl,user_status as ustatus,person_status as pstatus,company_status as cstatus')->find($uid);
            $ret['status'] = 'success';
            $ret['uid'] = $uid;
            $ret['nickname'] = $info['nickname'];
            $ret['phone'] = $info['phone'];
            $ret['phoneHide'] = hidePhoneNum($info['phone']);
            $ret['headImgUrl'] = headImgUrl($info['headimgurl']);
            if($info['pstatus']==2){
                $ret['type'] = 'p';
            }elseif($info['cstatus']==2){
                $ret['type'] = 'c';
            }else{
                $ret['type'] = 'u';
            }
        }else{
            $ret['status'] = 'error';
            $ret['msg'] = '请先登录';
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 登录
     */
    public function login(){
        if(IS_POST){
            $phone = I('post.phone');
            $pwd = I('post.password');
            $deviceId = I('jpushDeviceId');
            $res['status'] = 'error';
            $info = M('user')->where(array('phone'=>$phone))->field('uid,password,nickname,phone,headimgurl as img,user_status as ustatus,person_status as pstatus,company_status as cstatus')->find();
            if($info){
                if($info['password']==md5($pwd)){
                    if($info['status']!=3){
                        $ret['status'] = 'success';
                        $ret['uid'] = $info['uid'];
                        $ret['nickname'] = $info['nickname'];
                        $ret['phone'] = $info['phone'];
                        $ret['phoneHide'] = hidePhoneNum($info['phone']);
                        $ret['headImgUrl'] = headImgUrl($info['headimgurl']);
                        if($info['pstatus']==2){
                            $ret['type'] = 'p';
                        }elseif($info['cstatus']==2){
                            $ret['type'] = 'c';
                        }else{
                            $ret['type'] = 'u';
                        }
                    }else{
                        $ret['msg'] = '账户被限制';
                    }
                }else{
                    $ret['msg'] = '密码不正确';
                }
            }else{
                $ret['msg'] = '用户不存在';
            }
            $this->ajaxReturn($ret);
        }
    }

}