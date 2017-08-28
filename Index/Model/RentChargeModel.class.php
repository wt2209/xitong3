<?php
/**
 * Created by PhpStorm.
 * User: WT
 * Date: 2015/4/18
 * Time: 15:54
 */

class RentChargeModel extends Model{
    /**
     * 主表
     * @var string
     */
    public $table = 'rent_charge';

    /**
     * 退租人员缴费表
     * @var string
     */
    protected $quitChargeTable = 'rent_charge_quit';

    /**
     * 退租表
     * @var string
     */
    protected $quitTable = 'rent_quit';
    /**
     * 视图模型
     * @var
     */
    private  $viewModel;

    /**
     * 构造函数
     */
    public function __init()
    {
        $this->viewModel = K('RentChargeView');
    }

    /**
     * 获取全部信息。因为要实时获取数据，因此不适用缓存
     * @return array
     */
    public function getViewData($where=null, $limit=null)
    {
        //获取视图模型中的全部数据
        return $this->viewModel->getDataByWhere($where, $limit);
    }


    /**
     * 将$data添加进租赁缴费表
     * @param $data
     * @return boolean
     */
    public function addData($data)
    {
        return $this->add($data);
    }

    /**
     * 获取满足条件的记录数
     * @param null $where
     * @return mixed
     */
    public function getNumberByWhere($where = null)
    {
        if ($where)
            return $this->viewModel->where($where)->count();
        else
            return $this->viewModel->count();
    }

    /**
     * 根据条件获取金额总数
     * @param null $where
     * @return mixed
     */
    public function getSumMoneyByWhere($where = null)
    {
        if ($where)
            return $this->viewModel->where($where)->field('sum(money) as sum')->find();
        else
            return $this->viewModel->field('sum(money) as sum')->find();
    }

    /**
     * 获取数据
     * @param $where
     * @param bool $isOne
     * @return mixed
     */
    public function getDataByWhere($where, $isOne = false)
    {
        if ($where) {
            if ($isOne) {
                return $this->where($where)->find();
            } else {
                //已最新时间排序
                return $this->where($where)->order('charge_time desc')->select();
            }
        } else {
            if ($isOne) {
                return $this->find();
            } else {
                //已最新时间排序
                return $this->order('charge_time desc')->select();
            }
        }

    }

    /**
     * 删除数据
     * @param $where
     * @return mixed
     */
    public function delDataByWhere($where)
    {
        return $this->where($where)->del();
    }

    /**
     * 更新季度缓存
     * @return mixed
     */
    public function updateQuartersCache()
    {
        $quarters = $this->where('quarter != 0')->getField('distinct(quarter)', true);
        S('quarters', $quarters);
        return $quarters;
    }

    /**
     * 获取退租户的信息
     * @param $where
     * @return array
     */
    public function getQuitData($where = null)
    {
        if ($where) {
            return $this->table($this->quitTable)->where($where)->select();
        } else {
            return $this->table($this->quitTable)->select();
        }
    }

    /**
     * 获取缴费记录数
     * @param null $where
     * @return mixed
     */
    public function getQuitChargeDataNumber($where = null)
    {
        if ($where) {
            return $this->table($this->quitChargeTable)->where($where)->count();
        } else {
            return $this->table($this->quitChargeTable)->count();
        }
    }

    /**
     * 获取退租户的缴费记录
     * @param null $where
     * @return array
     */
    public function getQuitChargeData($where = null, $limit = null)
    {
        if ($limit) {
            if ($where) {
                return $this->table($this->quitChargeTable)->where($where)->limit($limit)->select();
            } else {
                return $this->table($this->quitChargeTable)->limit($limit)->select();
            }
        } else {
            if ($where) {
                return $this->table($this->quitChargeTable)->where($where)->select();
            } else {
                return $this->table($this->quitChargeTable)->select();
            }
        }

    }
}