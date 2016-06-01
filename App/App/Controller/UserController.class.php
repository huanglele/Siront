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

    /**
     * 提交注册
     */
    public function reg(){
        if(IS_POST){
            $telCode = I('post.telCode');
            $res['status'] = 'error';
            if(session('telCode')==$telCode){
                $data['phone'] = I('post.phone');
                $data['password'] = md5(I('post.password'));
                $data['money'] = $data['coin'] = $data['person_status'] = $data['company_status'] = 0;
                $data['user_status'] =2;        //注册通过验证
                $data['create_time'] = time();
                $data['headimgurl'] =  '';
                $data['nickname'] = I('post.nickname');
                $M = D('user');
                if($M->create($data)){
                    $r = $M->add($data);
                    if($r){
                        session('phone',$data['phone']);
                        session('uid',$r);
                        $data['status'] = 'success';
                        $res['msg'] = '注册成功';
                    }else{
                        $res['msg'] = '注册失败请重试';
                    }
                }else{
                    $res['msg'] = $M->getError();
                }
            }else{
                $res['msg'] = '短信验证码错误';
            }
            $this->ajaxReturn($res);
        }
    }

    /**
     * 退出登录
     */
    public function logout(){
        session('uid',null);
        $this->success('安全退出');
    }

}