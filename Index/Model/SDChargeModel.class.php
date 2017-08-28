<?php

class SDChargeModel extends Model
{
    public $table = 'single_sd_charge';

    public function addData($data)
    {
        return $this->add($data);
    }

    public function delData($where)
    {
        return $this->where($where)->del();
    }

    public function getOneField($field, $where=null)
    {
        if ($where) {
            return $this->where($where)->getField($field, true);
        } else {
            return $this->getField($field, true);
        }
    }

    public function getDataByWhere($where, $limit=null)
    {
        if ($limit) {
            if ($where) {
                return $this->where($where)->order('id desc')->limit($limit)->select();
            } else {
                return $this->order('id desc')->limit($limit)->select();
            }
        } else {
            if ($where) {
                return $this->where($where)->order('id desc')->select();
            } else {
                return $this->order('id desc')->select();
            }
        }
    }

    public function getNumberByWhere($where)
    {
        return $this->where($where)->count();
    }

    public function getWaterAndElectricSum($where = null, $isCharged)
    {
        $where['is_charged'] = array('eq', $isCharged);
        return $this->field('sum(water_money) as water, sum(electric_money) as electric')
            ->where($where)->find();
    }

    public function updateData($data, $where)
    {
        if (!$where) {
            return false;
        }
        return $this->where($where)->update($data);
    }
}