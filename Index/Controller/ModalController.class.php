<?php

/**
 * 控制模态框的显示
 *
 * Created by PhpStorm.
 * User: WT
 * Date: 2015/4/15
 * Time: 23:00
 */
class ModalController extends AuthController
{
    /**
     * @var 数据模型，根据传参不同定义成不同的模型
     */
    private $_db;

    /**
     * @var 房间操作模型
     */
    private $_room;

    /**
     * @var 模板前缀，根据传参不同生成不同的前缀
     */
    private $_template;

    /**
     * 构造方法
     */
    public function __init()
    {
        $type = isset($_GET['type']) ? intval($_GET['type']) : 0;
        switch ($type) {
            //大学生
            case 1:
                $this->_db = K('College');
                $this->_template = 'college';
                break;
            //职工
            case 2:
                $this->_db = K('Worker');
                $this->_template = 'worker';
                break;
            //派遣工
            case 3:
                $this->_db = K('Dispatch');
                $this->_template = 'dispatch';
//                $this->_db = K('College');
                break;
            //租赁
            case 4:
                $this->_db = K('Rent');
                $this->_template = 'rent';
                break;
            default:
                $this->error('错误：参数错误！');
        }
    }


    /**
     * 调用添加职工信息（入住）的模态框
     * 适用于所有人
     */
    public function AddModal()
    {
        if (IS_AJAX) {
            $roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
            if (!$roomId) {
                $this->error('错误：人员id不存在！');
            }

            $this->assign('roomId', $roomId);
            $content = $this->fetch($this->_template . ACTION);
            $this->success($content);
        } else {
            halt("参数错误！");
        }
    }

    /**
     * 调用调房模态框
     * 适用于单身职工
     */
    public function MoveModal()
    {
        if (IS_AJAX) {
            $this->assignPersonMessage();
            $content = $this->fetch($this->_template . ACTION);
            $this->success($content);
        } else {
            halt("参数错误！");
        }
    }

    /**
     * 调用修改信息模态框
     * 适用于所有人
     */
    public function EditModal()
    {
        if (IS_AJAX) {
            $this->assignPersonMessage();
            $content = $this->fetch($this->_template . ACTION);
            $this->success($content);
        } else {
            halt("参数错误！");
        }
    }

    /**
     * 调用职工水电费模态框
     * 适用于职工
     */
    public function workerSDChargeModal()
    {
        if (IS_AJAX) {
            /*原来的解决方案。类似于床位费的缴费模式
             * $this->assignRoomMessage();
             * $this->assign('allTypes', C('SINGLE_SD_TYPE'));
             * $content = $this->fetch();
             * $this->success($content);
             */

            $roomId = $this->assignRoomMessage();
            //房间号
            $where['room_id'] = $roomId;
            //未交费
            $where['is_charged'] = 0;
            $db = K('SDCharge');
            $data = $db->getDataByWhere($where);
            $this->assign('data', $data);
            $content = $this->fetch();
            $this->success($content);
        } else {
            halt("错误：参数错误！");
        }
    }

    /**
     * 调用职工床位费模态框
     * 适用于职工
     */
    public function workerRentChargeModal()
    {
        if (IS_AJAX) {
             $this->assignPersonMessage();
             $id = intval($_GET['id']);
             $allTypes = C('SINGLE_RENT_TYPE');
             //押金是否已交
             $isPledgeCharged = $this->isPledgeChargedOfSingle($id);
             $this->assign('allTypes', $allTypes);
             $this->assign('isPledgeCharged', $isPledgeCharged);
             $content = $this->fetch();
             $this->success($content);
        } else {
            halt("错误：参数错误！");
        }
    }

    /**
     * 检测单身户中，给定的人，是否已缴押金
     * @param int $id   单身人员id
     * @return bool
     */
    private function isPledgeChargedOfSingle($id)
    {
        $chargeDb = K('SingleRentCharge');
        $where = array(
            'id'=>intval($id),
            'charge_type'=>1
        );
        if ($chargeDb->isChargedOfPledge($where))
            return 1;
        else
            return 0;
    }

    /**
     * 调用租赁缴费模态框
     * 适用于租赁
     */
    public function rentChargeModal()
    {
        if (IS_AJAX) {
            $this->assignPersonMessage();
            //1为押金，2为租金，3为物业费，4为水费，5为电费，6为取暖费，7为燃气费，8为电梯费
            $id = intval($_GET['id']);
            //押金是否已交
            $isPledgeCharged = $this->isChargedOfRent($id, 1);
            //燃气费是否已交
            $isGasCharged = $this->isChargedOfRent($id, 7);
            $this->assign('allTypes', C("RENT_CHARGE_TYPE"));
            $this->assign('isPledgeCharged', $isPledgeCharged);
            $this->assign('isGasCharged', $isGasCharged);
            $content = $this->fetch();
            $this->success($content);
            exit;
        } else {
            halt('错误：非法请求！');
        }
    }


    /**
     * 检测租赁户中，给定的人，在给定的类型上是否已缴费
     * @param int $id   租赁人员id
     * @param int $type 1为押金，2为租金，3为物业费，4为水费，
     *                  5为电费，6为取暖费，7为燃气费，8为电梯费
     * @return bool
     */
    private function isChargedOfRent($id, $type)
    {
        // 不存在的类型
        $allTypes = array_keys(C('RENT_CHARGE_TYPE'));
        if (!in_array($type, $allTypes)) {
            return false;
        }
        $chargeDb = K('RentCharge');
        $where = array(
            'rent_id'=>intval($id),
            'charge_type'=>intval($type)
        );
        if ($chargeDb->getDataByWhere($where, true))
            return true;
        else
            return false;
    }


    /**
     * 租赁续签模态框
     * 适用于租赁
     */
    public function rentRenewModal()
    {
        if (IS_AJAX) {
            $this->assignPersonMessage();
            $content = $this->fetch();
            $this->success($content);
            exit;
        } else {
            halt('错误：非法请求！');
        }
    }

    /**
     * 向模态框模板分配房间信息
     */
    private function assignRoomMessage($roomId = null)
    {
        if (!$roomId)
            $roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

        if (!$roomId) {
            $this->error('房间ID不存在！');
        }

        $this->_room = K('Room');
        $room = $this->_room->getRoomByWhere(array('room_id' => $roomId), true);
        if (!$room) {
            $this->error('没有这个房间！');
        }

        if (mb_strpos($room['building'], '高') === 0) {
            $room['roomStr'] = $room['building'] . '-' . $room['room'];
        } else {
            $room['roomStr'] = $room['building'] .  '-' . $room['unit'] . '-' . $room['room'];
        }
        //数据正确，分配信息
        $this->assign('room', $room);
        return $roomId;
    }

    /**
     * 向模态框模板分配人员信息
     * @param null $id
     */
    private function assignPersonMessage($id = null)
    {
        //验证id是否存在
        if (!$id)
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $this->error('人员id不存在！');
        }


        //取得一条数据
        $data = $this->_db->getDataByWhere(array('id' => $id), true);
        if (!$data) {
            $this->error('没有这个人！');
        }


        //数据正确，分配信息
        $this->assign('person', $data);
    }
}