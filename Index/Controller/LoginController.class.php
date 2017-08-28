<?php

class LoginController extends Controller
{
    public function login()
    {
        if(IS_POST){
            //已登录则直接跳转
            if(isset($_SESSION['aid']) && isset($_SESSION['username'])){
                $this->display('index.html');
                die;
            }

            $username = Q("post.username");
            $password = Q("post.password",null,'md5');
            if(empty($username) || empty($password)) $this->error('请输入用户名或密码');

            //登录方式有问题！！！！！
            //TODO
            $where = array('username'=>$username,'password'=>$password);
            $db = M('admin');
            $data = $db->where($where)->find();
            if ($data) {
//                session('rolename',$data['name']);
                session('aid',$data['aid']);
                session('rid',$data['rid']);
                session('username',$data['username']);
                session('display_name',$data['display_name']);
                session('logintime',$data['logintime']);
                //记录最后登录时间
                $db ->save(array('aid'=>session('aid'),'logintime'=>time()));
                go('Index/index');
            } else {
                $this->error('用户名或密码错误');
            }
            /*$db = K('UserListView');
            $where = array('username'=>$username,'password'=>$password);
            if($data = $db->where($where)->find()){
                session('rolename',$data['name']);
                session('aid',$data['aid']);
                session('rid',$data['rid']);
                session('username',$data['username']);
                session('logintime',$data['logintime']);
                //记录最后登录时间
                $db ->save(array('aid'=>session('aid'),'logintime'=>time()));
                go('Index/index');
            }else{
                $this->error('用户名或密码错误');
            }*/
        }else{
            if(isset($_SESSION['aid']) && isset($_SESSION['username'])){
                go('Index/index');
                die;
            }
            $this->display();
        }
    }

    public function logout()
    {
        $_SESSION = array();
        session_unset();
        session_destroy();
        echo "<script>parent.window.location.href='".U("Index/index")."';</script>";
    }
}