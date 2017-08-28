<?php

class RepairModel extends Model
{
    /**
     * 存储数据。根据是否存在id字段来判断是添加还是修改
     * @param $data
     */
    public function saveRepairInfo($data)
    {
        if (isset($data['id'])) {
            //修改
            $this->where('id='.$data['id'])->save($data);
        } else {
            //新增
            $this->insert($data);
        }
    }

    public function getUnderReviewInfo()
    {
        return $this->where('is_reviewed=0')->all();
    }

    public function getRepairInfoById($id)
    {
        return $this->where('id='.intval($id))->find();
    }

    public function reviewForOne($data)
    {
        $where = array(
            'id'=>$data['id'],
            'is_reviewed'=>0
        );
        $this->where($where)->save($data);
    }

    public function reviewForAll()
    {
        $data = array(
            'is_reviewed'=>1,
            'reviewer' => session('display_name'),
            'is_passed'=>1,
            'review_remark'=>'',
            'reviewed_at'=>date('Y-m-d')
        );
        $this->where('is_reviewed=0')->save($data);
    }

    public function getUnderFinishInfo()
    {
        $where = array(
            'is_reviewed'=>1,
            'is_passed'=>1,
            'is_finished'=>0
        );
        return $this->where($where)->all();
    }

    public function itemIsPrinted($id)
    {
        $data = array(
            'is_printed'=>1,
            'printed_at'=>date('Y-m-d H:i:s')
        );
        $where = array(
            'id'=>$id,
            'is_printed'=>0
        );
        $this->where($where)->save($data);
    }

    public function finishForOne($data)
    {
        $where = array(
            'id'=>$data['id'],
            'is_finished'=>0
        );
        $this->where($where)->save($data);
    }

    public function finishForAll()
    {
        $data = array(
            'is_finished'=>1,
            'finish_remark'=>'',
            'finished_at'=>date('Y-m-d')
        );
        $this->where('is_finished=0')->save($data);
    }

    /**
     * 所有已完工项目
     */
    public function getFinishedInfo()
    {
        $where = array(
            'is_finished'=>1
        );
        return $this->where($where)->all();
    }
    public function commentForOne($data)
    {
        $where = array(
            'id'=>$data['id']
        );
        $this->where($where)->save($data);
    }
}