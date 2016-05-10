<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display('index');
    }

    public function mem(){
        $Mem = new \Memcache();
        var_dump($Mem);
    }

    public function info(){
        var_dump(session());
    }

    public function map(){
        $this->display('map');
    }
}