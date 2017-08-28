<?php

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if(!isset($_SESSION['aid']) || !isset($_SESSION['username'])) {
            echo "<script>parent.window.location.href='".U("Login/login")."';</script>";
            exit;
        }
        /* 吧节点添加node表中的临时代码
         * $nodeModel = M("node");
        $where = array("controller" => CONTROLLER, "action" =>ACTION);
        if (!$nodeModel->where($where)->field("id")->find()) {

            $nodeModel->add($where);
        }*/
        //检查权限
        if(!$this -> checkAccess()) {
            $this->authError('您没有权限！');
        }

    }

    //权限验证
    protected function checkAccess() {

        //超级管理员放行
        if ( $_SESSION['rid']==1) {
            return true;
        }
        $nodeModel = M("node");
        $where = array("controller" => CONTROLLER, "action" =>ACTION);
        $node = $nodeModel->where($where)->field("id")->find();
        //node不存在的节点自动通过验证
        if (!$node) {
            return true;
        } else {
            $AccessModel = M('access');
            $where = array("node_id" => $node['id'], "role_id" => session("rid"));
            return $AccessModel->where($where)->find();
        }
    }

    protected function authError($error)
    {
        if (IS_AJAX) {
            $this->error($error);
        } else {
            $this->error($error, null, 1, MODULE_PUBLIC_PATH.'authError.html');
        }
    }
}