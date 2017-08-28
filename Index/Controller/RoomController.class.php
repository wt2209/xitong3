<?php

class RoomController extends AuthController
{
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