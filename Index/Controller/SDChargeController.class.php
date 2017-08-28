<?php

class SDChargeController extends AuthController
{
    private $_db;

    private $_base;

    /**
     * 费用精度，小数点后保留几位，最多支持2位
     * @var
     */
    private $precision;

    public function __init()
    {
        $this->precision = C('CHARGE_PRECISION');
        $this->_db = K('SDCharge');
        $this->_base = K('SDBase');
    }

    public function index()
    {
        //只显示20条
        $limit = 20;
        $data = $this->_db->getDataByWhere(null, $limit);
        $allRoom = $this->getAllRoom();
        foreach ($data as $k=>$d) {
            $data[$k]['room'] = $allRoom[$d['room_id']]['room'];
        }

        //分配已缴费和未交费的合计数目
        $this->assignChargeSum();
        $this->assignAllMonth();
        $this->assignAllYear();
        $this->assign('data', $data);
        $this->display();
    }




    public function search()
    {
        if (IS_POST) {
            go(U('search', $_POST));
        }
        //每页显示20条记录
        $pageNumber = 20;
        $year = empty($_GET['year']) ? 0 : (int) $_GET['year'];
        $month = empty($_GET['month']) ? 0 : (int) $_GET['month'];
        //是否缴费 0|未交费 1|已缴费 2|全部
        $isCharged = (int)$_GET['isCharged'];
        $keyword = empty($_GET['keyword']) ? '' : htmlspecialchars(strip_tags($_GET['keyword']));

        $where = array();
        if ($year) {
            $where['start_year'] = array('elt', $year);
            $where['end_year'] = array('egt', $year);
        }
        if ($month) {
            $where['start_month'] = array('elt', $month);
            $where['end_month'] = array('egt', $month);
        }
        //选择了已缴费或者未缴费
        if ($isCharged === 1 || $isCharged === 0) {
            $where[] = 'is_charged=' . $isCharged;
        }

        //搜索房间的条件
        $map['room_type'] = 2; //职工

        //只搜索房间
        if ($keyword) {
            $tmp = explode('-', $keyword);
            //在房间查找，区分高层房间和多层房间
            if (mb_strpos($tmp[0], '高') === 0 || (isset($tmp[1]) && mb_strlen($tmp[1]) > 1)) {
                $map['building'] = array('like', '%' . $tmp[0] . '%');
                $map['unit'] = array('eq',1);
                if (isset($tmp[1])) {
                    $map['room'] = array('like', '%' . $tmp[1] . '%');
                }
            } else {
                $map['building'] = array('like', '%' . $tmp[0]);
                //单元号取“相等”的条件
                if (isset($tmp[1])) $map['unit'] = array('eq', $tmp[1]);
                if (isset($tmp[2])) $map['room'] = array('like', '%' . $tmp[2] . '%'); //可能没有输入房间号
            }
            $map['room_type'] = array('eq', 2);
        }

        $tmpRoom = K('Room')->getRoomByWhere($map);
        //没有找到合适的房间，本次查询失败
        if (!$tmpRoom) {
            $this->assign('data', '');
            $this->display();
            exit;
        }
        foreach ($tmpRoom as $r) {
            $room[$r['room_id']] = $r;
        }
        $roomIds = array_keys($room);
        if ($keyword) {
            $where['room_id'] = array('in', $roomIds);
        }
        if (empty($where)) {
            $this->error('错误：请设定查询条件！');
        }

        $total = $this->_db->getNumberByWhere($where);
        $page = new Page($total, $pageNumber);
        $data = $this->_db->getDataByWhere($where, $page->limit());
        foreach ($data as $k=>$v) {
            $currentRoom = $room[$v['room_id']];
            //高层
            if (mb_strpos($currentRoom['building'], '高') === 0) {
                $data[$k]['room'] = $currentRoom['building'].'-'.$currentRoom['room'];
            } else {
                $data[$k]['room'] = $currentRoom['building'].'-'.
                    $currentRoom['unit'].'-'.$currentRoom['room'];
            }
        }
        //分配已缴费和未交费的合计数目
        $this->assignChargeSum($where);
        $this->assignAllYear();
        $this->assignAllMonth();
        $this->assign('page', $page->show());
        $this->assign('data', $data);
        $this->display();
    }



    public function create()
    {
        if (IS_POST) {
            $startYear = (int)$_POST['startYear'];
            $endYear = (int)$_POST['endYear'];
            $startMonth = (int)$_POST['startMonth'];
            $endMonth = (int)$_POST['endMonth'];
            //验证输入
            if (!$startYear || !$endYear || !$startMonth || !$endMonth) {
                exit('<span style="color:red">错误：开始年月度和结束年月度必须填写！</span>');
            }
            if ($startMonth < 1 || $startMonth > 12 || $endMonth < 1 || $endMonth > 12) {
                exit('<span style="color:red">错误：请填写一个正确的月份！</span>');
            }
            if (($startMonth + 100*$startYear) > ($endMonth +100 * $endYear)) {
                exit('<span style="color:red">错误：开始年月度必须小于结束年月度！</span>');
            }

            //若开始年月度是2015-2，结束年月度是2015-3，则将使用2015-1和2015-3的电表底数，生成2015-2月—2015-3月水电
            //因此开始月度要“减一”，注意开始月度等于1的情况

            //要使用的水电表底数的开始年度和月度
            if ($startMonth == 1) {
                $baseStartMonth = 12;
                $baseStartYear = $startYear - 1;
            } else {
                $baseStartMonth = $startMonth - 1;
                $baseStartYear = $startYear;
            }

            //要使用的水电表底数的结束年度和月度
            $baseEndYear = $endYear;
            $baseEndMonth = $endMonth;


            $startBase = $this->getStartBase(array('year'=>$baseStartYear, 'month'=>$baseStartMonth));
            if (!$startBase) {
                exit('<span style="color:red">错误：不存在'.$baseStartYear. '-' .$baseStartMonth.'的底数！</span>');
            }
            $endBase = $this->_base->getDataByWhere(array('year'=>$baseEndYear, 'month'=>$baseEndMonth));
            if (!$endBase) {
                exit('<span style="color:red">错误：不存在'.$baseEndYear. '-' .$baseEndMonth.'的底数！</span>');
            }

            //TODO 假设已生成 3-5月的，现在要生成4-6月的，明显不符合常理，需检测
            //怎么检测？？？？！！！！！！！！！

            $waterPrice = C('WATER_PRICE');
            $electricPrice = C('ELECTRIC_PRICE');
            //已经生成的房间
            $isCreatedMap = array(
                'start_year'=>$startYear,
                'start_month'=>$startMonth,
                'end_year'=>$endYear,
                'end_month'=>$endMonth
            );
            $isCreatedRoom = $this->_db->getOneField('room_id', $isCreatedMap);

            $this->_db->beginTrans();
            foreach ($endBase as $e) {
                $roomId = $e['room_id'];
                //开始年月度里不存在
                if (!isset($startBase[$roomId])) {
                    continue;
                }
                //已经生成了
                if (in_array($roomId, $isCreatedRoom)) {
                    continue;
                }
                $data = array();
                $data['room_id'] = $roomId;
                $data['water']=(int)($e['water_base'] - $startBase[$roomId]['water_base']);
                $data['electric']=(int)($e['electric_base'] - $startBase[$roomId]['electric_base']);
                $data['water_money'] = round(($e['water_base'] - $startBase[$roomId]['water_base'])*$waterPrice, $this->precision);
                $data['electric_money'] = round(($e['electric_base'] - $startBase[$roomId]['electric_base'])*$electricPrice, $this->precision);
                $data['start_year'] = $startYear;
                $data['start_month'] = $startMonth;
                $data['end_year'] = $endYear;
                $data['end_month'] = $endMonth;
                $data['create_time'] = time();
                $data['is_charged'] = 0;
                if (!$this->_db->addData($data)) {
                    $this->rollback();
                    exit('<span style="color:red">错误：添加数据时发生错误！</span>');
                }
            }
            $this->_db->commit();
            exit('本次生成成功！');
        } else {
            $this->display();
        }
    }

    public function charge()
    {
        if (IS_AJAX) {
            $allId = explode(',', $_POST['allId']);
            if (!$allId) {
                $this->error('错误：参数错误！');
            }
            //查找已经交过费的房间
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
            $isChargedWhere['id'] = array('in', $map);
            $isChargedWhere['is_charged'] = 1;

            $isChargedRoom = $this->_db->getOneField('id', $isChargedWhere);

            //计算两个数组的差集
            $diff = array_diff($map, $isChargedRoom);
            if (empty($diff)) {
                $this->success('所选房间已经缴费！');
                exit;
            }
            $updateMap['id'] = array('in', $diff);

            //要修改的数据
            $data = array(
                'is_charged'=>1,
                'charge_time'=>time()
            );

            /*
             * 若$updateMap传递错误，将会发生将全部记录都改写的事情，
             * 太危险了！！！！！
             */
            if ($this->_db->updateData($data, $updateMap)) {
                $this->success('操作成功！');
            } else {
                $this->error('错误：更改数据时发生错误！');
            }
        } else {
            halt('错误：参数错误！');
        }
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
     * 分配单身住户水电费所有的年度
     */
    private function assignAllYear()
    {
        $startYear = $this->_db->getOneField('distinct(start_year)');
        $endYear = $this->_db->getOneField('distinct(end_year)');
        $year = array_unique(array_merge($startYear, $endYear));
        $this->assign('year', $year);
    }

    /**
     * 分配单身住户水电费所有的月度
     */
    private function assignAllMonth()
    {
        $startMonth = $this->_db->getOneField('distinct(start_month)');
        $endMonth = $this->_db->getOneField('distinct(end_month)');
        $month = array_unique(array_merge($startMonth, $endMonth));
        $this->assign('month', $month);
    }

    /**
     * 获取开始年月度所有的水电表底数
     * 返回的数组类似于：
     *          array(
     *              '256'(room_id)=>array(
     *                       'base_detail'
     *                  )
     *              )
     * @return array
     */
    private function getStartBase($where)
    {
        $startBase = array();
        $tmpStart = $this->_base->getDataByWhere($where);
        if ($tmpStart) {
            foreach ($tmpStart as $v) {
                $startBase[$v['room_id']] = $v;
            }
        }
        return $startBase;
    }

    /**
     * 获取所有需要交费的房间信息
     * @return array
     */
    private function getAllRoom()
    {
        //所有需要缴费的信息
        $tmpRoom = K('Room')->getRoomByWhere(array('room_type'=>2));
        //要使用的房间数组
        $allRoom = array();
        foreach ($tmpRoom as $r) {
            if (mb_strpos($r['building'], '高') === 0) {//高层
                $allRoom[$r['building'] . '-' . $r['room']] =$r['room_id'];
                $allRoom[$r['room_id']] = array(
                    'room'=>$r['building'] . '-' . $r['room'],
                    'room_id'=>$r['room_id']
                );
            } else {//多层
                $allRoom[$r['building'] . '-' . $r['unit'] . '-' . $r['room']] =$r['room_id'];
                $allRoom[$r['room_id']] = array(
                    'room'=>$r['building'] . '-' . $r['unit'] . '-' . $r['room'],
                    'room_id'=>$r['room_id']
                );
            }
        }
        return $allRoom;
    }


    /**
     * 分配已缴费和未缴费的金额合计
     * @param $data
     */
    private function assignChargeSum($where = null)
    {
        //已缴费费用总计
        $isChargedSum = $this->_db->getWaterAndElectricSum($where, 1);
        //未交费费用总计
        $noChargedSum = $this->_db->getWaterAndElectricSum($where, 0);
        //已缴费总计
        $isChargedSum['total'] = $isChargedSum['water'] + $isChargedSum['electric'];
        //未交费总计
        $noChargedSum['total'] = $noChargedSum['water'] + $noChargedSum['electric'];

        $this->assign('isChargedSum', $isChargedSum);
        $this->assign('noChargedSum', $noChargedSum);
    }

}