<?php

// 生成充值地址

// 引用数据库连接文件
require_once 'db_connection.php';
require_once 'sign.php';

// 获取数据库连接
$pdo = connectDB();

// 网络名称
$networkName = "BEP20";
// 币种符号
$coinSymbol = "USDT";
// 提现地址
$address = "xxxx";
// 数量
$amount = "100";

// 回调URL，创建该地址，如果有充值钱包服务就会调用该接口，这个接口地址业务端自己定义，要求不能有其他鉴权，参考微信支付模式
$callUrl = "http://127.0.0.1/withdraw_call.php";

//TODO 事务开始

// 1、业务系统扣款，如果有冻结余额概念，这里冻结余额

// 2、追加记录到 withdraw 表

// 3、withdraw 表添加后自增id用来当回调时的业务id，也可以自己定义，但是注意，这个id提现成功回调时会返回，这个时候根据业务id来确定是哪一条记录成功
$business_id = "1"; // 业务id

// 调用钱包服务创建地址的接口
$url = 'http://127.0.0.1:10001/createWallet';
$externalData = array(
    'network_name' => $networkName,
    'coin_symbol' => $coinSymbol,
    'address' => $address,
    'amount' => $amount,
    'business_id' => $business_id,
    'call_url' => $callUrl,
);

// 发送 cURL 请求
$externalResponse = sendCurlRequest($url, $externalData);

// 处理外部 API 的响应
$externalData = json_decode($externalResponse, true);

if (!isset($externalData["code"]) || $externalData["code"] > 0) {
    var_dump($externalResponse);
    die("请求失败");
}

//TODO 事务结束


// 成功，业务系统自行处理