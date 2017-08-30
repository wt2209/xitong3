<?php

class RepairController extends AuthController
{
    protected $repairModel;
    protected $error = '错误：内部错误！';


    public function __init()
    {
        $this->repairModel = K('repair');
    }


    /**
     * 推送未处理消息
     */
    public function notify()
    {
        $return = array(
            'data'=>array(
                'unreviewedCount'=>0
            ),
            'status'=>1,
        );
        $this->ajax($return);
    }
    /**
     * 添加维修项目
     */
    public function add()
    {
        $this->display('add');
    }

    /**
     * 修改待审核项目
     */
    public function edit(){
        $this->displayPage('edit');
    }

    /**
     * 存储数据
     */
    public function store()
    {
        if ($this->validateData()) {
            $repairInfo = $this->setRepairInfo();
            $this->repairModel->saveRepairInfo($repairInfo);
            $this->success();
        } else {
            $this->error($this->error);
        }
    }
    /**
     * 待审核项目
     */
    public function underReview()
    {
        $underReviews = $this->repairModel->getUnderReviewInfo();
        $this->assign('underReviews', $underReviews);
        $this->display('underReview');
    }

    /**
     * 显示审核页面
     */
    public function review()
    {
        $this->displayPage('review');
    }

    /**
     * 审核单个项目并保存结果
     */
    public function reviewForOne()
    {
        $review = array(
            'id'=>intval(Q('post.id')),
            'is_passed' => intval(Q('post.is_passed')),
            'review_remark'=>Q('post.review_remark'),
            'reviewed_at'=>empty($_POST['reviewed_at']) ? date('Y-m-d') : Q('reviewed_at'),
            'reviewer'=>session('display_name'),
            'is_reviewed'=>1
        );
        $this->repairModel->reviewForOne($review);
        $this->success();
    }

    /**
     * 审核全部未审核项目并全部通过
     */
    public function reviewForAll()
    {
        $this->repairModel->reviewForAll();
        $this->success();
    }


    /**
     * 待完工项目
     */
    public function underFinish()
    {
        $underFinishes = $this->repairModel->getUnderFinishInfo();
        $this->assign('underFinishes', $underFinishes);
        $this->display('underFinish');
    }

    /**
     * 已打印，保存打印时间等结果
     */
    public function printed()
    {
        $id = intval(Q('post.id'));
        $this->repairModel->itemIsPrinted($id);
    }

    /**
     * 显示项目完工页面
     */
    public function finish()
    {
        $this->displayPage('finish');
    }

    /**
     * 单个项目完工，存储完工结果
     */
    public function finishForOne()
    {
        $finish = array(
            'id'=>intval(Q('post.id')),
            'is_finished' => 1,
            'finish_remark'=>Q('post.finish_remark'),
            'finished_at'=>empty($_POST['finished_at']) ? date('Y-m-d') : Q('finished_at'),
        );
        $this->repairModel->finishForOne($finish);
        $this->success();
    }

    /**
     * 已完工项目
     */
    public function finished()
    {
        $finishedItems = $this->repairModel->getFinishedInfo();
        $this->assign('finishedItems', $finishedItems);
        $this->display('finishedItems');
    }

    /**
     * 所有项目完工
     */
    public function finishForAll()
    {
        $this->repairModel->finishForAll();
        $this->success();
    }

    /**
     * 显示评价界面
     */
    public function comment()
    {
        $this->displayPage('comment');
    }


    public function commentForOne()
    {
        $comment = array(
            'id'=>intval(Q('post.id')),
            'comment'=>Q('post.comment')
        );
        $this->repairModel->commentForOne($comment);
        $this->success();
    }

    /**
     * 验证字段是否为空
     * @return bool
     */
    private function validateData()
    {
        $location = Q('post.location');
        $content = Q('post.content');
        if (empty($location) || empty($content)) {
            $this->error = '错误：位置或内容不能为空！';
            return false;
        }

        return true;
    }

    /**
     * 显示界面
     * @param null $type
     */
    private function displayPage($type)
    {
        $id = Q('get.id');
        $repairItem = $this->repairModel->getRepairInfoById($id);
        $this->assign('repairItem', $repairItem);
        $this->display($type);
    }

    /**
     * 组合出需要保存的数据
     * @return array
     */
    private function setRepairInfo()
    {
        $createAt = Q('post.created_at');
        if (empty($createAt)) {
            $createAt = date('Y-m-d');
        }
        $result = array(
            'name'=>Q('post.name'),
            'location'=>Q('post.location'),
            'content'=>Q('post.content'),
            'created_at'=>$createAt,
            'input_man'=>session('display_name')
        );
        $id = intval(Q('post.id'));
        if (!empty($id)) {
            $result['id'] = $id;
        }
        return $result;
    }
}