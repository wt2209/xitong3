<?php

class SDBaseModel extends Model
{
    public $table = 'single_sd_base';

    public function addBaseData($data)
    {
        return $this->add($data);
    }

    public function getDataByWhere($where, $limit = null)
    {
        if ($limit) {
            return $this->where($where)->limit($limit)->order('id desc')->select();
        } else {
            return $this->where($where)->order('id desc')->select();
        }
    }

    public function delData($where)
    {
        return $this->where($where)->del();
    }

    public function getOneField($field)
    {
        return $this->getField($field, true);
    }

    public function getDistinctField($field)
    {
        return $this->field('distinct('.$field.')')->getField(null, true);
    }

    public function getNumberByWhere($where)
    {
        if (!$where) {
            return $this->count();
        }
        return $this->where($where)->count();
    }
}