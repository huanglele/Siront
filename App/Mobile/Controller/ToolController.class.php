<?php
/**
 * Created by PhpStorm.
 * author: huanglele
 * Date: 2016/5/17
 * Time: 13:24
 * Description: ajax 公共借口
 */

namespace Mobile\Controller;

use Think\Controller;

class ToolController extends Controller
{
    /**
     * 返回发布任务需要的时间格式
     */
    public function getSendTaskTime(){
        $data = array();
        $today['text'] = '今天';
        $today['value'] = date('Y-m-d');
        $today['children'] = createTime(date('H'),date('s'));
        $data[] = $today;
        $nextday['text'] = '明天';
        $nextday['value'] = date('Y-m-d',time()+24*3600);
        $nextday['children'] = createTime(0,0);
        $data[] = $nextday;
        $this->ajaxReturn($data);
    }

    /**
     * 获取任务分类
     */
    public function getSendTaskCat(){
        $CatMap = getCat();
        $html = '';
        foreach($CatMap[0] as $cat){
            if($cat['status']==1){
                $html .= '<div class="catItem"><div class="catFirstTitle"><span>'.$cat['name'].'</span><i class="iconfont icon-menuup"></i></div><ul class="catSecUl">';
                $h = '';
                foreach($CatMap[$cat['id']] as $v){
                    $h .= '<li data-cid="'.$v['id'].'" class="cat">'.$v['name'].'</li>';
                }
                $html .= $h.'</ul></div>';
            }
        }
        $ret['status'] = 'success';
        $ret['html'] = $html;
        $this->ajaxReturn($ret);
    }

    public function getMem(){
        $k = I('key');
        $M = new \Memcache();
        var_dump($M->get($k));
    }

    /**
     * 匹配一个符合任务的商家
     */
    public function matchServer($tid){
        $tInfo = M('task')->field('title,address,operate_time,status,cid,lon,lat')->find($tid);
        if(!$tInfo || $tInfo['status']!=1){
            return 0;   //任务不存在或者已经结束了
        }

        $lon = $tInfo['lon'];
        $lat = $tInfo['lat'];

        $map['status'] = 2;
//        $map['cid'] = array('like','%'.$tInfo['cid'].',%');
        $map['deviceid'] = array('neq','');
        $filed = "(POW(`lon`-".$lon.",2)+POW(`lat`-".$lat.",2)) as dis,uid,lon,lat,jPushDeviceId as deviceid";
        $list1 = M('company_info')->where($map)->order('dis asc')->field($filed)->select();
        $list2 = M('person_info')->where($map)->order('dis asc')->field($filed)->select();
        $list = array_merge($list1,$list2);
        $origins = '';

        $deviceId = array();
        $CatName = S('CatName');

        $extra['type'] = 'task';
        $extra['tid'] = $tid;
        $extra['url'] = U('server/sureTask');
        $extra['title'] = $tInfo['title'];
        $extra['address'] = $tInfo['address'];
        $extra['time'] = taskTime($tInfo['operate_time']);
        $extra['cid'] = $CatName[$tInfo['cid']];
        $title = '有一个新任务';
        $content = '点击查看';

        foreach($list as $v){
            if(''!=$v['deviceid']){
                $deviceId[] = $v['deviceid'];
                $origins .= $v['lon'].','.$v['lat'].'|';

            }
        }
        $this->sendAppNotify($deviceId, $title, $content, $extra);

        //如果匹配到了设备
        $num = count($deviceId);
      /*  if($num) {
            $origins = rtrim($origins, '|');
            $destination = $lon . ',' . $lat;
            $key = '4d6777df67a2c81ec8ec6a8480821a73';
            $url = 'http://restapi.amap.com/v3/distance?origins=' . $origins . '&destination=' . $destination . '&output=json&key=' . $key;
            $res = myCurl($url);
            $res = json_decode($res, true);
            $data = array();

            if ($res['info'] == 'OK') {
                foreach ($res['results'] as $k => $v) {
                    $t = $list[$k];
                    $t['distance'] = $v['distance'];
                    $t['time'] = $v['duration'];
                    $data[] = $t;
                }
            }
        }*/
        return $num;
    }

    /**
     * 发送一个广播
     */
    private function sendAppNotify($deviceId,$title,$content,$extra){
        include_once LIB_PATH.'Org/JPush/JPush.php';
//        $client = new \JPush(C('JPush.key'),C('JPush.secret'));
        $client = new \JPush(C('JPush.key'),C('JPush.secret'));
        $result = $client->push()
            ->setPlatform(array('ios', 'android'))
            ->addRegistrationId($deviceId)
            ->setNotificationAlert('Hi, JPush')
            ->addAndroidNotification($content, $title, 1,$extra)
            ->addIosNotification($content, 'iOS sound', \JPush::DISABLE_BADGE, true, 'iOS category',$extra)
//            ->setMessage($content, $title, 'type', $extra)
            ->setOptions(100000, 3600, null, false)
            ->send();
        return json_encode($result);
    }

    public function test(){
        $deviceId = array('1507bfd3f7c753da54a','140fe1da9eab45b0bee');
        $title = '推送测试';
        $content = 'content';
        $extra = array();
        $r = $this->sendAppNotify($deviceId,$title,$content,$extra);
        echo $r;
    }

}