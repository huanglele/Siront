<?php
/**
 * Author: huanglele
 * Date: 2016/5/3
 * Time: 下午 02:50
 * Description:
 */

/**
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