<?php
/**
 * Created by PhpStorm.
 * User: WT
 * Date: 2015/4/19
 * Time: 20:31
 */

abstract class CommonController extends AuthController
{
    /**
     * 人员模型
     */
    protected $_db;

    /**
     * 房间模型
     */
    protected $_room;

    /**
     * 当前楼号
     */
    protected $b;

    /**
     * 当前页码
     */
    protected $page;

    /**
     * 房间的人员数量
     * @var
     */
    protected $peopleNumberPerRoom;

    /**
     * 多层房间，6楼的人数
     * @var的
     */
    protected $peopleNumberPerRoomForSixFloor;


    /**
     * 入住
     */
    public function add()
    {
        //验证room_id
        if (empty($_POST['room_id']) || !((int)$_POST['room_id']))
            $this->ajax(array('status' => 0, 'message' => '错误：房间id错误！'));
        //验证姓名是否为空
        if (empty($_POST['name']))
            $this->ajax(array('status' => 0, 'message' => '错误：姓名不能为空！'));

        //数据处理()
        $data = $this->dealData($_POST);
        //插入数据
        if ($this->_db->addData($data)) {
            $this->ajax(array('status' => 1, 'message' => '操作成功！'));
        } else {
            $this->ajax(array('status' => 0, 'message' => $this->_db->error));
        }
    }


    /**
     * 修改信息
     */
    public function edit()
    {
        if (IS_AJAX) {
            //验证id是否存在
            if (!intval($_POST['id']))
                $this->ajax(array('status' => 0, 'message' => '错误：ID不存在！'));

            //验证姓名是否为空
            if (empty($_POST['name']))
                $this->ajax(array('status' => 0, 'message' => '错误：姓名不能为空！'));

            // 处理数据
            $data = $this->dealData($_POST);

            //修改数据
            if ($this->_db->editData($data)) {
                $this->ajax(array('status' => 1, 'message' => '操作成功！'));
            } else {
                $this->ajax(array('status' => 0, 'message' => $this->_db->error));
            }
        } else {
            halt('错误：非法请求！');
        }
    }

    /**
     * 退房
     */
    public function quit()
    {
        if (IS_AJAX) {
            $id = intval($_POST['id']);
            if (!$id)
                $this->ajax(array('status' => 0, 'message' => '错误：ID不存在！'));

            //获取人员信息
            $person = $this->_db->getDataByWhere(array('id' => $id), true);
            if (!$person)
                $this->ajax(array('status' => 0, 'message' => '错误：没有这个人！'));

            //获取所在房间
            $room = $this->_room->getRoomByWhere(array('room_id' => $person['room_id']), true);
            if (!$room)
                $this->ajax(array('status' => 0, 'message' => '错误：这个人没有房间信息！'));
            //把房间组合成字符串
            if (mb_strpos($room['building'], '高') === 0)
                $person['room'] = $room['building'] . '-' . $room['room'];
            else
                $person['room'] = $room['building'] . '-' . $room['unit'] . '-' . $room['room'];

            //卸载room_id
            if (isset($person['room_id'])) unset($person['room_id']);

            //退房时间
            $person['quit_time'] = time();

            //开启事务
            $this->_db->beginTrans();

            //添加信息到退房表
            if ($this->_db->addDataToQuit($person)) {
                //从主表中删除
                if ($this->_db->delData($id)) {
                    // 成功后提交事务
                    $this->_db->commit();
                    $this->ajax(array('status' => 1, 'message' => '操作成功！'));
                } else {
                    // 事务回滚
                    $this->_db->rollback();
                    $this->ajax(array('status' => 0, 'message' => '错误：删除信息时失败！'));
                }

            } else {
                // 事务回滚
                $this->_db->rollback();
                $this->ajax(array('status' => 0, 'message' => '错误：未能将信息添加到退房表！'));
            }
        } else {
            halt("错误：非法请求！");
        }
    }


    /**
     * 删除人员
     */
    public function del()
    {
        if (IS_AJAX) {
            $id = intval($_POST['id']);
            if (!$id) {
                $this->ajax(array('status' => 0, 'message' => '错误：ID不存在'));
            }
            //从主表中删除
            if ($this->_db->delData($id))
                $this->ajax(array('status' => 1, 'message' => '操作成功！'));
            else
                $this->ajax(array('status' => 0, 'message' => '错误：没有这个人！'));
        } else {
            halt('错误：非法请求！');
        }
    }

}