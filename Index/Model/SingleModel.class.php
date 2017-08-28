<?php

/**
 * Created by PhpStorm.
 * User: WT
 * Date: 15-4-3
 * Time: 下午11:12
 */
class SingleModel extends CommonModel
{
    /**
     * 将退房人员数据添加到退房表
     * @param $data
     * @return bool
     */
    public function addDataToQuit($data)
    {
        //卸载id，防止发生错误
        if (isset($data['id'])) {
            unset($data['id']);
        }
        if ($this->table($this->quitTable)->insert($data))
            return true;
        else {
            return false;
        }
    }

    /**
     * 将调房人员数据添加到退房表
     * @param $data
     * @return bool
     */
    public function addDataToMove($data)
    {
        if ($this->table($this->moveTable)->insert($data))
            return true;
        else {
            return false;
        }
    }

    /**
     * 删除退房信息
     * @param $id
     * @return mixed
     */
    public function delQuitData($id)
    {
        return $this->table($this->quitTable)->del($id);
    }

    /**
     * 删除调房信息
     * @param $id
     * @return mixed
     */
    public function delMoveData($id)
    {
        return $this->table($this->moveTable)->del($id);
    }
}