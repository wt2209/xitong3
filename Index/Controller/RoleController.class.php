<?php

class RoleController extends AuthController
{
    private $_db;


    public function __init()
    {
        $this->_db = K('Role');
    }

    public function index()
    {
        $data = $this->_db->getDataByWhere();
        $this->assign('data', $data);
        $this->display();
    }

    public function addNewRole()
    {
        if (IS_POST) {
            $roleName = htmlspecialchars(strip_tags($_POST['role_name']));
            $remark = htmlspecialchars(strip_tags($_POST['remark']));
            if (!$roleName) {
                $this->error('错误：角色名不能为空！');
                exit;
            }
            $data = array(
                'role_name'=>$roleName,
                'remark'=>$remark
            );
            if ($this->_db->addData($data)) {
                $this->success('操作成功！');
            } else {
                $this->error('错误：数据添加失败！');
            }
            exit;
        } else {
            $this->display();
        }
    }

    public function del()
    {
        if (IS_AJAX) {
            $id = (int) $_POST['id'];
            if (!$id) {
                $this->error('错误：id不存在！');
                exit;
            }
            $where['rid'] = array('eq', $id);
            $where['issystem'] = array('eq', 1);
            if ($this->_db->getDataByWhere($where)) {
                $this->error('错误：不允许删除系统角色！');
                exit;
            }
            //查看是否有用户属于此角色，有的话，不能删除
            if (K('User')->getDataByWhere(array('rid'=>array('eq', $id)))) {
                $this->error('错误：请先删除此角色下的用户！');
                exit;
            }
            if ($this->_db->delData($id)) {
                $this->success('操作成功！');
            } else {
                $this->error('错误：删除数据失败！');
            }
            exit;
        }
    }


    /**
     * 配置权限
     */
    public function setAccess(){
        if(IS_POST){
            $rid = (int)$_POST['rid'];
            foreach ($_POST['access'] as $v) {
                $data[] = array(
                    'role_id'=>$rid,
                    'node_id'=>$v
                );
            }
            $accessDb = M('access');
            $accessDb->beginTrans();
            if ($accessDb->where(array('role_id'=>array('eq', $rid)))->delete()) {
                foreach ($data as $d) {
                    if(!$accessDb->add($d)) {
                        $accessDb->rollback();
                        $this->error('错误：数据添加失败！');
                    }
                }
                $accessDb->commit();
                $this->success('操作成功！');
                exit;
            } else {
                $this->error('错误：删除原有权限失败！');
            }

        }else{
            $rid = (int)$_GET['rid'];
            if (!$rid) {
                exit;
            }

            $role = $this->_db->getOneData(array('rid'=>array('eq', $rid)));
            if (!$role) {
                $this->error('错误：没有这个角色！');
            }

            //获得当前角色的权限
            $access = M('access')->where(array('role_id'=>array('eq', $rid)))->getField('node_id',true);
            //获取全部节点
            $nodes = M('node')->select();
            foreach ($nodes as $key => $value) {
                if(in_array($value['id'], $access)){
                    $value['access'] = 1;
                }
                $nodes[$key] = $value;
            }

            $nodes = $this->node_merge($nodes);
            $this->assign('nodes', $nodes);
            $this->assign('role', $role);
            $this->display();
        }

    }

    private function node_merge($nodes,$pid=0){
        $arr = array();
        foreach ($nodes as $key => $value) {
            if($value['pid'] == $pid){
                $value['child'] = $this->node_merge($nodes,$value['id']);
                $arr[] = $value;
            }
        }
        return $arr;
    }

}