<?php
/**
 * Author: huanglele
 * Date: 2016/4/29
 * Time: 下午 02:51
 * Description:
 */

/**
 * 记录后台用户操作记录
 * @param $ac 事件详情
 */
function record($ac){
    $data = array();
    $data['aid'] = session('aid');
    $data['time'] = time();
    $data['action'] = $ac;
    $data['ip'] = get_client_ip();
    M('admin_action')->add($data);
}