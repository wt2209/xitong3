<?php

class SingleSDChargeViewModel extends ViewModel
{
    public $table = 'worker_sd_charge';

    public $view = array(
        'worker_sd_charge'=>array(
            '_as'=>'charge',
            '_type'=>'inner'
        ),
        'room'=>array(
            '_on'=>'charge.room_id=room.room_id',
        )
    );
}