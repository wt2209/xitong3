<?php

/**
 * Created by PhpStorm.
 * User: WT
 * Date: 15-4-3
 * Time: 下午10:26
 */
class WorkerController extends SingleController
{

    /**
     * 设置每一页共有多少个房间
     * 则楼层数为 $onePageNum/4
     * @var int
     */
    private $onePageNum = 20;

    /**
     * 构造函数
     */
    public function __init()
    {
        $this->_db = K('Worker');
        $this->_room = K('Room');
        $this->peopleNumberPerRoom = 4;
        //分页
        $this->page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        //楼号
        $this->b = isset($_GET['b']) ? Q('get.b') : '';
    }

    /**
     * 显示主页
     */
    public function index()
    {
        // 有高1、高2时的设计
        /*if (!$this->b) {
            $this->display('noBuilding');
            exit;
        }*/
        //在职工房间查找:'type'=>2
        $this->b = $this->b ? $this->b : '高2';
        $where = array('building' => $this->b, 'room_type' => 2);
        //limit 20个房间一页   即5层一页
        $limit = (($this->page - 1) * $this->onePageNum) . ',' . $this->onePageNum;
        $room = $this->getRoomListByWhere($where, $limit);
        $this->assign('room', $room);
        $this->setPage();
        $this->display('index');
    }

    /**
     * 设置页码显示方式，并向模板分配页码变量
     */
    protected function setPage()
    {
        /*  <a href="" class="btn btn-default">上一页</a>
          <a href="" class="btn btn-default">1</a>
          <a href="" class="btn btn-default">2</a>
          <a href="" class="btn btn-success">3</a>
          <a href="" class="btn btn-default">4</a>
          <a href="" class="btn btn-default">上一页</a>*/

        /*
         * $url = __URL__;
         * $url = remove_url_param('page', $url);
         */
        $totalNum = $this->_room->getAllNumber(array('building' => $this->b));
        $totalPageNum = ceil($totalNum / $this->onePageNum);
        $pageStr = '';
        if ($this->b == '高1')
            $start = 3;
        else
            $start = 1;

        /*//上一页
        if ($this->page == 1) {
            $pageStr = '<a href="javascript:;" class="btn btn-default">上一页</a>';
        } else {
            $pageStr = '<a href="' . U('index', array('b'=>$this->b, 'page' => $this->page - 1)) . '" class="btn btn-default">上一页</a>';
        }*/

        //中间页码
        for ($i = 1; $i <= $totalPageNum; $i++) {
            if (($start + ($i-1)*floor($this->onePageNum / 4)) < 13 && ($start + $i * floor($this->onePageNum / 4) -1 ) >= 13)
                $page = ($start + ($i-1)*floor($this->onePageNum / 4)) . '-'. ($start + $i * floor($this->onePageNum / 4));
            elseif(($start + ($i-1)*floor($this->onePageNum / 4)) >= 13)
                $page = ($start + ($i-1)*floor($this->onePageNum / 4)+1) . '-' . ($start + $i * floor($this->onePageNum / 4)- 1);
            else
                $page = ($start + ($i-1)*floor($this->onePageNum / 4)) . '-' . ($start + $i * floor($this->onePageNum / 4)- 1);


            if ($i == $this->page) {
                $pageStr .= '<a href="javascript:;" class="btn btn-success">' . $page . '</a>';
            } else {
                $pageStr .= '<a href="' . U('index', array('b'=>$this->b, 'page' => $i)) . '" class="btn btn-default">' . $page . '</a>';
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