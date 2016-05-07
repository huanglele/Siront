<?php
namespace Mobile\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){

    }

    /**
     * 用户注册
     */
    public function reg(){
        $this->display('reg');
    }

    /**
     * ajax提交注册
     */
    public function ajaxReg(){
        if(IS_AJAX && IS_POST){
            $telCode = I('post.telCode');
            $res['status'] = 'error';
            if(session('telCode')==$telCode){
                $data['phone'] = I('post.phone');
                $data['password'] = md5(I('post.password'));
                $data['money'] = $data['coin'] = $data['person_status'] = $data['company_status'] = 0;
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
     *普通用户登录
     */
    public function login(){
        $this->display('login');
    }

    public function ajaxLogin(){

    }

    public function sendSms(){
        echo 'success';//返回成功
    }
}