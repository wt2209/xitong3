<?php
/**
 * Created by PhpStorm.
 * User: WT
 * Date: 2015/4/18
 * Time: 15:54
 */

class SingleChargeModel extends Model{

    protected $viewModel;

     /**
     * 将$data添加进职工水电费表
     * @param $data
     * @return boolean
     */
    public function addSingleChargeData($data)
    {
        return $this->add($data);
    }

    public function getNumberByWhere($where = null)
    {
        if ($where) {
            return $this->viewModel->where($where)->count();
        } else {
            return $this->viewModel->count();
        }
    }
    public function getSumByWhere($where = null)
    {
        if ($where) {
            return $this->viewModel->field('sum(money) as sum')->where($where)->find();
        } else {
            return $this->viewModel->field('sum(money) as sum')->find();
        }
    }


    /**
     * 查找水电费。要实时获取数据，不用缓存
     * @param $where
     * @param bool $isOne
     * @return mixed
     */
    public function getViewData($where = null, $limit = false)
    {
        if ($where) {
            if ($limit) {
                return $this->viewModel->where($where)->order('charge_time desc')->limit($limit)->select();
            } else {
                return $this->viewModel->where($where)->order('charge_time desc')->select();
            }
        } eLse {
            if ($limit) {
                return $this->viewModel->order('charge_time desc')->limit($limit)->select();
            } else {
                return $this->viewModel->order('charge_time desc')->select();
            }
        }
    }

}