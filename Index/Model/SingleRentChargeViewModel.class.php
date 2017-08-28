<?php
class SingleRentChargeViewModel extends ViewModel
{
    public $table = 'worker_rent_charge';

    public $view = array(
        'worker_rent_charge'=>array(
            '_as'=>'charge',
            '_type'=>'inner'
        ),
        'worker'=>array(
            '_on'=>'charge.id=worker.id',
            '_type'=>'inner'
        ),
        'room'=>array(
            '_on'=>'worker.room_id=room.room_id',
        )
    );
}