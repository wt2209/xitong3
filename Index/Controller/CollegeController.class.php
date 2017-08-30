<?php
/**
 * Created by PhpStorm.
 * User: WT
 * Date: 15-4-3
 * Time: 下午10:26
 */
class CollegeController extends SingleController
{
    /**
     * 构造函数
     */
    public function __init()
    {
        $this->_db = K('College');
        $this->_room = K('Room');

        //分页，对于多层来说就是单元号
        $this->page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $this->_roomType = 1;
        // $this->b
        $this->getCurrentBuilding();
    }

    /**
     * 显示主页
     */
    public function index()
    {
        //在大学生房间查找:'type'=>1
        $where = array('building' => $this->b, 'unit' => $this->page, 'type_id' => 1);
        $room = $this->getRoomListByWhere($where);
        $this->assign('room', $room);
        $this->setPage();
        $this->display('index');
    }


    /**
     * 设置页码显示方式，并向模板分配页码变量
     */
    protected function setPage()
    {
        $totalPageNum = $this->_room->getUnitNumber($this->b);
        $pageStr = '';

        //中间页码
        for ($i = 1; $i <= $totalPageNum; $i++) {
            if ($i == $this->page) {
                $pageStr .= '<a href="javascript:;" class="btn btn-success">' . $i . '</a>';
            } else {
                $pageStr .= '<a href="' . U('index', array('b'=>$this->b, 'page' => $i)) . '" class="btn btn-default">' . $i . '</a>';
            }
        }

        //分配变量
        $this->assign('page', $pageStr);
    }

}