<?php
/**
 * Created by PhpStorm.
 * User: WT
 * Date: 2015/4/18
 * Time: 15:27
 */
//没有使用这个类
exit;
class SingleSDChargeController extends SingleChargeController
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
        $this->_db = K('SingleSDCharge');
        $this->_allTypes = C('SINGLE_SD_TYPE');
    }


    /**
     * 职工水电费缴费
     */
    public function charge()
    {
        $roomId = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
        if (!$roomId) {
            $this->ajax(array('status'=>0, 'message'=>'错误：房间ID不存在！'));
        }

        //验证房间是否存在
        $_roomDb = K("Room");
        $room = $_roomDb->getRoomByWhere(array('room_id'=>$roomId), true);
        if (!$room) {
            $this->ajax(array('status'=>0, 'message'=>'错误：没有此房间！'));
        }

        $_workerDb = K('Worker');
        $count = $_workerDb->getNumberByWhere(array('room_id'=>$room['room_id']));
        //此房间内没人居住
        if ($count == 0) {
            $this->ajax(array('status'=>0, 'message'=>'错误：此房间内没人居住！'));
        }

        //缴费人姓名，不是必须项
        $name = htmlspecialchars(strip_tags($_POST['name']));

        //缴费时间
        $chargeTime = empty($_POST['charge_time']) ? time() : strtotime($_POST['charge_time']);
        //所有类型的费用
        $allTypes = C('SINGLE_SD_TYPE');
        //开启事务
        $this->_db->beginTrans();
        foreach ($allTypes as $k=>$v) {
            if (isset($_POST[$v['name']]['money']) && is_numeric($_POST[$v['name']]['money'])) {
                $tmp = array(
                    'room_id'=>$roomId,
                    'name'=>$name,
                    'charge_type'=>$k,
                    'money'=>$_POST[$v['name']]['money'],
                    'remark'=>htmlspecialchars(strip_tags($_POST[$v['name']]['remark'])),
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