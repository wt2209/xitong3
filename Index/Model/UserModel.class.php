<?php

/**
 * 用户管理
 * Class UserModel
 */
class UserModel extends Model
{
    public $table = 'admin';

    public function getOneData($where)
    {
        return $this->where($where)->find();
    }

    public function getDataByWhere($where = null)
    {
        if ($where) {
            return $this->where($where)->select();
        } else {
            return $this->select();
        }
    }

    public function addData($data)
    {
        return $this->add($data);
    }

    public function updateData($data)
    {
        if (!(int)$data['aid']) {
            return false;
        }
        return $this->update($data);
    }

    public function delData($id)
    {
        return $this->del($id);
    }
}
