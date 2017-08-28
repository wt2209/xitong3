<?php
/**
 * Created by PhpStorm.
 * User: WT
 * Date: 2015/4/18
 * Time: 15:54
 */


class SingleSDChargeModel extends SingleChargeModel{
    /**
     * 主表
     * 职工水电费缴费表
     * @var string
     */
    public $table = 'worker_sd_charge';

    public function __init()
    {
        $this->viewModel = K('SingleSDChargeView');
    }
}
