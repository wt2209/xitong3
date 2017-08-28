<?php

/**
 * Created by PhpStorm.
 * User: WT
 * Date: 15-4-3
 * Time: 下午11:12
 */
class DispatchModel extends SingleModel
{
    /**
     * 主表
     * @var string
     */
    public $table = 'dispatch';

    protected $moveTable = 'dispatch_move';

    protected $quitTable = 'dispatch_quit';


    /**
     * 将人员信息移动到隐藏表中。文件导入时使用
     * @param $roomId
     * @return bool
     */
    public function moveToInvisibleTable($roomId)
    {
        //在外部已开启事务
        $data = $this->getDataByWhere(array('room_id'=>$roomId));
        //这个房间里没人
        if (!$data) {
            return true;
        }
        //删除的条件
        $allIds = array();

        //将人员移动到隐藏表
        foreach ($data as $v) {
            $allIds[] = $v['id'];
            unset($v['id']);
            if (!$this->table('dispatch_invisible')->add($v)) {
                return false;
            }
        }

        //在主表中删除
        $map['id'] = array('in', $allIds);
        return $this->where($map)->del();
    }
}