<?php

/**
 * Created by PhpStorm.
 * User: WT
 * Date: 2015/4/18
 * Time: 15:27
 */
class RentChargeController extends AuthController
{

    /**
     * 缴费模型
     * @var
     */
    private $_db;

    /**
     * 构造函数
     */
    public function __init()
    {
        $this->_db = K('RentCharge');
    }


    /**
     * 主页
     * 只显示最近50条缴费信息。若要查看更多，需使用搜索功能。
     */
    public function index()
    {


/*
        $count = $this->_db->getNumberByWhere();
        $page = new Page($count, 5);
        ;
        $totalMoneyArr = $this->_db->getSumMoneyByWhere();
$this->assign('totalMoney', $totalMoneyArr['sum']);
$this->assign('page', $page->show());
*/
        //只显示最新的50条
        $limit = '0,50';
        $data = $this->_db->getViewData(null, $limit);
        //总钱数
        $sum = $this->_db->getSumMoneyByWhere();
        $count = $this->_db->getNumberByWhere();
        //分配模板数据
        $this->assign('quarters', $this->getAllQuarters());
        $this->assignAllRentType();
        $this->assign('count', $count);
        $this->assign('sum', $sum['sum']);
        $this->assign('data', $data);
        $this->display('index');
    }


    /**
     *
     */
    public function search()
    {
        /**
         * 直接使用GET方式提交表单有一个问题，就是get方式会忽略掉 action 属性中原有的get参数，
         * 在本例中就会忽略 a c m 参数，造成URL解析错误。
         * 但是直接使用post提交又不能保留提交的参数，
         * 因此此处使用post提交，然后转到get
         */
        if (IS_POST) {
            go(U('search', $_POST));
        }
        //每页显示20条
        $pageShowNumber = 20;
        $startTime = isset($_GET['startTime']) ? strtotime($_GET['startTime']) : 0;
        $endTime = isset($_GET['endTime']) ? strtotime($_GET['endTime']) : 0;
        $chargeType = isset($_GET['chargeType']) ? intval($_GET['chargeType']) : 0;
        $quarter = isset($_GET['quarter']) ? intval($_GET['quarter']) : 0;
        $keyword = isset($_GET['keyword']) ? htmlspecialchars(strip_tags($_GET['keyword'])) : '';

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
            $this->index();
            exit;
        }

        $count = $this->_db->getNumberByWhere($where);
        $page = new Page($count, $pageShowNumber);
        $data = $this->_db->getViewData($where, $page->limit());
        //总钱数
        $sum = $this->_db->getSumMoneyByWhere($where);
        //分配模板数据
        $this->assign('count', $count);
        $this->assign('quarters', $this->getAllQuarters());
        $this->assignAllRentType();
        $this->assign('sum', $sum['sum']);
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }


    private function getAllQuarters()
    {
        $quarters = S('quarters');
        if (!$quarters) {
            $quarters = $this->_db->updateQuartersCache();
        }
        return $quarters;
    }

    private function assignAllRentType()
    {
        $chargeType = C('RENT_CHARGE_TYPE');
        if (!$chargeType) {
            return false;
        }
        $this->assign('chargeType', $chargeType);
    }

    public function rentCharge()
    {
        $rentId = intval($_POST['rent_id']);
        if (!$rentId) {
            $this->ajax(array('status' => 0, 'message' => '错误：人员ID不存在！'));
        }
        unset($_POST['rent_id']);

        $chargeTime = empty($_POST['charge_time']) ? time() : strtotime($_POST['charge_time']);
        unset($_POST['charge_time']);

        //获取所有季度
        $quarters = $this->getAllQuarters();
        //是否需要更新quarter缓存
        $needToUpdateCache = false;
        // 开启事务
        $this->_db->beginTrans();
        foreach ($_POST as $key => $value) {
            //金额为空，不处理。单允许值为0
            if (is_array($value) && empty($value['money']) && $value['money'] !== '0') {
                continue;
            } elseif (is_array($value)) {
                $k = htmlspecialchars(strip_tags($key));
                $money = $value['money'];
                $quarter = empty($value['quarter']) ? 0 : $value['quarter'];
                if (!is_numeric($money) || !is_numeric($quarter)) {
                    $this->ajax(array('status' => 0, 'message' => '错误：季度和金额必须是数字！'));
                    exit;
                }
                //1为押金，2为租金，3为物业费，4为水费，5为电费，6为取暖费，7为燃气费，8为电梯费
                $allChargeType = C('RENT_CHARGE_TYPE');
                $type = intval(substr($k, 1));
                //类型是一个合法的值，则可以进行添加工作
                if (in_array($type, array_keys($allChargeType))) {
                    if ($quarter == 0 && $allChargeType[$type]['needQuarter'] === true) {
                        $this->ajax(array('status' => 0, 'message' => '错误：季度必须是一个数字！'));
                        exit();
                    }
                    //若此次添加的季度不在所有季度中，则进行更新季度缓存工作
                    if (!in_array($quarter, $quarters)) {
                        $needToUpdateCache = true;
                    }
                } else {
                    halt('非法请求！');
                }
                //组合数组，准备添加
                $tmp = array(
                    'rent_id' => $rentId,
                    'quarter' => $quarter,
                    'charge_type' => $type,
                    'money' => $money,
                    'charge_remark' => htmlspecialchars(strip_tags($value['remark'])),
                    'charge_time' => $chargeTime
                );
                $status = $this->_db->addData($tmp);
                if (!$status) {
                    $this->_db->rollback();
                    $this->ajax(array('status' => 0, 'message' => '错误：插入数据过程中发生错误！'));
                }
            }
        }
        //更新季度缓存
        if ($needToUpdateCache) {
            $this->_db->updateQuartersCache();
        }
        //提交事务
        $this->_db->commit();
        $this->ajax(array('status' => 1, 'message' => '操作成功！'));
    }

    /**
     * 租赁退租费用
     * 可以进行搜索
     */
    public function quitChargeIndex()
    {
        if (IS_POST) {
            go(U('quitChargeIndex', $_POST));
        }
        //每页显示20条
        $pageShowNumber = 20;
        $startTime = isset($_GET['startTime']) ? strtotime($_GET['startTime']) : 0;
        $endTime = isset($_GET['endTime']) ? strtotime($_GET['endTime']) : 0;
        $chargeType = isset($_GET['chargeType']) ? intval($_GET['chargeType']) : 0;
        $quarter = isset($_GET['quarter']) ? intval($_GET['quarter']) : 0;
        $keyword = isset($_GET['keyword']) ? htmlspecialchars(strip_tags($_GET['keyword'])) : '';

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
        //初始化
        $map = array();
        if ($keyword) {
            //房间号
            if (mb_strpos($keyword, '-') !== false) {
                $map['room'] = array('like', "%{$keyword}%");
            } elseif (intval($keyword)) { //电话
                $map['tel']=array('like', "%{$keyword}%");
            } else {//姓名
                $map['name'] = array('like', "%{$keyword}%");
            }

        }
        $tmpPersons = $this->_db->getQuitData($map);
        //没有找到,分配空数据到模板
        if (!$tmpPersons) {
            $this->assign('data', array());
            $this->display();
            exit;
        }
        foreach ($tmpPersons as $p) {
            $persons[$p['id']] = $p;
        }

        $allIds = array_keys($persons);
        $where['rent_quit_id'] = array('in', $allIds);

        $count = $this->_db->getQuitChargeDataNumber($where);
        $page = new Page($count, $pageShowNumber);
        $data = $this->_db->getQuitChargeData($where, $page->limit());
        foreach ($data as $k => $v) {
            $data[$k] = array_merge($v, $persons[$v['rent_quit_id']]);
        }
        $this->assignAllRentType();
        $this->assign('quarters', $this->getAllQuarters());
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

}