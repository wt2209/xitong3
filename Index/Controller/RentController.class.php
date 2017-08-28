<?php

class RentController extends CommonController
{
    /**
     * 是否是高层
     * @var bool
     */
    private $isHighBuilding = false;

    /**
     * 设置每一页共有多少个房间，高层楼使用
     * 则楼层数为 $onePageNum/4
     * @var int
     */
    private $onePageNum = 20;

    /**
     * 构造函数
     */
    public function __init()
    {
        $this->_db = K('Rent');
        $this->_room = K('Room');
        //分页
        $this->page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        //楼号
        $this->b = isset($_GET['b']) ? Q('get.b') : '';
        //判断是否是高层
        if(mb_strpos($this->b, '高') === 0)
            $this->isHighBuilding = true;
    }

    public function index()
    {
        if (!$this->b) {
            $this->display('noBuilding');
            exit;
        }
        //在租赁房间查找:'type'=>4
        if ($this->isHighBuilding) {
        //limit 20个房间一页   即5层一页
            $where = array('building' => $this->b, 'room_type' => 4);
            $limit = (($this->page - 1) * $this->onePageNum) . ',' . $this->onePageNum;
            $room = $this->getRoomListByWhere($where, $limit);
            if ($this->b == '高1') {
                $this->setPageForHigh(3);
            } else {
                $this->setPageForHigh();
            }
        } else {
            $where = array('building' => $this->b, 'unit'=>$this->page, 'room_type' => 4);
            $room = $this->getRoomListByWhere($where);
            $this->setPageForLow();
        }
        $this->assign('room', $room);
        $this->display();
    }

    public function search()
    {
        if (IS_POST) {
            go(U('search', $_POST));
        }
        //搜索类型：1|租赁合同签署日 2|租赁合同终止日，默认为1
        $searchType = isset($_GET['searchType']) ? intval($_GET['searchType']) : 1;
        $startTime = isset($_GET['startTime']) ? strtotime($_GET['startTime']) : '';
        $endTime = isset($_GET['endTime']) ? strtotime($_GET['endTime']) : '';
        $keyword = isset($_GET['keyword']) ? htmlspecialchars(strip_tags($_GET['keyword'])) : '';

        switch ($searchType) {
            case 1:
                $timeField = 'rent_s';
                break;
            case 2:
                $timeField = 'rent_e';
        }

        $where = array();
        if ($startTime) {
            $where[] = $timeField.'>='.$startTime;
        }
        if ($endTime) {
            $where[] = $timeField.'<='.$endTime;
        }
        if ($keyword) {
            //房间号
            if (mb_strpos($keyword, '-') !== false) {
                //房间搜索不需要起止时间，因此清空起止时间
                $where = array();
                $where['room_type'] = 4; //租赁
                $tmp = explode('-', $keyword);
                //在房间查找，区分高层房间和多层房间
                if (mb_strpos($tmp[0], '高') === 0 || (isset($tmp[1]) && mb_strlen($tmp[1]) > 1)) {
                    $where['unit'] = array('eq',1);
                    if (isset($tmp[1])) {
                        $where['room'] = array('like', $tmp[1] . '%');
                    }
                    $where['building'] = array('like', '%' . $tmp[0] . '%');

                } else {
                    $where['building'] = array('eq', $tmp[0]);
                    //单元号取“相等”的条件
                    if (isset($tmp[1])) $where['unit'] = array('eq', $tmp[1]);
                    if (isset($tmp[2])) $where['room'] = array('like', $tmp[2] . '%'); //可能没有输入房间号
                }
                $room = $this->getRoomListByWhere($where);
            } elseif (intval($keyword)) { //电话
                $where['tel']=array('like', "%{$keyword}%");
            } else {//姓名
                $where['name'] = array('like', "%{$keyword}%");
            }
        }
        if (empty($where)) {
            $this->error('错误：请设定查询条件！');
        }
        if (!isset($room)) {
            $room = $this->searchPersonByWhere($where);
        }

        $this->assign('room', $room);
        $this->display();
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

        $keyword = (isset($_GET['keyword']) && !empty($_GET['keyword'])) ?
            htmlspecialchars(strip_tags($_GET['keyword'])) : '';
        //初始化
        $where = array();
        if ($keyword) {
            if (intval($keyword)) { //电话
                $where['tel'] = array('like', '%' . $keyword . '%');
            } elseif (mb_strpos($keyword, '-') !== false) { //房间
                $where['room'] = array('like', '%' . $keyword . '%');
            } else { // 姓名
                $where['name'] = array('like', '%' . $keyword .'%');
            }
        }

        $count = $this->_db->getQuitOrMoveNumber('quit', $where);
        $page = new Page($count, $pageLimit);
        $data = $this->_db->getQuitOrMoveData('quit', $where, $page->limit());
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }
    /**
     * 设置页码显示方式，并向模板分配页码变量
     */
    private function setPageForLow()
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

    private function setPageForHigh($start = 1)
    {
        $totalNum = $this->_room->getAllNumber(array('building' => $this->b));
        $totalPageNum = ceil($totalNum / $this->onePageNum);
        $pageStr = '';
        // $start = 1;
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

    /**
     * 续签
     */
    public function renew()
    {
        if (IS_AJAX) {
            //验证id是否存在
            $id = intval($_POST['id']);
            if (!$id)
                $this->ajax(array('status' => 0, 'message' => '错误：ID不存在！'));

            //查找当前要续签的人员
            $person = $this->_db->getDataByWhere(array('id'=>$id), true);
            if (!$person) {
                $this->ajax(array('status' => 0, 'message' => '错误：没有这个人的信息！'));
            }

            //处理post数据
            $data = $this->dealData($_POST);
            //组合需要添加到续签表的数据
            $renewPerson = array_merge($person, $data);
            $renewPerson = $this->formatRenewData($renewPerson);

            //开启事务
            $this->_db->beginTrans();

            //将数据添加进续签表
            if ($this->_db->addDataToRenew($renewPerson)) {
                $arr = array(
                    'id'=>$id,
                    'contract_s'=>$data['contract_s_new'],
                    'contract_e'=>$data['contract_e_new'],
                    'rent_e'=>$data['rent_e_new']
                );
                if ($this->_db->editData($arr)) {
                    $this->_db->commit();
                    $this->ajax(array('status' => 1, 'message' => '操作成功！'));
                    exit;
                }
            }
            $this->_db->rollback();
            $this->ajax(array('status' => 0, 'message' => '错误：数据添加失败！'));
        } else {
            halt('错误：非法请求！');
        }
    }

    public function renewInfo()
    {
        $data = $this->_db->getRenewData();
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 组合出要向续签表中添加的数据格式
     * @param $renewPerson
     */
    private function formatRenewData($renewPerson)
    {
        $personRoom = $this->_room->getRoomByWhere(array('room_id'=>$renewPerson['room_id']), true);
        if (!$personRoom) {
            $this->ajax(array('status' => 0, 'message' => '错误：这个人没有所属房间！'));
        }
        $renewPerson['rid'] = $renewPerson['id'];
        unset($renewPerson['id']);
        $renewPerson['renew_time'] = time();
        if (mb_strpos($personRoom['building'], '高') === 0) {
            $renewPerson['room'] = $personRoom['building'] . '-' . $personRoom['room'];
        } else {
            $renewPerson['room'] = $personRoom['building'] . '-' . $personRoom['unit'] .'-' . $personRoom['room'];
        }
        return $renewPerson;
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
            if (mb_strpos($v['building'], '高') === 0) {
                $room[$k]['isHigh'] = '1';
            } else {
                $room[$k]['isHigh'] = '0';
            }
            //只有一个人 即第0个
            $room[$k]['person'] = isset($data[$room[$k]['room_id']][0]) ? $data[$room[$k]['room_id']][0] : '';
            if (!empty($room[$k]['person'])) {
                $realCount++;
            }
        }
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
     * 格式化数据，尤其要处理合同数据（转成UNIX时间戳）
     * @param $data
     * @return array
     */
    protected function dealData($data)
    {
        $return = array();
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'contract_s':
                case 'rent_s':
                case 'rent_e':
                case 'contract_s_new':
                case 'rent_e_new':
                    $return[$key] = strtotime($value);
                    if (!$return[$key]) {
                        $this->error('错误：日期格式错误！');
                    }
                    break;
                case 'contract_e':
                case 'contract_e_new':
                    if ($value == '无固定期') {
                        $return[$key] = 0;
                    } else {
                        $return[$key] = strtotime($value);
                        if (!$return[$key]) {
                            $this->error('错误：日期格式错误！');
                        }
                    }
                    break;
                default:
                    $return[$key] = strip_tags(htmlspecialchars($value));
            }
        }
        return $return;
    }
}