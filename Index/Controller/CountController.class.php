<?php

class CountController extends AuthController
{
    private $_room;

    public function __init()
    {
        $this->_room = K('room');
    }

    /**
     * 统计大学生人数
     *
     */
    public function college()
    {

        /*  有两种实现方式：方式1：每一个房间都发送一个sql查询人数，
         *                      此方式所需时间是第二种方式的3倍多
         *   $db = K('College');
         *   $tmp = $this->_room->getRoomByWhere(array('room_type'=>1));
         *   $room = array();
         *   $total = 0;
         *   foreach ($tmp as $r) {
         *       $room[$r['building']][$r['unit']][$r['room']] = $db->getNumberByWhere(array('room_id'=>$r['room_id']));
         *       $total += $room[$r['building']][$r['unit']][$r['room']];
         *   }
         *   echo $total;
         *   p($room);
         */
        //方式2：先取出所有房间和人员，用php进行运算。使用了缓存
        $roomTmp = $this->_room->getRoomByWhere(array('room_type'=>1));
        //这个方法使用了缓存
        $persons = K('College')->getAllData();
        foreach ($roomTmp as $k => $r) {
            $roomCount = count(isset($persons[$roomTmp[$k]['room_id']]) ? $persons[$roomTmp[$k]['room_id']] : array());
            $room[$r['building']][$r['unit']][$r['room']] = $roomCount;
        }
        $this->assign('room', $room);
        $this->display();

    }

    public function worker()
    {
        $roomTmp = $this->_room->getRoomByWhere(array('room_type'=>2));
        //这个方法使用了缓存
        $persons = K('worker')->getAllData();
        foreach ($roomTmp as $k => $r) {
            $roomCount = count(isset($persons[$roomTmp[$k]['room_id']]) ? $persons[$roomTmp[$k]['room_id']] : array());
            $room[$r['building']][$r['unit']][$r['room']] = $roomCount;
        }
        $this->assign('room', $room);
        $this->display();
    }

    public function dispatch()
    {
        echo '正在开发';
    }

    public function rent()
    {
        $roomTmp = $this->_room->getRoomByWhere(array('room_type'=>4));
        //这个方法使用了缓存
        $persons = K('rent')->getAllData();
        $room = array();
        foreach ($roomTmp as $k => $r) {
            $roomCount = count(isset($persons[$roomTmp[$k]['room_id']]) ? $persons[$roomTmp[$k]['room_id']] : array());
            $room[$r['building']] = (isset($room[$r['building']])?$room[$r['building']]:0) + $roomCount;
        }
        $this->assign('room', $room);
        $this->display();
    }
}