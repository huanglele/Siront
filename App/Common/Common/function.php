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
 * @param $timestamp 时间戳
 * @return bool|string date时间
 */
function myDate($timestamp){
    if(is_numeric($timestamp) && $timestamp>0){
        return date('Y-m-d H:i:s',$timestamp);
    }else{
        return '';
    }
}