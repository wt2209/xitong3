<?php


class RentModel extends CommonModel
{
    /**
     * 主表
     * @var string
     */
    public $table = 'rent';

    /**
     * 退租表
     * @var string
     */
    protected $quitTable = 'rent_quit';

    /**
     * 续签表
     * @var string
     */
    private $renewTable = 'rent_renew';

    /**
     * 退租缴费表
     * @var string
     */
    private $quitChargeTable = 'rent_charge_quit';

    public function addDataToRenew($data)
    {
        return $this->table($this->renewTable)->add($data);
    }

    /**
     * 将信息添加到退房表
     * 为了使用CommonController进行退房操作才这么写（兼容单身的退房操作）
     * 需要完成2个工作：
     *       1、将本人信息添加到退租表
     *       2、将本人的缴费信息添加到退组缴费表
     * @param $person
     */
    public function addDataToQuit($person)
    {
        //注意：控制器必须开启事务

        if (isset($person['id'])) {
            $personId = $person['id'];
            unset($person['id']);
        } else {
            return false;
        }
        //添加进退房表
        $quitId = $this->table($this->quitTable)->insert($person);
        if (!$quitId) {
            return false;
        }
        //缴费模型
        $chargeDb = K('RentCharge');
        //此人所有的缴费信息
        $chargeData = $chargeDb->getDataByWhere(array('rent_id'=>$personId));
        //存在则进行下一步处理
        if ($chargeData) {
            foreach ($chargeData as $value) {
                //卸载原有租户id
                unset($value['rent_id']);
                //使用退房后的租户id
                $value['rent_quit_id'] = $quitId;
                //添加进退房缴费表
                $this->table($this->quitChargeTable)->insert($value);
            }
        }
        return true;
    }

    public function delData($id)
    {
        //注意：控制器必须开启事务

        //缴费模型
        $chargeDb = K('RentCharge');
        //删除在缴费表中的此人的所有记录，不考虑是否删除成功
        $chargeDb->delDataByWhere(array('rent_id'=>$id));
        //删除主表中此人的记录
        if ($this->where(array('id'=>$id))->find()) {
            if ($this->where(array('id'=>$id))->del()) {
                //更新缓存
                return $this->updateCache();
            } else {
                return false;
            }
        }
        return false;
    }


    /**
     * 获取续签信息
     * @param $where
     * @param $limit
     * @return array
     */
    public function getRenewData($where = null, $limit = null)
    {
        if ($where) {
            if ($limit) {
                return $this->table($this->renewTable)->where($where)->order('id desc')->select();
            } else {
                return $this->table($this->renewTable)->where($where)->order('id desc')->select();
            }
        } else {
            if ($limit) {
                return $this->table($this->renewTable)->order('renew_id desc')->select();
            } else {
                return $this->table($this->renewTable)->order('renew_id desc')->select();
            }
        }
    }
}