<?php

class RoomController extends AuthController
{
    protected $roomModel;

    public function __init() {
        $this->roomModel = K('room');
    }
    public function index()
    {
        $rooms = $this->roomModel->getAllRoom();
        $this->assign('rooms', $rooms);
        $this->display();
    }

    /**
     * 所有房间类型
     */
    public function types()
    {
        $types = $this->roomModel->getTypes();
        $this->assign('types', $types);
        $this->display();
    }

    /**
     * 添加房间类型
     */
    public function addType()
    {
        $this->display();
    }

    public function storeType()
    {
        $data = array(
            'type_name'=>Q('type_name'),
            'description'=>Q('description'),
            'created_at'=>date('Y-m-d H:i:s')
        );
        $this->roomModel->addType($data);
        $this->success();
    }














    public function setRoomRemark()
    {
        if (IS_AJAX) {
            $data['room_id'] = intval($_POST['room_id']);
            $data['room_remark'] = htmlspecialchars(strip_tags($_POST['room_remark']));
            if (!$data['room_id']) {
                $this->error('错误：没有room_id！');
                exit;
            }
            $db = K('Room');
            if ($db->updateRoomData($data)) {
                $this->success('修改成功！', null, 0.1);
            } else {
                $this->success('错误：数据添加失败！');
            }
        } else {
            halt('错误：参数错误！');
        }
    }
}