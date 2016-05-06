<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display('index');
    }

    public function mem(){
        session('hello','你好');
    }

    public function info(){
        var_dump(session());
    }

    public function map(){
        $this->display('map');
    }
}