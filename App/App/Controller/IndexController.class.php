<?php
namespace App\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        $Mem = new \Memcache();
        var_dump($Mem);
    }

}