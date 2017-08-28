<?php
import('HDPHP.Extend.Org.PhpExcel.HdExcel');// 导入类库

class DispatchController extends SingleController
{
    /**
     * 构造函数
     */
    public function __init()
    {
        $this->_db = K('Dispatch');
        $this->_room = K('Room');
        $this->peopleNumberPerRoomForSixFloor = 10;
        $this->peopleNumberPerRoom = 8;
        //分页，对于多层来说就是单元号
        $this->page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        //楼号
        $this->b = isset($_GET['b']) ? Q('get.b') : 13;
    }

    /**
     * 显示主页
     */
    public function index()
    {
        //在大学生房间查找:'type'=>3
        $where = array('building' => $this->b, 'unit' => $this->page, 'room_type' => 3);
        $room = $this->getRoomListByWhere($where);
        $this->assign('room', $room);
        $this->setPage();
        $this->display();
    }

    public function setPage()
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

    /**
     * 从文件导入
     */
    public function import()
    {
        if (IS_POST) {
            //文件为空
            if (empty($_FILES['file']['name'])) {
                $str = '<span style=\"color:red\">错误：请上传一个文件！</span>';
                echo "<script>window.parent.callback('". $str ."')</script>";
                exit;
            }

            $pathInfo = pathinfo($_FILES['file']['name']);
            //文件格式错误
            if ($pathInfo['extension'] != 'xls') {
                $str = '<span style=\"color:red\">错误：请上传一个EXCEL97-2003 ( .xls )文件！</span>';
                echo "<script>window.parent.callback('". $str ."')</script>";
                exit;
            }

            //所有的派遣工房间
            $allRoom = $this->getDispatchRoom();
            $phpExcel = new HdExcel();
            //需要从原表中移动到隐藏表的房间id
            $needToMoveRoomIds = array();
            if (is_file($_FILES['file']['tmp_name'])) {
                $tmpData = $phpExcel->readExcel($_FILES['file']['tmp_name']);
                //开启事务
                $this->_db->beginTrans();
                foreach ($tmpData as $k=>$v) {
                    //第一行是标题，抛弃之
                    if ($k == 0) {
                        continue;
                    }
                    //姓名不存在则跳过
                    $name = isset($v[1]) ? htmlspecialchars(strip_tags($v[1])) : '';
                    if (!$name) {
                        continue;
                    }
                    $currentRoom = isset($v[0]) ? htmlspecialchars(strip_tags($v[0])) : '';
                    if ($currentRoom && isset($allRoom[$currentRoom])) {
                        $currentRoomId = $allRoom[$currentRoom]['room_id'];
                        //组合数据
                        $data = array(
                            'room_id'=>$currentRoomId,
                            'name'=>$name,
                            'sex'=>$v[2] == '男' ? 1 : ($v[2] == '女' ? 2 : 0) ,
                            'bed_number'=>isset($v[3]) ? (int)$v[3] : 0,
                            'department'=>isset($v[4]) ? htmlspecialchars(strip_tags($v[4])) : '',
                            'entrancetime'=>isset($v[5]) ? htmlspecialchars(strip_tags($v[5])) : '',
                            'tel'=>isset($v[6]) ? htmlspecialchars(strip_tags($v[6])) : 0,
                            'remark'=>isset($v[7]) ? htmlspecialchars(strip_tags($v[7])) : ''
                        );
                        //这个房间还没有将原有人员移动到隐藏表
                        if (!in_array($currentRoomId, $needToMoveRoomIds)) {
                            if (!$this->_db->moveToInvisibleTable($currentRoomId)) {
                                $this->_db->rollback();
                                $str = '<strong style="color:red">错误：移动数据时发生错误！</strong><br/>';
                                exit("<script>window.parent.callback('".$str."')</script>");
                            }
                            //将id添加进需移动的数组中，以便下次查找
                            array_push($needToMoveRoomIds, $currentRoomId);
                        }
                        //添加数据
                        if (!$this->_db->addData($data)) {
                            $this->_db->rollback();
                            $str = '<strong style="color:red">错误：添加数据时发生错误！</strong><br/>';
                            exit("<script>window.parent.callback('".$str."')</script>");
                        }
                    }

                }
                $this->_db->commit();
                $str = '<strong style="color:red">本次添加成功！</strong><br/>';
                exit("<script>window.parent.callback('".$str."')</script>");
            } else {
                $str = '<strong style="color:red">错误：临时文件不存在！</strong><br/>';
                exit("<script>window.parent.callback('".$str."')</script>");
            }
        } else {
            $this->display();
        }
    }

    /**
     * 取得所有的派遣工房间
     * @return array
     */
    private function getDispatchRoom()
    {
        $tmpRoom = $this->_room->getRoomByWhere(array('room_type'=>3));
        $allRoom = array();
        foreach ($tmpRoom as $v) {
            $allRoom[$v['building'].'-'.$v['unit'].'-'.$v['room']] = $v;
        }
        return $allRoom;
    }
}