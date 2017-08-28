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

        $this->peopleNumberPerRoomForSixFloor = 6;
        $this->peopleNumberPerRoom = 4;
        //分页，对于多层来说就是单元号
        $this->page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        //楼号
        $this->b = isset($_GET['b']) ? Q('get.b') : '';
    }

    /**
     * 显示主页
     */
    public function index()
    {
        //不存在楼号
        if (empty($this->b)) {
            $this->display('noBuilding');
            exit;
        }
        //在大学生房间查找:'type'=>1
        $where = array('building' => $this->b, 'unit' => $this->page, 'room_type' => 1);
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
        /*//上一页
        if ($this->page == 1) {
            $pageStr = '<a href="javascript:;" class="btn btn-default">上一页</a>';
        } else {
            $pageStr = '<a href="' . U('index', array('b'=>$this->b, 'page' => $this->page - 1)) . '" class="btn btn-default">上一页</a>';
        }*/

        //中间页码
        for ($i = 1; $i <= $totalPageNum; $i++) {
            if ($i == $this->page) {
                $pageStr .= '<a href="javascript:;" class="btn btn-success">' . $i . '</a>';
            } else {
                $pageStr .= '<a href="' . U('index', array('b'=>$this->b, 'page' => $i)) . '" class="btn btn-default">' . $i . '</a>';
            }
        }

        /*//下一页
        if ($this->page == $totalPageNum) {
            $pageStr .= '<a href="javascript:;" class="btn btn-default">下一页</a>';
        } else {
            $pageStr .= '<a href="' . U('index', array('b'=>$this->b, 'page' => $this->page + 1)) . '" class="btn btn-default">下一页</a>';
        }*/

        //分配变量
        $this->assign('page', $pageStr);
    }
}