<?php
/**
 * Created by PhpStorm.
 * User: WT
 * Date: 2015/4/18
 * Time: 15:27
 */

abstract class SingleChargeController extends AuthController
{
    /**
     * 显示职工床位费缴费信息
     */
    public function index()
    {
        $data = $this->_db->getViewData(null, 50);
        $sum = $this->_db->getSumByWhere();
        $count = $this->_db->getNumberByWhere();
        $this->assign('sum', $sum['sum']);
        $this->assign('count', $count);
        $this->assign('data', $data);
        $this->assign('chargeType', $this->_allTypes);
        $this->display('index');
    }

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
        //每页的记录条数
        $pageShowNumber = 20;
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
            $this->index();
            exit;
        }

        $count = $this->_db->getNumberByWhere($where);
        $page = new Page($count, $pageShowNumber);
        $data = $this->_db->getViewData($where, $page->limit());
        //总钱数
        $sum = $this->_db->getSumByWhere($where);
        //分配模板数据
        $this->assign('count', $count);
        $this->assign('chargeType', C('SINGLE_RENT_TYPE'));
        $this->assign('sum', $sum['sum']);
        $this->assign('data', $data);
        $this->assign('page', $page->show());
        $this->display();
    }

    abstract public function charge();
}