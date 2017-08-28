<?php
import('HDPHP.Extend.Org.PhpExcel.HdExcel');// 导入类库


class ExportController extends AuthController
{
    private $phpExcel;

    public function __init()
    {
        $this->phpExcel = new HdExcel();
    }

    /**
     * 人员信息首页
     */
    public function index()
    {
        $this->display();
            /*p($_FILES);
            $phpExcel = new HdExcel();
            $data = $phpExcel->readExcel($_FILES['file']['tmp_name']);
            header('Content-Type:text/html;charset=utf-8');
            p($data);
            die;

            $this->display();*/
            /*$phpExcel = new HdExcel();
            $data = $phpExcel->readExcel('C:\Users\WT\Desktop\节点表.xls');
            p($data);*/

    }

    /**
     * 费用报表首页
     */
    public function chargeIndex()
    {
        //单身水电费的所有类型
//        $this->assignSingleSDType();
        $this->assignAllYear();
        $this->assignAllMonth();

        //单身床位费的所有类型
        $this->assignSingleRentType();
        //租赁的所有费用类型
        $this->assignAllRentType();
        //租赁的所有季度
        $this->assignAllQuarters();

        $this->display();
    }

    /**
     * 生成大学生入住人员报表
     */
    public function college()
    {
        //大学生的类型是1
        $room = $this->getRoom(1);
        $persons = $this->getPerson('College', $room);
        $data = $this->singleFormatData($persons, $room);
        $this->phpExcel->downloadExcel($data, '大学生信息表'.date('Ymd', time()));
    }

    /**
     * 生成职工入住人员报表
     */
    public function worker()
    {
        //职工的类型是2
        $room = $this->getRoom(2);
        $persons = $this->getPerson('Worker', $room);
        $data = $this->singleFormatData($persons, $room);
        $this->phpExcel->downloadExcel($data, '职工信息表'.date('Ymd', time()));
    }


    /**
     * 生成租赁户入住人员报表
     */
    public function rent()
    {
        //租赁的类型是4
        $room = $this->getRoom(4);
        $persons = $this->getPerson('Rent', $room);
        $data = $this->rentFormatData($persons, $room);
        $this->phpExcel->downloadExcel($data, '租赁人员信息表'.date('Ymd', time()));
    }

    public function singleRentCharge()
    {
        //每页的记录条数
        $startTime = isset($_GET['startTime']) ? strtotime($_GET['startTime']) : 0;
        $endTime = isset($_GET['endTime']) ? strtotime($_GET['endTime']) : 0;
        $chargeType = isset($_GET['chargeType']) ? intval($_GET['chargeType']) : 0;
        //keyword默认为"_"，防止keyword为空时出错
        $keyword = isset($_GET['keyword']) ? htmlspecialchars(strip_tags($_GET['keyword'])) : '_';

        $where = array();
        if ($startTime) {
            $where[] = 'charge_time>='.$startTime;
        }
        if ($endTime) {
            $where[] = 'charge_time<='.$endTime;
        }
        if ($chargeType) {
            $where[] = 'charge_type='.$chargeType;
        }

        if ($keyword) {
            //房间号
            if (mb_strpos($keyword, '-') !== false) {

                $where['room_type'] = 2; //租赁
                $tmp = explode('-', $keyword);
                //在房间查找，区分高层房间和多层房间
                // if (mb_strpos($tmp[0], '高') === 0 || (isset($tmp[1]) && mb_strlen($tmp[1]) > 1)) {
                    $where['building'] = array('like', '%' . $tmp[0] . '%');
                    $where['unit'] = array('eq',1);
                    if (isset($tmp[1])) {
                        $where['room'] = array('like', '%' . $tmp[1] . '%');
                    }
                // } else {
                    // $where['building'] = array('like', '%' . $tmp[0]);
                    //单元号取“相等”的条件
                    // if (isset($tmp[1])) $where['unit'] = array('eq', $tmp[1]);
                    // if (isset($tmp[2])) $where['room'] = array('like', '%' . $tmp[2] . '%'); //可能没有输入房间号
                // }
                $room = $this->getRoomListByWhere($where);
            } elseif (intval($keyword)) { //电话
                $where['tel']=array('like', "%{$keyword}%");
            } else {//姓名
                $where['name'] = array('like', "%{$keyword}%");
            }
        }

        //查询条件为空，则转到主页
        if (empty($where)) {
            $this->error('错误：请输入查询条件！');
            exit;
        }

        $result = K('SingleRentCharge')->getViewData($where);

        $data = array();
        foreach ($result as $r) {
            $arr['房间号'] = $r['building'] . '-' . $r['room'];
            $arr['姓名'] = $r['name'];
            $arr['性别'] = $r['sex'] == 1 ? '男' : '女';
            $arr['部门'] = $r['department'];
            $arr['电话'] = $r['tel'];
            $arr['费用类型'] = $r['charge_type'] == 1 ? '押金' : '租金';
            $arr['金额'] = $r['money'];
            $arr['缴费时间'] = $r['charge_time'] ? hd_date($r['charge_time']) : '';
            $arr['费用说明'] = $r['worker_rent_remark'];
            $data[] = $arr;
        }












$this->phpExcel->downloadExcel($data, '单身床位费费用表'.date('Ymd', time()));
    }

    public function singleSDCharge()
    {
        $year = empty($_POST['year']) ? 0 : (int) $_POST['year'];
        $month = empty($_POST['month']) ? 0 : (int) $_POST['month'];
        //是否缴费 0|未交费 1|已缴费 2|全部
        $isCharged = (int)$_POST['isCharged'];
        $keyword = empty($_POST['keyword']) ? '' : htmlspecialchars(strip_tags($_POST['keyword']));

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
            $this->error('没有找到满足条件的记录！');
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
            exit();
        }

        $result = K('SDCharge')->getDataByWhere($where);
        $data = array();
        foreach ($result as $k=>$v) {
            $currentRoom = $room[$v['room_id']];
            //高层
            if (mb_strpos($currentRoom['building'], '高') === 0) {
                $v['room'] = $currentRoom['building'].'-'.$currentRoom['room'];
            } else {
                $v['room'] = $currentRoom['building'].'-'.
                    $currentRoom['unit'].'-'.$currentRoom['room'];
            }
            $tmp = array();
            $tmp['房间号'] = $v['room'];
            $tmp['用电量(度)'] = $v['electric'];
            $tmp['用水量(吨)'] = $v['water'];
            $tmp['电费(元)'] = $v['electric_money'];
            $tmp['水费(元)'] = $v['water_money'];
            $tmp['合计(元)'] = $v['electric_money'] + $v['water_money'];

            if ($v['start_year'] == $v['end_year'] && $v['start_month'] == $v['end_month']){
                $tmp['月份'] = $v['start_year'] . '-' . $v['start_month'];
            } else {
                $tmp['月份'] = $v['start_year'] . '-' . $v['start_month'] . '—' 
                                . $v['end_year'] . '-' . $v['end_month'];
            }

            $tmp['费用生成时间'] = hd_date($v['create_time']);
            $tmp['是否缴费'] = $v['is_charged'] == 1 ? '是' : '否';
            $tmp['缴费时间'] = $v['charge_time'] ? hd_date($v['charge_time']) : '';
            $tmp['备注'] = $v['remark'];
            $data[] = $tmp;
        }

        $this->phpExcel->downloadExcel($data, '单身水电费费用表'.date('Ymd', time()));



    }

    public function rentCharge()
    {
        $startTime = isset($_POST['startTime']) ? strtotime($_POST['startTime']) : 0;
        $endTime = isset($_POST['endTime']) ? strtotime($_POST['endTime']) : 0;
        $chargeType = isset($_POST['chargeType']) ? intval($_POST['chargeType']) : 0;
        $quarter = isset($_POST['quarter']) ? intval($_POST['quarter']) : 0;
        $keyword = isset($_POST['keyword']) ? htmlspecialchars(strip_tags($_POST['keyword'])) : '';

        $where = array();
        if ($startTime) {
            $where[] = 'charge_time>='.$startTime;
        }
        if ($endTime) {
            $where[] = 'charge_time<='.$endTime;
        }
        if ($chargeType) {
            $where[] = 'charge_type='.$chargeType;
        }
        if ($quarter) {
            $where[] = 'quarter='.$quarter;
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
                    $where['building'] = array('like', $tmp[0] . '%');
                    $where['unit'] = array('eq',1);
                    if (isset($tmp[1])) {
                        $where['room'] = array('like', '%' . $tmp[1] . '%');
                    }
                } else {
                    $where['building'] = array('like', '%' . $tmp[0]);
                    //单元号取“相等”的条件
                    if (isset($tmp[1])) $where['unit'] = array('eq', $tmp[1]);
                    if (isset($tmp[2])) $where['room'] = array('like', '%' . $tmp[2] . '%'); //可能没有输入房间号
                }
                $room = $this->getRoomListByWhere($where);
            } elseif (intval($keyword)) { //电话
                $where['tel']=array('like', "%{$keyword}%");
            } else {//姓名
                $where['name'] = array('like', "%{$keyword}%");
            }
        }

        //查询条件为空，则转到主页
        if (empty($where)) {
            $this->error('错误：请设定查询条件！');
            exit;
        }

        $tmpData = K('RentCharge')->getViewData($where);
        $chargeType = C('RENT_CHARGE_TYPE');
        $data = array();
        foreach ($tmpData as $d) {
            $arr = array();
            if (mb_strpos($d['building'], '高') === 0) {
                $arr['房间号'] = $d['building'] . '-' . $d['room'];
            } else {
                $arr['房间号'] = $d['building'] . '-' . $d['unit'] . '-' . $d['room'];
            }

            $arr['姓名'] = $d['name'];
            $arr['部门'] = $d['department'];
            $arr['电话'] = $d['tel'];
            $arr['季度'] = $d['quarter'] ? $d['quarter'] : '';
            $arr['类型'] = $chargeType[$d['charge_type']]['title'];
            $arr['缴费时间'] = $d['charge_time'] ? hd_date($d['charge_time']) : '';
            $arr['金额'] = $d['money'];
            $arr['费用说明'] = $d['charge_remark'];
            $data[] = $arr;
        }
        $this->phpExcel->downloadExcel($data, '租赁费用表'.date('Ymd', time()));
    }

    /**
     * 向模板分配所有的租赁季度
     */
    private function assignAllQuarters()
    {
        $quarters = S('quarters');
        if (!$quarters) {
            $quarters = K('RentCharge')->updateQuartersCache();
        }
        $this->assign('quarters', $quarters);
    }

    /**
     * 向模板分配所有的租赁费用类型
     */
    private function assignAllRentType()
    {
        $chargeType = C('RENT_CHARGE_TYPE');
        if (!$chargeType) {
            return false;
        }
        $this->assign('rentChargeType', $chargeType);
    }

    /**
     * 分配单身住户水电费所有的年度
     */
    private function assignAllYear()
    {
        $db = K('SDCharge');
        $startYear = $db->getOneField('distinct(start_year)');
        $endYear = $db->getOneField('distinct(end_year)');
        $year = array_unique(array_merge($startYear, $endYear));
        $this->assign('year', $year);
    }

    /**
     * 分配单身住户水电费所有的月度
     */
    private function assignAllMonth()
    {
        $db = K('SDCharge');
        $startMonth = $db->getOneField('distinct(start_month)');
        $endMonth = $db->getOneField('distinct(end_month)');
        $month = array_unique(array_merge($startMonth, $endMonth));
        $this->assign('month', $month);
    }


    /**
     * 向模板分配所有的单身水电费类型
     * 新的收费模型用不到
     */
    private function assignSingleSDType()
    {
        $chargeType = C('SINGLE_SD_TYPE');
        if (!$chargeType) {
            return false;
        }
        $this->assign('singleSDChargeType', $chargeType);
    }

    /**
     * 向模板分配所有的单身床位费类型
     */
    private function assignSingleRentType()
    {
        $chargeType = C('SINGLE_Rent_TYPE');
        if (!$chargeType) {
            return false;
        }
        $this->assign('singleRentChargeType', $chargeType);
    }
    /**
     * 获取满足条件的指定类型的房间
     * @param $roomType
     * @return mixed
     */
    private function getRoom($roomType)
    {
        if (!isset($_POST) && !is_array($_POST['building'])) {
            halt('错误：参数错误！');
        }
        $building = array();
        foreach ($_POST['building'] as $b) {
            $building[] = htmlspecialchars(strip_tags($b));
        }
        if (empty($building)) {
            $this->error('错误：请至少选择一栋楼！');
        }

        $where['building'] = array('in', implode(',', $building));
        $where = array_merge($where, array('room_type'=>$roomType));
        //所有人员
        return K('Room')->getRoomByWhere($where);
    }

    /**
     * 根据条件获得人员信息
     * @param $modelName 模型名，如‘College’
     * @param $room 从数据库查找出的房间
     * @return mixed
     */
    private function getPerson($modelName, $room)
    {
        $in = array();
        foreach ($room as $r) {
            $in[] = $r['room_id'];
        }
        $map['room_id']=array('in', $in);
        $tmp = K($modelName)->getDataByWhere($map);

        foreach ($tmp as $v) {
            $persons[$v['room_id']][] = $v;
        }
        return $persons;
    }

    /**
     * 格式化租赁人员信息，组织出要写入excel的数组
     * @param $persons
     * @param $room
     * @return array|bool
     */
    private function rentFormatData($persons, $room)
    {
        $data = array();
        foreach ($room as $k => $v) {
            //只有一个人 即第0个
            $v['person'] = isset($persons[$v['room_id']][0]) ? $persons[$v['room_id']][0] : '';

            $arr = array();
            //组织数据
            $arr['房间号'] = mb_strpos($v['building'], '高') === 0 ?
                $v['building'] .'-'.$v['room'] :
                $v['building'] .'-'.$v['unit'] .'-'.$v['room'];
            $arr['姓名'] = $v['person']['name'];
            $arr['部门'] = $v['person']['department'];
            $arr['性别'] = $v['person']['sex'] == 1 ? '男' : ($v['person']['sex'] == 2 ? '女' :'');
            $arr['身份证号'] = $v['person']['identify'] ? $v['person']['identify'] : '';
            if (empty($v['person']['rent_s']) && empty($v['person']['rent_e'])) {
                $arr['租赁合同起止日'] = '';
            } else {
                $arr['租赁合同起止日'] = hd_date($v['person']['rent_s']).'—'.
                    hd_date($v['person']['rent_e']);
            }

            if (empty($v['person']['contract_s']) && empty($v['person']['contract_e'])) {
                $arr['劳动合同起止日'] = '';
            } else {
                $arr['劳动合同起止日'] = hd_date($v['person']['contract_s']).'—'.
                    (empty($v['person']['contract_e']) ? '无固定期' : hd_date($v['person']['contract_e']));
            }


            $arr['电话'] = $v['person']['tel'] ? $v['person']['tel'] : '';
            $arr['备注'] = $v['person']['remark'];
            $data[] = $arr;

        }
        return $data;
    }

    /**
     * 根据房间号进行升序排序
     * @param $a
     * @param $b
     * @return bool
     */
    private function personSortByRoom($a, $b)
    {
        return $a['房间号'] >= $b['房间号'];
    }

    /**
     * 格式化单身人员信息，组织出要写入excel的数组
     * @param $persons
     * @param $room
     * @return array
     */
    private function singleFormatData($persons, $room)
    {
        $data = array();
        foreach ($room as $k => $v) {
            // 计算每个房间的人员数量
            $v['persons'] = isset($persons[$v['room_id']]) ? $persons[$v['room_id']] : array();
            $count = count($v['persons']);
            //计算每个房间最大人员数量
            if (mb_strpos($v['building'], '高') === 0)
                $v['length'] = ($count > 4) ? $count : 4;
            elseif (mb_strpos($v['room'], '6') === 0)
                $v['length'] = ($count > 6) ? $count : 6;
            else
                $v['length'] = ($count > 4) ? $count : 4;

            for ($i = 0; $i < $v['length']; $i++) {
                $arr = array();
                //组织数据
                $arr['房间号'] = mb_strpos($v['building'], '高') === 0 ?
                    $v['building'] .'-'.$v['room'] :
                    $v['building'] .'-'.$v['unit'] .'-'.$v['room'];
                $arr['姓名'] = $v['persons'][$i]['name'];
                $arr['部门'] = $v['persons'][$i]['department'];
                $arr['学历'] = $v['persons'][$i]['edu'] ? '研究生':'';
                $arr['床号'] = $v['persons'][$i]['bed_number'] ? $v['persons'][$i]['bed_number'] : '';
                $arr['电话'] = $v['persons'][$i]['tel'] ? $v['persons'][$i]['tel'] : '';
                $arr['进厂时间'] = $v['persons'][$i]['entrancetime'];
                $arr['备注'] = $v['persons'][$i]['remark'];
                $data[] = $arr;
            }
        }
        //按房间排序
        usort($data, array(__CLASS__, 'personSortByRoom'));
        return $data;
    }
}

