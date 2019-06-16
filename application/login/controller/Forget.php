<?php
namespace app\login\controller;

use think\Controller;

class Forget extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}
