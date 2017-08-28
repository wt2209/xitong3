<?php
abstract class SingleController extends CommonController
{
    /**
     * 显示列表
     */
    abstract public function index();

    /**
     * 搜索提交后的处理方法
     */
    public function search()
    {
        //转成get
        if (IS_POST) {
            go(U('search', $_POST));
        }
        $keyword = Q('keyword');
        if (!$keyword) {
            $this->index();
        }
        //初始化
        $room = array();

        //搜索房间号
        if (mb_strpos($keyword, '-') !== false) {
            $tmp = explode('-', $keyword);
            //在房间查找，区分大学生房间和职工房间
            $where = array();
            switch ($this->_db->table) {
                case 'college':
                    $where['room_type'] = 1;
                    $where['building'] = array('eq', $tmp[0]);
                    //单元号取“相等”的条件
                    $where['unit'] = array('eq', $tmp[1]);
                    if (isset($tmp[2])) $where['room'] = array('like', $tmp[2] . '%'); //可能没有输入房间号
                    break;
                case 'dispatch':
                    $where['room_type'] = 3;
                    $where['building'] = array('eq', $tmp[0]);
                    //单元号取“相等”的条件
                    $where['unit'] = array('eq', $tmp[1]);
                    if (isset($tmp[2])) $where['room'] = array('like', $tmp[2] . '%'); //可能没有输入房间号
                    break;
                case 'worker':
                    $where['room_type'] = 2;
                    $where['building'] = array('like', '%' . $tmp[0] . '%');
                    $where['unit'] = array('eq', 1);
                    $where['room'] = array('like', $tmp[1] . '%');
                    break;
                default:
                    exit;
            }

            //查找所有满足条件的房间
            $room = $this->getRoomListByWhere($where);
        } elseif (intval($keyword)) { //搜索电话
            $where = "tel like '%{$keyword}%'";
            $room = $this->searchPersonByWhere($where);
        } else { //搜索姓名
            $where = "name like '%{$keyword}%'";
            $room = $this->searchPersonByWhere($where);
        }
        $this->assign('room', $room);
        $this->display();
    }


    /**
     * 根据where查找房间并组合出房间内的人员
     * @param $where 查询条件
     * @param $limit mysql查询中的limit
     * @param $data 需要组合的人员信息数组
     * @return array or null
     */
    protected function getRoomListByWhere($where, $limit = null, $data = null)
    {
        $room = $this->_room->getRoomByWhere($where, false, $limit);
        if(is_null($data))
            $data = $this->_db->getAllData();

        $realCount = 0;
        foreach ($room as $k => $v) {
            // 计算每个房间的人员数量
            $room[$k]['persons'] = isset($data[$room[$k]['room_id']]) ? $data[$room[$k]['room_id']] : array();
            $count = count($room[$k]['persons']);
            //每个房间里的实际人数
            if (!empty($room[$k]['persons'])) {
                $realCount += count($room[$k]['persons']);
            }

            //计算每个房间最大人员数量
            if (mb_strpos($room[$k]['building'], '高') === 0)
                $room[$k]['length'] = ($count > $this->peopleNumberPerRoom) ? $count : $this->peopleNumberPerRoom;
            elseif (mb_strpos($room[$k]['room'], '6') === 0)
                $room[$k]['length'] = ($count > $this->peopleNumberPerRoomForSixFloor) ? $count : $this->peopleNumberPerRoomForSixFloor;
            else
                $room[$k]['length'] = ($count > $this->peopleNumberPerRoom) ? $count : $this->peopleNumberPerRoom;
        }
        //将实际人数存入到数组的第一个元素里
        if (!empty($room)) {
            $room[0]['realCount'] = $realCount;
        }
        return $room;
    }

    /**
     * 根据条件查找信息
     * 全局搜索也要使用这个方法
     * @param $where
     * @return array|bool
     */
    public function searchPersonByWhere($where)
    {
        $persons = $this->_db->getDataByWhere($where);
        if (!$persons)
            return false;
        //将人员组合成以room_id为索引的数组
        foreach ($persons as $v) {
            $data[$v['room_id']][] = $v;
        }
        $arr = array_keys($data);

        //where条件
        $map['room_id'] = array('in', $arr);
        return $this->getRoomListByWhere($map, null, $data);
    }
    /**
     * 设置页码
     */
    abstract protected function setPage();

    /**
     * 调房
     */
    public function move()
    {
        if (IS_AJAX) {
            $id = intval($_POST['id']);
            if (!$id)
                $this->ajax(array('status' => 0, 'message' => '错误：ID不存在！'));

            //要调整到的房间号
            $moveTo = Q('post.moveto');
            if (!$moveTo)
                $this->ajax(array('status' => 0, 'message' => '错误：房间输入有误！'));

            $tmp = explode('-', $moveTo);
            //组合查询条件
            $where = array();
            switch ($this->_db->table) {
                case 'college':
                    $where = array(
                        'building' => isset($tmp[0]) ? $tmp[0] : '',
                        'unit' => isset($tmp[1]) ? $tmp[1] : '',
                        'room' => isset($tmp[2]) ? $tmp[2] : '',
                        'room_type' => 1
                    );
                    break;
                case 'dispatch':
                    $where = array(
                        'building' => isset($tmp[0]) ? $tmp[0] : '',
                        'unit' => isset($tmp[1]) ? $tmp[1] : '',
                        'room' => isset($tmp[2]) ? $tmp[2] : '',
                        'room_type' => 3
                    );
                    break;
                case 'worker':
                    $where = array(
                        'building' => isset($tmp[0]) ? $tmp[0] : '',
                        'room' => isset($tmp[1]) ? $tmp[1] : '',
                        'room_type' => 2
                    );
                    break;
            }

            $room = $this->_room->getRoomByWhere($where, true);
            if (!$room)
                $this->ajax(array('status' => 0, 'message' => '错误：您输入的房间不存在！'));

            //若房间人数已满，则不能调整
            if ($this->isFull($room))
                $this->ajax(array('status' => 0, 'message' => '您输入的房间已满员，不能调整！'));
            //主表数据
            $data = array(
                'id' => $id,
                'room_id' => $room['room_id']
            );

            //组合需要添加到调房表的数据,必须在editData之前完成
            $moveData = $this->setMoveData($id, $moveTo);

            //开启事务
            $this->_db->beginTrans();

            //修改主表数据
            if ($this->_db->editData($data)) {
                //将此次调房信息添加进调房表
                if ($this->_db->addDataToMove($moveData)){
                    //提交事务
                    $this->_db->commit();
                    $this->ajax(array('status' => 1, 'message' => '操作成功！'));
                } else {
                    //回滚事务
                    $this->_db->rollback();
                    $this->ajax(array('status' => 0, 'message' => '错误：未能将数据添加进调房表！'));
                }
            } else {
                //回滚事务
                $this->_db->rollback();
                $this->ajax(array('status' => 0, 'message' => '错误：数据修改失败！'));
            }
        } else {
            halt('错误：非法请求！');
        }
    }


    /**
     * 退房信息
     * 同时兼具搜索功能
     */
    public function quitInfo()
    {
        if (IS_POST) {
            go(U('quitInfo', $_POST));
        }
        //每页显示的数量
        $pageLimit = 20;

        $where = $this->setQuitOrMoveSearchCondition('quit');

        $count = $this->_db->getQuitOrMoveNumber('quit', $where);
        $page = new Page($count, $pageLimit);
        $data = $this->_db->getQuitOrMoveData('quit', $where, $page->limit());
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 删除退房信息
     */
    public function delQuitInfo()
    {
        if (IS_AJAX) {
            $id = intval($_POST['id']);
            if (!$id) {
                $this->ajax(array('status' => 0, 'message' => '错误：ID不存在'));
            }
            //从主表中删除
            if ($this->_db->delQuitData($id))
                $this->ajax(array('status' => 1, 'message' => '操作成功！'));
            else
                $this->ajax(array('status' => 0, 'message' => '错误：没有这个人！'));
        } else {
            halt('错误：非法请求！');
        }
    }
    /**
     * 调房信息
     * 同时兼具搜索功能
     */
    public function moveInfo()
    {
        if (IS_POST) {
            go(U('moveInfo', $_POST));
        }
        //每页显示的数量
        $pageLimit = 20;
        $where = $this->setQuitOrMoveSearchCondition('move');

        $count = $this->_db->getQuitOrMoveNumber('move', $where);
        $page = new Page($count, $pageLimit);
        $data = $this->_db->getQuitOrMoveData('move', $where, $page->limit());
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 删除调房信息
     */
    public function delMoveInfo()
    {
        if (IS_AJAX) {
            $id = intval($_POST['id']);
            if (!$id) {
                $this->ajax(array('status' => 0, 'message' => '错误：ID不存在'));
            }
            //从主表中删除
            if ($this->_db->delMoveData($id))
                $this->ajax(array('status' => 1, 'message' => '操作成功！'));
            else
                $this->ajax(array('status' => 0, 'message' => '错误：没有这个人！'));
        } else {
            halt('错误：非法请求！');
        }
    }

    /**
     * 设置调房或退房条件
     * @return array
     */
    private function setQuitOrMoveSearchCondition($type='quit')
    {
        $keyword = (isset($_GET['keyword']) && !empty($_GET['keyword'])) ?
            htmlspecialchars(strip_tags($_GET['keyword'])) : '';
        //初始化
        $where = array();
        if ($keyword) {
            if (mb_strpos($keyword, '-') !== false) { //房间
                if ($type == 'quit') {
                    $where['room'] = array('like', '%' . $keyword . '%');
                } else {
                    $where['old_room'] = array('like', '%' . $keyword . '%');
                }
            } elseif (intval($keyword)){
                $where['tel'] = array('like', '%' . $keyword . '%');
            } else { // 姓名
                $where['name'] = array('like', '%' . $keyword .'%');
            }
        }
        return $where;
    }
    /**
     * 将调房信息添加进调房表
     * @param int $id
     * @param string $newRoom
     * @return array
     */
    protected function setMoveData($id, $newRoom)
    {
        //人员
        $person = $this->_db->getDataByWhere(array('id' => $id), true);
        if (!$person)
            return false;
        //房间
        $room = $this->_room->getRoomByWhere(array('room_id' => $person['room_id']), true);
        if (!$room)
            return false;
        if (mb_strpos($room['building'], '高') === 0) //高层
            $person['old_room'] = $room['building'] . '-' . $room['room'];
        else  //多层
            $person['old_room'] = $room['building'] . '-' . $room['unit'] . '-' . $room['room'];

        $person['new_room'] = $newRoom;
        $person['move_time'] = time();
        if (isset($person['id'])) unset($person['id']);
        if (isset($person['room_id'])) unset($person['room_id']);
        return $person;
    }

    /**
     * 判断房间是否已满员
     * @param $room
     * @return bool
     */
    protected function isFull($room)
    {
        //当前房间的人数
        $count = $this->_db->getNumberByWhere(array('room_id' => $room['room_id']));
        //判断是4人间还是6人间
        if (mb_strpos($room['room'], '6') === 0 && $this->_db->table == 'college') { //大学生六人间
            $total = 6;
        } else { //四人间
            $total = 4;
        }
        if ($count >= $total)
            return true;
        else
            return false;
    }

    /**
     * 使用strip_tags和htmlspecialchars函数对数据进行处理
     * @param $data
     * @return array
     */
    protected function dealData($data)
    {
        $return = array();
        foreach ($data as $key => $value) {
            $return[$key] = strip_tags(htmlspecialchars($value));
        }
        return $return;
    }
}