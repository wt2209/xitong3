<?php

class SettingController extends AuthController
{
    /**
     * 修改密码
     */
    public function editPassword()
    {
        if (IS_AJAX) {
            $aid = (int)$_SESSION['aid'];
            if (!$aid) {
                $this->error('错误：参数错误！');
            }
            $oldPassword = md5($_POST['oldPassword']);
            $newPassword = md5($_POST['newPassword']);
            $confirmPassword = md5($_POST['confirmPassword']);

            if($newPassword != $confirmPassword) $this->error("两次密码不一致！");

            $db = K('User');
            $data = $db->getOneData("aid=$aid");
            if ($oldPassword != $data['password']) {
                $this->error('旧密码输入错误！');
            }
            $arr = array('aid'=>$aid, 'password'=>$newPassword);
            if($db->updateData($arr)){
                $this->success('密码更改成功！');
            }else{
                $this->error('错误：密码更改失败！');
            }
        } else {
            $this->display();
        }
    }

}