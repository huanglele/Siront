<?php
/**
 * Author: huanglele
 * Date: 2016/5/3
 * Time: 下午 02:50
 * Description:
 */

/**
 * 返回一个图片的地址，不传入返回一个默认图片
 * @param bool|false $url 图片地址
 * @return bool|string 头像地址
 */
function headImgUrl($url=false){
    if(!$url){//随机返回一个默认头像
        return __ROOT__.'/Public/img/avatar.png';
    }elseif(preg_match('^http',$url)){
        return $url;
    }else{
        return $url;
    }
}

/**
 * 将时间戳转化成date格式时间
 * @param int $timestamp 时间戳
 * @return bool|string date时间
 */
function myDate($timestamp){
    if(is_numeric($timestamp) && $timestamp>0){
        return date('Y-m-d H:i:s',$timestamp);
    }else{
        return '';
    }
}

function taskTime($timestamp){
    $todayStamp = mktime('0','0','0',date('m'),date('d'),date('Y'));
    if($timestamp>$todayStamp){
        $day = '今天';
    }else{
        $leftDay = intval($todayStamp-$timestamp);
        if($leftDay<4){
            $day = $leftDay.'天前';
        }else{
            $day = date('m-d',$timestamp);
        }
    }
    return $day.' '.date('H:s',$timestamp);
}

function createTime($house,$second){
    $second = intval($second/5)*5;
    $day = array();
    $h['text'] = $house.' ';
    $h['value'] = $house;
    for($j=$second;$j<60;$j+=5){
        $m['text'] = $j.' ';
        $m['value'] = $j;
        $h['children'][] = $m;
    }
    $day[] = $h;
    $house++;
    for($house;$house<24;$house++){
        $h['text'] = $house.' ';
        $h['value'] = $house;
        for($j=0;$j<60;$j+=5){
            $m['text'] = $j.' ';
            $m['value'] = $j;
            $h['children'][] = $m;
        }
        $day[] = $h;
    }
    return $day;
}

/**
 * 发送短信
 * @param int $tel 接受者电话
 * @param string $temp 短信模板id
 * @param string $text 发送的内容
 * @return array 发送结果
 */
function sendSms($tel,$temp,$text){
    header('content-type:text/html;charset=utf-8');
    $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
    $smsConf = array(
        'key' => '*****************', //您申请的APPKEY
        'mobile'    => $tel, //接受短信的用户手机号码
        'tpl_id'    => $temp, //您申请的短信模板ID，根据实际情况修改
        'tpl_value' =>$text //您设置的模板变量，根据实际情况修改
    );
    $content = myCurl($sendUrl,$smsConf,1); //请求发送短信
    $res['status'] = 0;
    if($content){
        $result = json_decode($content,true);
        $error_code = $result['error_code'];
        if($error_code == 0){
            //状态为0，说明短信发送成功
            $res['msg'] = "短信发送成功";
            $res['status'] = 1;
        }else{
            //状态非0，说明失败
            $res['msg'] = $result['reason'];
        }
    }else{
        //返回内容异常，以下可根据业务逻辑自行修改
        $res['msg'] ="请求发送短信失败";
    }
    return $res;
}


/**
 *
 * @param  string $url [请求的URL地址]
 * @param  string $params [请求的参数]
 * @param  int $ipost [是否采用POST形式]
 * @return  string
 */
function myCurl($url,$params=false,$ispost=0){
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
    curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
    if( $ispost ) {
        curl_setopt( $ch , CURLOPT_POST , true );
        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
        curl_setopt( $ch , CURLOPT_URL , $url );
    }else {
        if($params){
            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
        }else{
            curl_setopt( $ch , CURLOPT_URL , $url);
        }
    }
    $response = curl_exec( $ch );
    if ($response === FALSE) {
        return false;
    }
    curl_close( $ch );
    return $response;
}

function hidePhoneNum($s){
    return substr($s,0,3).'****'.substr($s,6,4);
}

/**
 * 缓存【更新】类别数据
 * @param bool|false $cache
 * @return array|mixed
 */
function getCat($cache = false){
    $r = S('CatMap');
    if($cache || !$r){
        $list = M('category')->select();
        if(!$list) $list = array();
        $menu = array();
        $name = array();
        foreach($list as $v){
            $menu[$v['cid']][$v['id']] = $v;
            $name[$v['id']] = $v['name'];
        }
        //防止一级目录下没有二级子目录为空
        foreach($menu['0'] as $v){
            if(!array_key_exists($v['id'],$menu)){
                $menu[$v['id']] = array();
            }
        }
        S('CatMap',$menu);
        S('CatName',$name);
        $r = $menu;
    }
    return $r;
}

/**
 * 验证是不是电话号码包括手机号和座机。
 * @param s
 */
function isTel($s){
    $patrn = '/^(0\d{2,3})?\d{7,8}$/';
    if (preg_match($patrn,$s) || isMobil($s)) return true;
    return false;
}

/**
 * 验证是不是手机号
 * @param s
 * @returns {boolean}
 */
function isMobil($s) {
    $patrn = '/^(13[0-9]|14[5|7]|15[0-9]|17[0-9])\d{8}$/';
    if (!preg_match($patrn,$s)) return false;
    return true;
}


function jpushByDeviceIds($ids,$options){
    $url = 'https://api.jpush.cn/v3/push';

    $notification['android'] = array('alert'=>'我是alert','title'=>'我是title','builder_id'=>3,'extras'=>array('device',$ids[0]));
    $notification['ios'] = array('alert'=>'我是alert','title'=>'我是title','extras'=>array('device',$ids[0]));

    $data['platform'] = 'all';
    $data['audience'] = json_encode(array("registration_id"=>$ids));
    $data['notification'] = json_encode($notification);
    $data['options'] = json_encode($options);

}
