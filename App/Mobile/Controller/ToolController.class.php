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

    public function getMem()
    {
        $k = I('key');
        $M = new \Memcache();
        var_dump($M->get($k));
    }

    public function test(){
        $M = new \Memcache();
        $w = $M->set('k','hello');
        $r = $M->get('k');
        var_dump($w,$r);
    }

}