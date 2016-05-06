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
        var_dump($menu);
    }

    public function delEmptyCat(){
        $map['name'] = '';
        M('category')->where($map)->delete();
    }
}