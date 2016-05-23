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
        $this->uid = I('uid');
        $this->deviceId = I('deviceId');
        $token = I('token');
        if($this->uid && $token){
            $info = S($token);
            if($info['uid']==$this->uid && $info['deviceId']==$this->deviceId){
                $this->isLogin = true;
            }else{
                //登录失效
                S($token,null);
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
                    $res['msg'] = '登录成功';
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
                    $token = $data['token'] = session_id();
                    $data['deviceId'] = $this->deviceId;
                    S($token,$data);
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
        $this->ajaxReturn($res);
    }

    /**
     * 发布任务
     */
    public function addTask(){
        $ret['status'] = 'error';
        $uid = I('uid');
        if(!$this->isLogin){
            $ret['msg'] = '登录失效';
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
     * 检测发布任务的数据是否正确
     */
    private function checkTaskData(){
        $ret['status'] = false;
        //发布时间
        $operate_time = strtotime(I('time'));
        if($operate_time){
            $data['operate_time'] = $operate_time;
            //位置信息
            $lat = I('lat',0,'float');
            $lon = I('lon',0,'float');
            if($lat && $lon){
                $data['lat'] = $lat;
                $data['lon'] = $lon;
                $data['cityCode'] = I('cityCode');
                $data['address'] = I('place');

                //发布类型
                $type = I('type',0,'number_int');
                if($type){
                    $data['cid'] = $type;

                    //联系方式
                    $tel = I('tel');
                    if(isTel($tel)){
                        $data['tel'] = $tel;
                        //简单描述
                        $desc = I('desc');
                        if($desc){
                            $data['desc'] = $desc;
                            $data['title'] = I('title');

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

    /**
     * 商家app测试单个推送
     */
    public function serverinit(){
        $deviceId = I('jpushDeviceId');
        $client = new \Org\Jpush\JPush(C('JPush.key'),C('JPush.secret'));
        $result = $client->push()
            ->setPlatform(array('ios', 'android'))
            ->addRegistrationId($deviceId)
            ->setNotificationAlert('Hi, JPush')
            ->addAndroidNotification('Hi, android notification', 'notification title', 1, array("deviceId"=>$deviceId))
            ->addIosNotification("Hi, iOS notification", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array("key1"=>"value1", "key2"=>"value2"))
            ->setMessage("msg content", 'msg title', 'type', array("key1"=>"value1", "key2"=>"value2"))
            ->setOptions(100000, 3600, null, false)
            ->send();
        echo 'Result=' . json_encode($result);
    }

}