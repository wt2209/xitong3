<?php
/**
* Created by PhpStorm.
* User: WT
* Date: 15-4-3
* Time: 下午11:12
*/
class WorkerModel extends SingleModel
{
/**
* 主表
* @var string
*/
    public $table = 'worker';

    protected $moveTable = 'worker_move';

    protected $quitTable = 'worker_quit';
}