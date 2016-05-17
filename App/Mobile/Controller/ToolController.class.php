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

}