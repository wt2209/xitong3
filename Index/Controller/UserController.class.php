<?php

class UserController extends AuthController
{
    /**
     * 用户模型
     * @var
     */
    private $_db;

    /**
     * 角色模型
     * @var
     */
    private $_role;

    /**
     * 构造函数
     */
    public function __init()
    {
        $this->_db = K('User');
        $this->_role = K('Role');
    }
    /**
     * 用户列表
     */
    public function index()
    {
        $data = $this->_db->getDataByWhere();
        $tmpRole = $this->_role->getDataByWhere();
        foreach ($tmpRole as $r) {
            $role[$r['rid']] = $r;
        }
        foreach ($data as $k=>$v) {
            $data[$k] = array_merge($v, $role[$v['rid']]);
        }
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 添加用户
     */
    public function addNewUser()
    {
        if (IS_POST) {
            $username = htmlspecialchars(strip_tags($_POST['username']));
            $rid = (int) $_POST['rid'];
            $password = md5($_POST['password']);
            $confirmPassword = md5($_POST['confirmPassword']);
            if (!$username) {
                $this->error('错误：用户名不能为空！');
                exit;
            }
            if (!$rid) {
                $this->error('错误：请为用户选择一个角色！');
                exit;
            }
            if ($password !== $confirmPassword) {
                $this->error('错误：两次密码不一致！');
                exit;
            }

            $where['username'] = array('eq', $username);
            //查找用户是否存在
            if ($this->_db->getOneData($where)) {
                $this->error('错误：此用户已存在！');
                exit;
            }
            $data = array(
                'username'=>$username,
                'password'=>$password,
                'rid'=>$rid
            );
            if ($this->_db->addData($data)) {
                $this->success('操作成功！');
            } else {
                $this->error('错误：数据添加失败！');
            }
            exit;
        } else {
            $auth = $this->_role->getDataByWhere();
            $this->assign('auth', $auth);
            $this->display();
        }
    }

    /**
     * 删除用户
     */
    public function del()
    {
        if (IS_AJAX) {
            $id = (int) $_POST['id'];
            if (!$id) {
                $this->error('错误：id不存在！');
                exit;
            }

            if ($this->_db->delData($id)) {
                $this->success('操作成功！');
            } else {
                $this->error('错误：无法删除数据！');
            }
        } else {
            exit;
        }

    }
}