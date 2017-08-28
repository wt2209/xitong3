<?php
import('HDPHP.Extend.Org.PhpExcel.HdExcel');// 导入类库
class SDBaseController extends AuthController
{
    private $_db;

    public function __init()
    {
        $this->_db = K('SDBase');
    }
    public function index()
    {
        //只显示最新20条记录
        $limit = '20';
        $this->assign('year', $this->getDistinctField('year'));
        $this->assign('month', $this->getDistinctField('month'));
        //获取所有需要的房间
        $allRoom = $this->getNeededRoom(array('room_type'=>2));
        $data = $this->_db->getDataByWhere(null, $limit);
        foreach ($data as $k=>$d) {
            $data[$k]['room'] = $allRoom[$d['room_id']]['room'];
        }
        $this->assign('data', $data);
        $this->display('index');
    }

    public function search()
    {
        if (IS_POST) {
            go(U('search', $_POST));
        }
        //每页的记录条数
        $pageShowNumber = 20;

        $year = (int)$_GET['year'];
        $month = (int)$_GET['month'];
        $startTime = isset($_GET['startTime']) ? strtotime($_GET['startTime']) : 0;
        $endTime = isset($_GET['endTime']) ? strtotime($_GET['endTime']) : 0;
        $keyword = isset($_GET['keyword']) ? htmlspecialchars(strip_tags($_GET['keyword'])) : '';

        $where = array();
        if ($startTime) {
            $where[] = 'read_time>='.$startTime;
        }
        if ($endTime) {
            $where[] = 'read_time<='.$endTime;
        }
        if ($year) {
            $where[] = 'year='.$year;
        }
        if ($month) {
            $where[] = 'month='.$month;
        }


        //只支持房间查询
        if ($keyword) {
            $map['room_type'] = 2; //单身职工
            $tmp = explode('-', $keyword);
            //在房间查找
            //只在高层房间查找
            $map['building'] = array('like', '%' . $tmp[0]);
            if (isset($tmp[1])) {
                $map['room'] = array('eq', $tmp[1]);
            }
            $allRoom = $this->getNeededRoom($map);
            $uniqueId = array();
            foreach ($allRoom as $v) {
                if (!is_array($v)) {
                    $uniqueId[] = intval($v);
                }
            }
            $where['room_id'] =array('in', $uniqueId);
        } else {
            $allRoom = $this->getNeededRoom(array('room_type'=>2));
        }
        //查询条件为空，则转到主页
        if (empty($where)) {
            $this->index();
            exit;
        }

        $count = $this->_db->getNumberByWhere($where);
        $page = new Page($count, $pageShowNumber);
        $data = $this->_db->getDataByWhere($where, $page->limit());
        foreach ($data as $k=>$d) {
            $data[$k]['room'] = $allRoom[$d['room_id']]['room'];
        }
        //分配模板数据
        $this->assign('year', $this->getDistinctField('year'));
        $this->assign('month', $this->getDistinctField('month'));
        $this->assign('count', $count);
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 批量添加水电表底数
     */
    public function add()
    {
        if (IS_POST) {
            $year = (int) (isset($_POST['year']) ? $_POST['year'] : 0);
            $month = (int) (isset($_POST['month']) ? $_POST['month'] : 0);
            if (!$year || !$month) {
                $str = '<strong style="color:red">本次添加失败！</strong><br/>';
                $str .= '失败原因：年度和月度必须填写。<br/>';
                $str .= '<br/><a href="'.U('add').'" class="btn btn-info">继续添加</a>';
                echo $str;
                exit;
            }
            unset($_POST['year']);
            unset($_POST['month']);

            if (isset($_POST['totalName']) && !empty($_POST['totalName'])) {
                $totalName = htmlspecialchars(strip_tags($_POST['totalName']));
                unset($_POST['totalName']);
            } else {
                $totalName = '';
            }
            if (isset($_POST['totalReadTime']) && !empty($_POST['totalReadTime'])) {
                $totalReadTime = strtotime($_POST['totalReadTime']);
                unset($_POST['totalReadTime']);
            } else {
                $totalReadTime = time();
            }

            $option = array(
                'year'=>$year,
                'month'=>$month,
                'totalName'=>$totalName,
                'totalReadTime'=>$totalReadTime
            );
            //批量添加数据
            $ret = $this->batchAdd($_POST, $option);

            if ($ret['status'] == 1) {
                $str = '<strong style="color:red">本次添加成功！</strong><br/>';
                $str .= '<br/><a href="'.U('add').'" class="btn btn-info">继续添加</a><br />';
                $str .= '以下是详细信息：<br />';
                $str .= $ret['str'];
            } else {
                $str = '<strong style="color:red">本次添加失败！</strong><br/>';
                $str .= '失败原因：数据库写入错误。<br/>';
                $str .= '<br/><a href="'.U('add').'" class="btn btn-info">继续添加</a>';
            }
            echo $str;
            exit;
        } else {
            $this->display();
        }
    }


    /**
     * 导入文件
     */
    public function import()
    {
        if (IS_POST) {
            //检测是否填写年度和月度
            $year = (int) (isset($_POST['year']) ? $_POST['year'] : 0);
            $month = (int) (isset($_POST['month']) ? $_POST['month'] : 0);
            if (!$year || !$month) {
                $str = '<span style=\"color:red\">错误：请填写年度和月度！</span>';
                echo "<script>window.parent.callback('".$str."')</script>";
                exit;
            }

            if (isset($_POST['totalName']) && !empty($_POST['totalName'])) {
                $totalName = htmlspecialchars(strip_tags($_POST['totalName']));
            } else {
                $totalName = '';
            }
            if (isset($_POST['totalReadTime']) && !empty($_POST['totalReadTime'])) {
                $totalReadTime = strtotime($_POST['totalReadTime']);
            } else {
                $totalReadTime = time();
            }


            if (empty($_FILES['file']['name'])) {
                $str = '<span style=\"color:red\">错误：请上传一个文件！</span>';
                echo "<script>window.parent.callback('". $str ."')</script>";
                exit;
            }

            $pathInfo = pathinfo($_FILES['file']['name']);
            if ($pathInfo['extension'] != 'xls') {
                $str = '<span style=\"color:red\">错误：请上传一个EXCEL97-2003 ( .xls )文件！</span>';
                echo "<script>window.parent.callback('". $str ."')</script>";
                exit;
            }

            $phpExcel = new HdExcel();
            if (is_file($_FILES['file']['tmp_name'])) {
                $tmpData = $phpExcel->readExcel($_FILES['file']['tmp_name']);

                $data = array();
                foreach ($tmpData as $k=>$v) {
                    //第一行是标题，抛弃之
                    if ($k == 0) {
                        continue;
                    }
                    //只取前6列
                    $data[] = array(
                        'room'=>isset($v[0]) ? htmlspecialchars(strip_tags($v[0])) : '',
                        'electric_base'=>isset($v[1]) ? (int)$v[1] : 0,
                        'water_base'=>isset($v[2]) ? (int)$v[2] : 0,
                        'name'=>isset($v[3]) ? htmlspecialchars(strip_tags($v[3])) : '',
                        'read_time'=>isset($v[4]) ? strtotime(str_replace('.', '-', $v[4])) : 0,
                        'remark'=>isset($v[5]) ? htmlspecialchars(strip_tags($v[5])) : ''
                    );
                }

                $option = array(
                    'year'=>$year,
                    'month'=>$month,
                    'totalName'=>$totalName,
                    'totalReadTime'=>$totalReadTime
                );
                $ret = $this->batchAdd($data, $option);

                if ($ret['status'] == 1) {
                    $str = '<strong style="color:red">本次添加成功！</strong><br/>';
                    $str .= '以下是详细信息：<br />';
                    $str .= $ret['str'];
                } else {
                    $str = '<strong style="color:red">本次添加失败！</strong><br/>';
                    $str .= '失败原因：数据库写入错误。<br/>';
                }

                echo "<script>window.parent.callback('".$str."')</script>";
            } else {
                $str = '<strong style="color:red">错误：临时文件不存在！</strong><br/>';
                echo "<script>window.parent.callback('".$str."')</script>";
                exit;
            }
        } else {
            $this->display();
        }
    }

    public function edit()
    {

    }

    /**
     * 删除
     */
    public function del()
    {
        if (IS_AJAX) {
            //为了安全起见，只允许最多一次性删除10个
            $delLimit = 10;
            $allId = explode(',', $_POST['allId']);
            if (!$allId) {
                $this->error('错误：参数错误！');
            }
            $map = array();
            foreach ($allId as $id) {
                $intId = intval($id);
                if ($intId) {
                    $map[] = intval($id);
                }
            }
            if (empty($map)) {
                $this->error('错误：请至少选择一项数据！');
            }

            //只允许删除10个
            $where['id'] = array('in', array_slice($map, 0, $delLimit));
            if ($this->_db->delData($where)) {
                $this->success('操作成功！');
            } else {
                $this->error('错误：删除失败！');
            }
        } else {
            halt('错误：参数错误！');
        }
    }

    /**
     * 循环添加数据
     * @param $insertData
     * @param $option
     * @return string
     */
    private function batchAdd($insertData, $option)
    {
        //获取需要录入信息的房间，以便检测提交的数据的正确性
        $allRoom = $this->getNeededRoom(array('room_type'=>2));

        //检测是否已录入
        $where['month'] = $option['month'];
        $where['year'] = $option['year'];
        $hasInserted = $this->getHasInsertedRoom($where);
        $str = '';
        $this->_db->beginTrans();
        foreach ($insertData as $v) {
            if (empty($v['room'])) {
                continue;
            }
            $rArr = explode('-', $v['room']);
            if (count($rArr) < 2) {
                $str .= $v['room'].' 不是一个合法的房间！<br />';
                continue;
            }
            if (!isset($allRoom[$v['room']])) {
                $str .= $v['room'].' 不存在！<br />';
                continue;
            }
            $data = array(
                'room_id'=>$allRoom[$v['room']],
                'water_base'=>(int) $v['water_base'],
                'electric_base'=>(int) $v['electric_base'],
                'name'=>empty($v['name']) ? $option['totalName'] : htmlspecialchars(strip_tags($v['name'])),
                'month'=>$option['month'],
                'year'=>$option['year'],
                'remark'=>htmlspecialchars(strip_tags($v['remark'])),
                'read_time'=>empty($v['read_time']) ? $option['totalReadTime'] : $v['read_time'],
            );

            if (isset($hasInserted[$data['room_id']])) {
                $str .= $v['room'].' 已经录入<br />';
                continue;
            }
            if (!$this->_db->addBaseData($data)) {
                $this->_db->rollback();
                return array('status'=>0);
            } else {
                $str .= $v['room'] . ' 录入成功！<br />';
            }
        }
        $this->_db->commit();
        return array('status'=>1, 'str'=>$str);
    }

    /**
     * 获取需要使用的房间数组
     * @param $where
     * @return array
     */
    private function getNeededRoom($where)
    {
        //所有需要缴费的信息
        $tmpRoom = K('Room')->getRoomByWhere($where);
        //要使用的房间数组
        $allRoom = array();
        foreach ($tmpRoom as $r) {
            if (mb_strpos($r['building'], '高') === 0) {//高层
                $allRoom[$r['building'] . '-' . $r['room']] = $r['room_id'];
                $allRoom[$r['room_id']] = array(
                    'room'=>$r['building'] . '-' . $r['room'],
                    'room_id'=>$r['room_id']
                );
            } else {//多层
                $allRoom[$r['building'] . '-' . $r['unit'] . '-' . $r['room']] = $r['room_id'];
                $allRoom[$r['room_id']] = array(
                    'room'=>$r['building'] . '-' . $r['unit'] . '-' . $r['room'],
                    'room_id'=>$r['room_id']
                );
            }
        }
        return $allRoom;
    }

    /**
     * 获取已经录入的房间信息
     * @param $where
     */
    private function getHasInsertedRoom($where)
    {
        $tmpInsert = $this->_db->getDataByWhere($where);
        $hasInserted = array();
        if ($tmpInsert) {
            foreach ($tmpInsert as $v) {
                $hasInserted[$v['room_id']] = 1;
            }
        }
        return $hasInserted;
    }

    /**
     * 获取某字段，并去重
     * @param $field
     * @return mixed
     */
    private function getDistinctField($field)
    {
        return $this->_db->getDistinctField($field);
    }
}