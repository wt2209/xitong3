<?php
class RentChargeViewModel extends ViewModel
{
    public $table = 'rent_charge';

    public $view = array(
        'rent_charge'=>array(
            '_as'=>'charge',
            '_type'=>'inner'
        ),
        'rent'=>array(
            '_type'=>'inner',
            '_on'=>'charge.rent_id=rent.id'
        ),
        'room'=>array(
            '_on'=>'rent.room_id=room.room_id',
        )
    );

    public function getDataByWhere($where, $limit)
    {
        if ($where) {
            if ($limit)
                return $this->where($where)->limit($limit)->order('charge_time desc')->select();
            else
                return $this->where($where)->order('charge_time desc')->select();
        } else {
            if ($limit)
                return $this->limit($limit)->order('charge_time desc')->select();
            else
                return $this->select();
        }
    }
}