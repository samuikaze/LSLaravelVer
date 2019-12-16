@extends('frontend.layouts.master')

@section('title', $postinfo['title'] . " - " . $boardinfo['name'] . " | 使用者設定")

@section('content')
<div class="row">
    <div class="col-md-10 col-md-push-1">
    <?php /* if (!empty($_GET['msg']) && $_GET['msg'] == 'notifyerrnooid') { ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4><strong>無法識別訂單編號，請依正常程序操作！</strong></h4>
        </div>
    <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'notifysuccess') { ?>
        <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4><strong>已通知團隊您已付款！</strong></h4>
        </div>
    <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'removeerrnoremovereason') { ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4><strong>請確實輸入您的申請原因！</strong></h4>
        </div>
    <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'removeerrnoorderstatus') { ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 1em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4><strong>無法取得訂單狀態，請依正常程序操作！</strong></h4>
        </div>
    <?php } elseif (!empty($_GET['msg']) && $_GET['msg'] == 'removesuccess') { ?>
        <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-top: 1em;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4><strong>完成取消訂單申請！</strong></h4>
        </div>
    <?php } */
        /* 申請取消訂單 */ if (!empty($_GET['action']) && $_GET['action'] == 'removeorder') {
        if (empty($_GET['oid'])) { ?>
                <div class="panel panel-danger" style="margin-top: 1em;">
                    <div class="panel-heading">
                        <h3 class="panel-title">警告</h3>
                    </div>
                    <div class="panel-body">
                        <h2 class="danger-warn">無法識別訂單編號，請依正常程序執行操作！<br /><br />
                            <div class="btn-group" role="group">
                                <a class="btn btn-lg btn-info" href="?action=orderlist">返回訂單管理</a>
                            </div>
                        </h2>
                    </div>
                </div>
            <?php } else {
            $orderid = $_GET['oid'];
            $orderdetailSql = mysqli_query($connect, "SELECT * FROM `orders` WHERE `orderID`=$orderid;");
            $ordernums = mysqli_num_rows($orderdetailSql);
            // 若找不到這筆資料
            if ($ordernums == 0) { ?>
                    <div class="panel panel-danger" style="margin-top: 1em;">
                        <div class="panel-heading">
                            <h3 class="panel-title">警告</h3>
                        </div>
                        <div class="panel-body">
                            <h2 class="danger-warn">找不到這筆訂單，請依正常程序執行操作！<br /><br />
                                <div class="btn-group" role="group">
                                    <a class="btn btn-lg btn-info" href="?action=orderlist">返回訂單管理</a>
                                </div>
                            </h2>
                        </div>
                    </div>
                <?php } else {
                $orderdetailData = mysqli_fetch_array($orderdetailSql, MYSQLI_ASSOC);
                // 如果已經提出過取消訂單的申請
                if($orderdetailData['removeApplied'] == '1'){ ?>
                    <div class="panel panel-danger" style="margin-top: 1em;">
                        <div class="panel-heading">
                            <h3 class="panel-title">錯誤</h3>
                        </div>
                        <div class="panel-body">
                            <h2 class="danger-warn">您已經提出過申請了，請不要重複提出取消的申請！<br /><br />
                                <div class="btn-group" role="group">
                                    <a class="btn btn-lg btn-info" href="?action=orderlist">返回訂單管理</a>
                                </div>
                            </h2>
                        </div>
                    </div>
                <?php }elseif($orderdetailData['orderStatus'] == '訂單已取消'){ ?>
                    <div class="panel panel-danger" style="margin-top: 1em;">
                        <div class="panel-heading">
                            <h3 class="panel-title">錯誤</h3>
                        </div>
                        <div class="panel-body">
                            <h2 class="danger-warn">這筆訂單已被取消，請依正常程序執行操作！<br /><br />
                                <div class="btn-group" role="group">
                                    <a class="btn btn-lg btn-info" href="?action=orderlist">返回訂單管理</a>
                                </div>
                            </h2>
                        </div>
                    </div>
                <?php }else{
                // 先把個別商品分出來(第$i個商品為$orderGoods[$i])
                $orderGoods = explode(",", $orderdetailData['orderContent']);
                // 再處理商品ID($goodsinfo[$i][0])和數量($goodsinfo[$i][1])
                $goodsinfo = array();
                foreach ($orderGoods as $i => $val) {
                    $goodsinfo[$i] = explode(":", $orderGoods[$i]);
                    // 處理 SQL 條件語法
                    if ($i == 0) {
                        $condition = "`goodsOrder`=" . $goodsinfo[$i][0];
                        $gOrder = "ORDER BY CASE `goodsOrder` WHEN " . $goodsinfo[$i][0] . " THEN " . ($i + 1);
                    } else {
                        $condition .= " OR `goodsOrder`=" . $goodsinfo[$i][0];
                        $gOrder .= " WHEN " . $goodsinfo[$i][0] . " THEN " . ($i + 1);
                    }
                }
                $gOrder .= " END";
                $goodsdata = mysqli_query($connect, "SELECT * FROM `goodslist` WHERE $condition $gOrder;");
                ?>
                    <form method="POST" action="actions.php?action=removeorder">
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h3 class="panel-title">申請取消訂單</h3>
                            </div>
                            <div class="panel-body noPadding">
                                <div class="alert alert-danger" role="alert" style="margin: 1em;"><strong>注意！</strong> 多次申請取消訂單我們可能會暫時收回您下訂商品的權利</div>
                                <div class="form-group" style="margin: 1em;">
                                    <label for="removereason">申請取消訂單的原因</label>
                                    <textarea name="removereason" id="removereason" row="3" class="form-control" placeholder="請輸入您想取消此訂單的原因以供我們審核，請您一併付上退款方式"></textarea>
                                </div>
                                <input type="hidden" name="oid" value="<?php echo $orderdetailData['orderID']; ?>" />
                                <input type="hidden" name="orderstatus" value="<?php echo $orderdetailData['orderStatus']; ?>" />
                                <hr />
                                <div class="col-sm-8">
                                    <div class="panel panel-info" style="margin-top: 1em;">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">訂購商品內容</h3>
                                        </div>
                                        <div class="panel-body">
                                            <?php
                                            $i = 0;
                                            while ($goodsdataR = mysqli_fetch_array($goodsdata, MYSQLI_ASSOC)) {
                                                if ($i != 0) { ?>
                                                    <div class="clearfix"></div>
                                                    <hr class="divideTotal" />
                                                <?php } ?>
                                                <!-- 一個商品 -->
                                                <div class="form-group">
                                                    <div class="col-sm-8">
                                                        <img src="images/goods/<?php echo $goodsdataR['goodsImgUrl']; ?>" alt="商品圖" class="img-responsive" />
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="col-sm-5 control-label sessionCtrlTable">品名</label>
                                                        <div class="col-sm-7">
                                                            <p class="form-control-static"><?php echo $goodsdataR['goodsName']; ?></p>
                                                        </div>
                                                        <label class="col-sm-5 control-label sessionCtrlTable">單價</label>
                                                        <div class="col-sm-7">
                                                            <p class="form-control-static"><?php echo $goodsdataR['goodsPrice']; ?> 元</p>
                                                        </div>
                                                        <label class="col-sm-5 control-label sessionCtrlTable">數量</label>
                                                        <div class="col-sm-7">
                                                            <p class="form-control-static"><?php echo $goodsinfo[$i][1]; ?></p>
                                                        </div>
                                                        <label class="col-sm-5 control-label sessionCtrlTable">小計</label>
                                                        <div class="col-sm-7">
                                                            <p class="form-control-static"><?php echo $goodsdataR['goodsPrice'] * $goodsinfo[$i][1]; ?> 元</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /一個商品 -->
                                                <?php
                                                $i += 1;
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="panel panel-info" style="margin-top: 1em;">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">訂單詳細資料</h3>
                                        </div>
                                        <div class="panel-body noPadding">
                                            <div class="form-group">
                                                <label class="col-sm-5 control-label sessionCtrlTable">訂單編號</label>
                                                <div class="col-sm-7">
                                                    <p class="form-control-static"><?php echo $orderdetailData['orderID']; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-5 control-label sessionCtrlTable">訂貨人</label>
                                                <div class="col-sm-7">
                                                    <p class="form-control-static"><?php echo $orderdetailData['orderRealName']; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-5 control-label sessionCtrlTable">連絡電話</label>
                                                <div class="col-sm-7">
                                                    <p class="form-control-static"><?php echo $orderdetailData['orderPhone']; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-5 control-label sessionCtrlTable">付款方式</label>
                                                <div class="col-sm-7">
                                                    <p class="form-control-static"><?php echo $orderdetailData['orderCasher']; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-5 control-label sessionCtrlTable">取貨方式</label>
                                                <div class="col-sm-7">
                                                    <p class="form-control-static"><?php echo $orderdetailData['orderPattern']; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-5 control-label sessionCtrlTable">送貨位置</label>
                                                <div class="col-sm-7">
                                                    <p class="form-control-static"><?php echo $orderdetailData['orderAddress']; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-5 control-label sessionCtrlTable">運費</label>
                                                <div class="col-sm-7">
                                                    <p class="form-control-static"><?php echo $orderdetailData['orderFreight']; ?> 元</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-5 control-label sessionCtrlTable">應付金額</label>
                                                <div class="col-sm-7">
                                                    <p class="form-control-static"><?php echo $orderdetailData['orderPrice']; ?> 元</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-5 control-label sessionCtrlTable">下訂日期</label>
                                                <div class="col-sm-7">
                                                    <p class="form-control-static"><?php echo $orderdetailData['orderDate']; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-5 control-label sessionCtrlTable">訂單狀態</label>
                                                <div class="col-sm-7">
                                                    <p class="form-control-static"><?php echo $orderdetailData['orderStatus']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center" style="margin-bottom: 1em;">
                                    <input type="submit" class="btn btn-danger btn-lg" name="submit" value="確認提出申請" />
                                    <a href="?action=orderlist" class="btn btn-lg btn-success">返回訂單管理</a>
                                </div>
                                        <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </form>
        <?php } ?>
    </div>
</div>
@endsection