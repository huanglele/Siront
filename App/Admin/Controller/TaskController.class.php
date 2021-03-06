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

    public function _initialize(){
        parent::_initialize();
        $taskStatusColor =array(
            '0' => '',
            '1' => 'bg-green',    // 发布中,
            '2' => 'bg-light-blue',  //已接单
            '3' => 'bg-blue',   //已完成
            '4' => 'bg-navy',      //已评价
            '5' => 'bg-black',      //放弃
        );
        $this->assign('TaskStatusColor',$taskStatusColor);
    }

    /**
     * 处理搜索
     */
    private function searchToMap(){

    }

    /**
     * 显示所有的任务
     */
    public function index(){
        $map = array();
        $status = I('get.status',-1,'number_int');
        if($status!='-1'){
            $map['status'] = $status;
        }
        $this->assign('status',$status);
        $field = 'tid,from_uid as fuid,title,cid,create_time as ctime,operate_time as otime,status';
        $uidArr = array(0);
        $list = $this->getData(M('Task'),$map,'tid desc',$field);
        if(is_array($list)){
            foreach($list as $u){
                $uidArr[] = $u['fuid'];
            }
            $userInfo = M('user')->where(array('uid'=>array('in',$uidArr)))->getField('uid,nickname,phone');
            $this->assign('userInfo',$userInfo);
        }
        $this->assign('CatName',S('CatName'));
        $this->assign('TaskStatus',C('TaskStatus'));
        $this->display('index');
    }

    /**
     * 查看一个任务详情
     */
    public function item(){
        $tid = I('get.tid',0,'number_int');
        $info = M('task')->find($tid);
        if($info){
            //获取双方的信息
            $usersUid[] = $info['from_uid'];
            if($info['work_uid']){
                $usersUid[] = $info['work_uid'];
            }
            $this->assign('CatName',S('CatName'));
            $this->assign('TaskStatus',C('TaskStatus'));
            $usersInfo = M('user')->where(array('uid'=>array('in',$usersUid)))->getField('uid,nickname,phone');
            $this->assign('usersInfo',$usersInfo);
            $this->assign('info',$info);
            $this->display('item');
        }else{
            $this->error('页面不存在');
        }
    }

    /**
     * 删除一个任务
     */
    public function delTask(){
        $tid = I('get.tid');
        if(M('task')->where(array('tid'=>$tid))->setField('status',6)){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    /**
     * 显示一级分类
     */
    public function category(){
        $status = I('get.status',-1,'number_int');
        if($status!='-1'){
            $map['status'] = $status;
        }
        $this->assign('status',$status);
        $map['cid'] = 0;
        $this->getData(M('category'),$map,'cid desc');
        $nums = M('category')->where(array('cid' => array('neq',0)))->group('cid')->getField('cid,count(`id`) as num');
        $this->assign('nums',$nums);
        $this->assign('CatStatus',C('CategoryStatus'));
        $this->assign('title','一级分类');
        $this->assign('cid',0);
        $this->assign('includeFiles','Task/addCatModal,Task/updateCatModal');
        $this->display();
    }

    /**
     * 显示二级分类
     */
    public function showCat(){
        $cid = I('get.cid',0,'number_int');
        $M = M('category');
        $title = $M->where(array('id'=>$cid))->getField('name');
        if(is_null($title)){$this->error('分类不存在',U('task/category'));die;}

        $status = I('get.status',-1,'number_int');
        if($status!='-1'){
            $map['status'] = $status;
        }
        $this->assign('status',$status);
        $map['cid'] = $cid;
        $this->getData($M,$map,'cid desc');
        $this->assign('CatStatus',C('CategoryStatus'));
        $this->assign('title',$title);
        $this->assign('cid',$cid);
        $this->assign('includeFiles','Task/addCatModal,Task/updateCatModal');
        $this->display('showCat');
    }


    /**
     * 添加分类
     */
    public function addCat(){
        if(isset($_POST)){
            $cid = I('post.cid',0,'number_int');
            $names = I('post.names');
            $status = I('post.status');
            $allDate = array();
            $count = count($names);
            for($i=0; $i<$count;$i++){
                $name = trim($names[$i]);
                if($name){
                    $t['cid'] = $cid;
                    $t['name'] = $name;
                    $t['status'] = $status[$i];
                    $allDate[] = $t;
                }
            }
            if(M('category')->addAll($allDate)){
                $this->updateCacheCategory();
                $this->success('添加成功',U('task/category'));
            }else{
                $this->error('添加失败',U('task/category'));
            }
        }
    }

    /**
     * 更新分类
     */
    public function updateCat(){
        if(isset($_POST)){
            if(M('category')->save($_POST)){
                $this->updateCacheCategory();
                $this->success('更新成功');
            }else{
                $this->error('更新失败');
            }
        }else{
            $this->error('参数错误');
        }
    }
    /**
     * 删除分类
     */
    public function delCat(){
        $id = I('get.id');
        $M = M('category');
        $info = $M->field('id,cid')->find($id);
        if(!$info){$this->error('页面不存在');die;}
        if($info['cid']==0){    //一级标题
            //判断是否有二级标题
            $num = $M->where(array('cid'=>$id))->count();
            if($num){
                $this->error('请先删除该分类下的二级分类');
            }else{
                $M->delete($id);
                $this->updateCacheCategory();
                $this->success('删除成功');
            }
        }else{  //二级标题
            $num = M('task')->where(array('cid'=>$id))->count();
            if($num){
                $this->error('已存在任务不可删除');
            }else{
                $M->delete($id);
                $this->updateCacheCategory();
                $this->success('删除成功');
            }
        }
    }

    /**
     *更新分类缓存信息
     */
    public function updateCacheCategory(){
        getCat(true);
    }


}