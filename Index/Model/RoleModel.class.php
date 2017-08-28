<?php

class RoleModel extends Model
{
    public $table = 'role';

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

    public function delData($id)
    {
        return $this->del($id);
    }

}