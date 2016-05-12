<?php
/**
 * Author: huanglele
 * Date: 2016/5/9
 * Time: 上午 09:13
 * Description:
 */

namespace Mobile\Controller;
use Think\Controller;

class AjaxController extends Controller
{

    public function _initialize(){
        if(!IS_AJAX){
            $this->error('页面不存在');
        }
    }

    /**
     * 处理登录提交
     */
    public function ajaxLogin(){
        if(IS_POST){
            $phone = I('post.phone');
            $pwd = I('post.password');
            $res['status'] = 'error';
            $info = M('user')->where(array('phone'=>$phone))->field('uid,nickname,password,user_status as status')->find();
            if($info){
                if($info['password']==md5($pwd)){
                    if($info['status']!=3){
                        $data['msg'] = '登录成功';
                        if($info['nickname']){
                            $res['user'] = $info['nickname'];
                        }else{
                            $res['user'] = hidePhoneNum($phone);
                        }
                        session('phone',$phone);
                        session('uid',$info['uid']);
                        $res['status'] = 'success';
                    }else{
                        $res['msg'] = '账户被限制';
                    }
                }else{
                    $res['msg'] = '密码不正确';
                }
            }else{
                $res['msg'] = '用户不存在';
            }
            $this->ajaxReturn($res);
        }
    }

    /**
     * 发送验证码
     */
    public function sendSms(){
        echo 'success';//返回成功
    }

    /**
     * 提交注册
     */
    public function ajaxReg(){
        if(IS_POST){
            $telCode = I('post.telCode');
            $res['status'] = 'error';
            if(session('telCode')==$telCode){
                $data['phone'] = I('post.phone');
                $data['password'] = md5(I('post.password'));
                $data['money'] = $data['coin'] = $data['person_status'] = $data['company_status'] = 0;
                $data['user_status'] =2;        //注册通过验证
                $data['create_time'] = time();
                $data['headimgurl'] = $data[''] = '';
                $M = D('user');
                if($M->save($data)){
                    $data['status'] = 'success';
                    $data['msg'] = '登录成功';
                }else{
                    $data['msg'] = '登录失败';
                }
            }else{
                $res['msg'] = '短信验证码错误';
            }
            $this->ajaxReturn($res);
        }
    }

    /**
     * 找回密码
     */
    public function ajaxFindPwd(){
        if(IS_POST){
            $telCode = I('post.telCode');
            $res['status'] = 'error';
            if(session('telCode')==$telCode){
                $M = D('user');
                $phone = I('post.phone');
                $pwd = md5(I('post.password'));
                $info = $M->where(array('phone'=>$phone))->field('uid,password')->find();
                if($info){
                    if($info['password']==$pwd){
                        $res['status'] = 'success';
                        $res['msg'] = '修改成功';
                    }else if($M->where(array('uid'=>$info['uid']))->setField('password',$pwd)){
                        $res['status'] = 'success';
                        $res['msg'] = '修改成功';
                    }else{
                        $res['msg'] = '修改失败';
                    }
                }else{
                    $res['msg'] = '用户不存在';
                }
            }else{
                $res['msg'] = '短信验证码错误';
            }
            $this->ajaxReturn($res);
        }
    }

}