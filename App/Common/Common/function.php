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