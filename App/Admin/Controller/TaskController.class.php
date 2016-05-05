<?php
/**
 * Author: huanglele
 * Date: 2016/5/5
 * Time: 上午 10:39
 * Description:
 */

namespace Admin\Controller;


class TaskController extends CommonController
{

    /**
     * 显示所有的任务
     */
    public function index(){

    }



    /**
     * 显示分类
     */
    public function category(){
        $status = I('get.status');
        $status = is_numeric($status)?$status:-1;
        if($status!='-1'){
            $map['status'] = $status;
        }else{
            $map['status'] = array('neq',0);
        }
        $this->assign('status',$status);
        $map['cid'] = 0;
        $this->getData(M('category'),$map,'cid desc');
        $this->assign('CatStatus',C('CategoryStatus'));
        $this->display();
    }
    /**
     * 添加分类
     */
    public function addCat(){
        if(isset($_POST)){
            $cid = I('post.cid',0,'number_int');
        }
    }

}