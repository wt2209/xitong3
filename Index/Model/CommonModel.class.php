<?php

class CommonModel extends Model
{
    /**
     * 返回全部数据
     * 使用缓存
     * @return array
     */
    public function getAllData()
    {
        $data = array();
        if (!($data = S($this->table))) {
            $this->updateCache();
            $data = S($this->table);
        }
        return $data;
    }

    /**
     * 根据where返回数据
     * @param $where
     * @param $isOne =false  是否只返回一条数据，默认为否
     */
    public function getDataByWhere($where, $isOne = false)
    {
        if ($isOne) //返回一条数据
            return $this->where($where)->find();
        else //返回全部数据
            return $this->where($where)->order('id desc')->select();
        }

    /**
     * 返回满足条件的记录数量
     * @param $where
     * @return int
     */
    public function getNumberByWhere($where)
    {
        return $this->where($where)->count();
    }


    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function addData($data)
    {
        if (!$data) {
            $this->error = '错误：数据不能为空！';
            return false;
        }
        if ($this->add($data)) {
            //更新缓存
            if (!$this->updateCache()) {
                $this->error = '错误：请在设置中更新缓存！';
                return false;
            }
            return true;
        } else {
            $this->error = '错误：数据添加失败！';
            return false;
        }
    }

    /**
     * 修改数据
     * @param $data
     * @return bool
     */
    public function editData($data)
    {
        if (!$data) {
            $this->error = '错误：数据不能为空！';
            return false;
        }

        //验证id
        if (!intval($data['id'])) {
            $this->error = '错误：ID不合法！';
            return false;
        }

        if ($this->update($data)) {
            if (!$this->updateCache()) {
                $this->error = '错误：请在设置中更新缓存！';
                return false;
            }
            return true;
        } else {
            $this->error = '错误：数据修改失败！';
            return false;
        }
    }


    /**
     * 根据id删除数据
     * @param $id
     * @return bool
     */
    public function delData($id)
    {
        if (!$id)
            return false;

        if ($this->del($id)) {
            //更新缓存
            if (!$this->updateCache()) {
                $this->error = '错误：请在设置中更新缓存！';
                return false;
            }
            return true;
        }
    }


    /**
     * 获取调退房信息
     * @param $type
     * @param $where
     * @param $limit
     * @return array
     */
    public function getQuitOrMoveData($type, $where, $limit = null)
    {
        switch (strtolower($type)) {
            case 'move':
                $table = $this->moveTable;
                break;
            case 'quit':
                $table = $this->quitTable;
                break;
            default:
                return false;
        }
        if ($limit) {
            if ($where) {
                return $this->table($table)->where($where)->order('id desc')->limit($limit)->select();
            } else {
                return $this->table($table)->order('id desc')->limit($limit)->select();
            }
        } else {
            if ($where) {
                return $this->table($table)->order('id desc')->where($where)->select();
            } else {
                return $this->table($table)->order('id desc')->select();
            }
        }
    }

    /**
     * 取得调退房人数
     * @param $type
     * @param $where
     * @return int
     */
    public function getQuitOrMoveNumber($type, $where = null)
    {
        switch (strtolower($type)) {
            case 'move':
                $table = $this->moveTable;
                break;
            case 'quit':
                $table = $this->quitTable;
                break;
            default:
                return fasle;
        }
        if ($where) {
            return $this->table($table)->where($where)->count();
        } else {
            return $this->table($table)->count();
        }
    }

    /**
     * 更新缓存
     * @return bool
     */
    protected function updateCache()
    {
        $data = array();
        $tmp = $this->order('id asc')->select();
        foreach ($tmp as $v) {
            $data[$v['room_id']][] = $v;
        }
        //缓存
        if (S($this->table, $data))
            return true;
        else {
            $this->error = '错误：缓存更新失败。。。';
            return false;
        }
    }
}