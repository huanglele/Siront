<?php
namespace Admin\Controller;

class IndexController extends CommonController{

    public function index(){
        var_dump(session());
    }

    public function temp(){
        $this->display();
    }
}