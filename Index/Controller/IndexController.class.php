<?php
import('HDPHP.Extend.Org.PhpExcel.HdExcel');// 导入类库
//测试控制器类
class IndexController extends AuthController{
    //动作方法
    public function index()
    {
        //显示视图
        $this->display();
    }

    public function map()
    {
        $this->display();
    }

    /**
     * 全局搜索
     */
    public function search()
    {
        $keyword = $_GET['keyword'];
        if (!$keyword) {
            exit;
        }
        if (intval($keyword)) { //搜索电话
            $where = "tel like '%{$keyword}%'";
        } else { //搜索姓名
            $where = "name like '%{$keyword}%'";
        }

        $collegeDb = new CollegeController();
        $workerDb = new WorkerController();
        $dispatchDb = new DispatchController();
        $rentDb = new RentController();

        $college = $collegeDb->searchPersonByWhere($where);
        $worker = $workerDb->searchPersonByWhere($where);
        $dispatch = $dispatchDb->searchPersonByWhere($where);
        $rent = $rentDb->searchPersonByWhere($where);

        $this->assign('college', $college);
        $this->assign('worker', $worker);
        $this->assign('dispatch', $dispatch);
        $this->assign('rent', $rent);

        $this->display();
    }



    public function aaa()
    {
        $rent = K('rent')->all();
        foreach ($rent as $key => $value) {
            $rent[$value['name']] = $value;
        }
        $college = K('college')->all();
        $worker = K('worker')->all();
        $roomTmp = K('room')->all();
        $room = array();
        foreach ($roomTmp as $key => $value) {
            $room[$value['room_id']] = $value['building'] . '-' . $value['unit'] . '-' . $value['room'];
        }

        $result_1 = array();
        $result_2 = array();
        foreach ($college as $key => $value) {
            if (isset($rent[$value['name']])) {
                if (isset($rent[$value['name']]['department']) 
                    && $rent[$value['name']]['department'] == $value['department']) {
                    $result_2[] = array(
                        '姓名'=>$value['name'],
                        '部门'=>$value['department'],
                        '单身房间'=>$room[$value['room_id']],
                        '租赁房间'=>$room[$rent[$value['name']]['room_id']],
                        '入住时间'=>$value['entrancetime'],
                        '劳动合同起始日'=>date('Y-m-d', $rent[$value['name']]['contract_s']),
                        '电话'=>$rent[$value['name']]['tel']
                        );
                }
                $result_1[] = $value['name'];
            }
        }
        foreach ($worker as $key => $value) {
            if (isset($rent[$value['name']])) {
                if (isset($rent[$value['name']]['department']) 
                    && $rent[$value['name']]['department'] == $value['department']) {
                    $result_2[] = array(
                        '姓名'=>$value['name'],
                        '部门'=>$value['department'],
                        '单身房间'=>$room[$value['room_id']],
                        '租赁房间'=>$room[$rent[$value['name']]['room_id']],
                        '入住时间'=>$value['entrancetime'],
                        '劳动合同起始日'=>date('Y-m-d', $rent[$value['name']]['contract_s']),
                        '电话'=>$rent[$value['name']]['tel']
                        );
                }
                $result_1[] = $value['name'];
            }
        }
        header("Content-type:text/html; charset=UTF-8");
        p($result_1);
        p($result_2);
    }

    public function bbb()
    {
       /* $phpExcel = new HdExcel();
        $zhuankeTmp = $phpExcel->readExcel('D:\zhuanke.xls');
        header('Content-Type:text/html;charset=utf-8');
        foreach ($zhuankeTmp as $k => $v) {
            $zhuanke[trim($v[0])] = array(
                'identify'=>$v[1],
                'department' => $v[3]
            );
        }

        // p($zhuanke);die;

        $collegeTmp = K('college')->all();
        // p($collegeTmp);die;
        foreach ($collegeTmp as $k => $v) {
            if (substr($v['entrancetime'], 0,4) == '2016') {
                //$college[] = $v;
                if (isset($zhuanke[$v['name']])) {
                    if (empty($v['department'])) {
                        $v['department'] = $zhuanke[$v['name']]['department'];
                    }
                    $v['identify'] = $zhuanke[$v['name']]['identify'];

                    K('college')->save($v);
                }
            }
        }
        // p($college);die;*/

    }
}