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
                $data['phone'] = I('post.no');
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

    /**
     * 发布任务
     */
    public function addTask(){
        $ret['status'] = 'error';
        $uid = session('uid');
        if(!$uid){
            $ret['msg'] = '请重新登录';
        }else{
            $r = $this->checkTaskData();
            if($r['status']){
                $data = $r['data'];
                $data['from_uid'] = $uid;
                $data['create_time'] = time();
                $data['status'] = 1;
                if(M('task')->add($data)){
                    $ret['status'] = 'success';
                }else{
                    $ret['msg'] = '服务器错误';
                }
            }else{
                $ret['msg'] = $r['msg'];
            }
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 服务商登录
     */
    public function loginServer(){
        if(IS_POST){
            $ret['status'] = 'error';
            $phone = I('post.phone',0,'number_int');
            $map['phone'] = $phone;
            $map['person_status|company_status'] = array('neq',0);
            $info = M('user')->field('uid,password,person_status as pStatus,company_status as cStatus')->where($map)->find();
            if($info){
                if(md5(I('post.pwd'))==$info['password']){
                    //先判断是否是有正常的状态
                    if($info['pstatus']==2 || $info['cstatus']==2){
                        $type = ($info['cstatus']==2)?'c':'p';
                        session('uid',$info['uid']);
                        session('type',$type);
                        $ret['status'] = 'success';
                    }else{
                        $UserStatus = array('0'=>'用户不存在','1' => '账户待认证', '3' => '账户被限制',);
                        $s = $info['cstatus']?$info['cstatus']:$info['pstatus'];
                        $ret['msg'] = $UserStatus[$s];
                    }
                }else{
                    $ret['msg'] = '密码错误';
                }
            }else{
                $ret['msg'] = '用户不存在';
            }
            $this->ajaxReturn($ret);
        }
    }

    /**
     * 获取用户的基本信息
     */
    public function getUserInfo(){
        $uid = session('uid');
        if($uid){
            $info = M('user')->field('uid,nickname,phone,headimgurl as img,user_status as ustatus,person_status as pstatus,company_status as cstatus')->find($uid);
            $info['img'] = headImgUrl($info['img']);
            $ret['status'] = 'success';
            $ret['info'] = $info;
        }else{
            $ret['status'] = 'error';
            $ret['msg'] = '请先登录';
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 检测发布任务的数据是否正确
     */
    private function checkTaskData(){
        $ret['status'] = false;
        //发布时间
        $operate_time = strtotime(I('post.time'));
        if($operate_time){
            $data['operate_time'] = $operate_time;
            //位置信息
            $lat = I('post.lat',0,'float');
            $lon = I('post.lon',0,'float');
            if($lat && $lon){
                $data['lat'] = $lat;
                $data['lon'] = $lon;
                $data['cityCode'] = I('post.cityCode');
                $data['address'] = I('post.place');

                //发布类型
                $type = I('post.type',0,'number_int');
                if($type){
                    $data['cid'] = $type;

                    //联系方式
                    $tel = I('post.tel');
                    if(isTel($tel)){
                        $data['tel'] = $tel;
                        //简单描述
                        $desc = I('post.desc');
                        if($desc){
                            $data['desc'] = $desc;
                            $data['title'] = I('post.title')?I('post.title'):substr($desc,0,10);
                            $ret['status'] = true;
                            $ret['data'] = $data;
                        }else{
                            $ret['msg'] = '请简单描述问题';
                        }
                    }else{
                        $ret['msg'] = '联系方式错误';
                    }
                }else{
                    $ret['msg'] = '帮助类型错误';
                }
            }else{
                $ret['msg'] = '位置错误';
            }
        }else{
            $ret['msg'] = '发布时间错误';
        }
        return $ret;
    }
}