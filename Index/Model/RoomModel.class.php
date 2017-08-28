<?php
/**
 * Created by PhpStorm.
 * User: WT
 * Date: 15-4-4
 * Time: 下午10:48
 */

class RoomModel extends Model{
    public $table = 'room';

    /**
     * 获取全部房间
     * @return array
     */
    public function getAllRoom()
    {
        $data = array();
        if($data = S("room")){
            return $data;
        }
        $data = $this->select();
        //缓存
        S('room', $data);
        return $data;
    }

    /**
     * 根据where获取房间
     * @param $where
     * @param $isOne 是否只返回一条数据 true：是 false：否
     * @return array
     */
    public function getRoomByWhere($where, $isOne = false, $limit = null)
    {
        if ($isOne)
            return $this->where($where)->find();
        else{
            if ($limit)
                return $this->where($where)->order('building asc, unit asc, room asc')->limit($limit)->select();
            else
                return $this->where($where)->order('building asc, unit asc, room asc')->select();
        }
    }

    /**
     * 获取当前楼共有几个单元
     */
    public function getUnitNumber($building)
    {
        return $this->where('building="'.$building.'"')->count('distinct(unit)');
    }

    /**
     * 获取满足条件的房间共有多少个
     * @param $where
     * @return mixed
     */
    public function getAllNumber($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 添加数据
     * @param $data
     * @return mixed
     */
    public function addRoomData($data)
    {
        return $this->add($data);
    }

    public function getOneField($field, $where)
    {
        if ($where) {
            return $this->where($where)->getField($field, true);
        } else {
            return $this->getField($field, true);
        }
    }

    public function updateRoomData($data)
    {
        return $this->update($data);
    }
}