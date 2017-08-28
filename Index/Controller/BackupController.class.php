<?php
/**
 * 备份与还原
 */
class BackupController extends AuthController{

    public function set(){
        $this->display();
    }
    /**
     * 备份数据
     */
    public function backup(){

        $result = Backup::backup(
            array(
                "size" => 100,
                "dir" => "./data/" . C("BACKUP_DIR") . '/' . date("YmdHis")
            )
        );

        if ($result === false) {
            // 备份发生错误
            $this->error(Backup::$error, U('set'));
        } else {
            if ($result['status'] == 'success') {
                // 备份完成
                $this->success($result['message'], U('set'));
            } else {
               // 备份过程中
                $this->success($result['message'], $result['url'], 0.1);
            }
        }
    }

    /**
     * 还原数据
     */
    public function index() {
        $file = Dir::tree("./data/backup");
        $dir = array();
        foreach ($file as $f) {
            if (is_dir($f['path'])) {
                $dir[] = $f;
            }
        }
        $this -> assign("dir", $dir);
        $this -> display();
    }
    /**
     * 还原数据
     */
    public function recovery() {
        $dir = Q('dir');
        $result = Backup::recovery(array('dir'=>"./data/" . C("BACKUP_DIR") . "/" . $dir));
         if ($result === false) {// 还原发生错误
            $this->error(Backup::$error, U('index'));
         } else {
            if ($result['status'] == 'success') {// 还原完毕
                $this->success($result['message'], U('index'));
            } else {// 备份运行中 ...
                $this->success($result['message'], $result['url'], 3);
            }
         }
    }
    /**
     * 删除还原目录
     */
    public function del() {
        $dir = $_POST['dir'];
        foreach ($dir as $d) {
            if (!Dir::del("./data/" . C("BACKUP_DIR") . "/" . $d)) {
                $this->error('错误：删除失败！');
            }
        }
        $this->success('删除成功');
    }
}