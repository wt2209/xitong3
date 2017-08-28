<?php
/**
 * Created by PhpStorm.
 * User: WT
 * Date: 2015/4/18
 * Time: 15:27
 */

class SingleRentChargeController extends SingleChargeController
{

    /**
     * 缴费模型
     * @var
     */
    protected $_db;
    protected $_allTypes;

    /**
     * 构造函数
     */
    public function __init()
    {
        $this->_db = K('SingleRentCharge');
        $this->_allTypes = C('SINGLE_RENT_TYPE');
    }

    /**
     * 职工床位费缴费
     */
    public function charge()
    {
        /*//设定类型
        $this->type = 'worker';*/

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if (!$id) {
            $this->ajax(array('status'=>0, 'message'=>'错误：ID不存在！'));
        }

        //验证人员是否存在
        $_workerDb = K("Worker");
        $person = $_workerDb->getDataByWhere(array('id'=>$id), true);
        if (!$person) {
            $this->ajax(array('status'=>0, 'message'=>'错误：没有此人！'));
        }
        //缴费时间
        $chargeTime = empty($_POST['charge_time']) ? time() : strtotime($_POST['charge_time']);
        //所有类型的费用
        $allTypes = C('SINGLE_RENT_TYPE');

        //开启事务
        $this->_db->beginTrans();
        foreach ($allTypes as $k=>$v) {
            if (isset($_POST[$v['name']]['money']) && is_numeric($_POST[$v['name']]['money'])) {
                $tmp = array(
                    'id'=>$id,
                    'charge_type'=>$k,
                    'money'=>$_POST[$v['name']]['money'],
                    'worker_rent_remark'=>htmlspecialchars(strip_tags($_POST[$v['name']]['remark'])),
                    'charge_time'=>$chargeTime
                );
                // 数据存储未成功时...
                if (!$this->_db->addSingleChargeData($tmp)) {
                    //回滚事务
                    $this->_db->rollback();
                    $this->ajax(array('status'=>0, 'message'=>'错误：数据添加错误，请重试！'));
                    exit();
                }
            }
        }
        //提交事务
        $this->_db->commit();
        $this->ajax(array('status'=>1, 'message'=>'操作成功！'));
    }
}