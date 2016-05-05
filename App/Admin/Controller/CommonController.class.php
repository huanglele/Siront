<?php
/**
 * Author: huanglele
 * Date: 2016/4/29
 * Time: 上午 11:05
 * Description:
 */

namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller
{
    public $aid = null;
    public $role = null;
    public $status = 0;

    /**
     * 判断登录
     */
    public function _initialize(){
        $this->aid = session('aid');
        $this->role = session('role');
        $this->status = session('status');
        $ac = strtolower(ACTION_NAME);
        if(!in_array($ac,array('login','logout'))){ //排除登录登出操作
            if(!$this->aid){ //没有登录
                $this->login();
            }else{
                if($this->status != 1){ //账户被限制了
                    die('你的账户已经被限制了，请联系管理员');
                }else{//登录成功
                    if(!$this->checkAccess()){$this->error('没有操作权限');die;}
                    $this->assign('role',$this->role);
                    $this->assign('controller',strtolower(CONTROLLER_NAME));
                }
            }
        }
    }

    //管理员登录
    public function login(){
        layout(false);
        if(isset($_POST['name'])){
            $map['name'] = $_POST['name'];
            $info = M('admin')->where($map)->find();
            if($info){
                if($info['password']!=md5($_POST['password'])){
                    //密码不正确
                    $this->error('密码错误');
                }else{
                    if($info['status']!=1){
                        //限制账户
                        $this->error('密码错误');
                    }else{
                        //登录成功
                        session('aid',$info['aid']);
                        session('role',$info['role']);
                        session('name',$_POST['name']);
                        session('status',$info['status']);
                        $this->success('登录成功',U('index/index'));
                    }
                }
            }else{
                //用户名不存在
                $this->error('用户名不存在');
            }
        }else{
            layout(false);
            C('SHOW_PAGE_TRACE',false);
            $this->display('Public/login');die;
        }
    }

    public function logout(){
        session('aid',null);
        session('role',null);
        session('status',null);
        $this->redirect('common/login');
    }

    /**
     * @param $M 需要查询的数据库对象
     * @param $map 查询条件
     * @param $order 排序条件
     * @param bool|false $field 需要查询的字段
     * @return mixed 查询的数据数组
     */
    protected function getData($M, $map, $order, $field=false){
        $count = $M->where($map)->count();
        $Page = new \Think\Page($count);
        $show = $Page->show();
        if($field){
            $list = $M->where($map)->field($field)->order($order)->limit($Page->firstRow,$Page->listRows)->select();
        }else{
            $list = $M->where($map)->order($order)->limit($Page->firstRow,$Page->listRows)->select();
        }
        $this->assign('page',$show);
        $this->assign('list',$list);
        return $list;
    }

    /**
     * @return bool 检测结果如果有返回true，没有返回false
     */
    private function checkAccess(){
        $RoleOfAccess = include COMMON_PATH.'Conf/RoleOfAccess.php';
        $action =  strtolower(CONTROLLER_NAME.'.'.ACTION_NAME);
        if(array_key_exists($action,$RoleOfAccess)){
            $access = $RoleOfAccess[$action];   //操作允许的角色
            if(in_array($this->role,$access)){
                return true;
            }else{
                return false;
            }
        }else{
            //没有定义权限，直接返回true
            return true;
        }
    }

}