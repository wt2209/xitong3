<?php

/**
 * Created by PhpStorm.
 * User: WT
 * Date: 15-4-3
 * Time: 下午11:12
 */
class CollegeModel extends SingleModel
{
    /**
     * 主表
     * @var string
     */
    public $table = 'college';

    protected $moveTable = 'college_move';

    protected $quitTable = 'college_quit';
}