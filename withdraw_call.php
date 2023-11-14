<?php

// 引用数据库连接文件
require_once 'db_connection.php';
require_once 'sign.php';

$jsonData = file_get_contents("php://input");

// 解码 JSON 数据为 PHP 数组
$data = json_decode($jsonData, true);

global $appid, $secretKey;

// 提取需要的参数
$appid = isset($data['appid']) ? $data['appid'] : null;
$network_name = isset($data['network_name']) ? $data['network_name'] : null;
$coin_symbol = isset($data['coin_symbol']) ? $data['coin_symbol'] : null;
$decimals = isset($data['decimals']) ? $data['decimals'] : null;
$address = isset($data['address']) ? $data['address'] : null;
$amount = isset($data['amount']) ? $data['amount'] : null;
$business_id = isset($data['business_id']) ? $data['business_id'] : null; // 当前业务id就是创建提现时业务系统传给钱包服务的
$max_block_high = isset($data['max_block_high']) ? $data['max_block_high'] : null;
$block_high = isset($data['block_high']) ? $data['block_high'] : null;
$block_hash = isset($data['block_hash']) ? $data['block_hash'] : null;
$txid = isset($data['txid']) ? $data['txid'] : null;
$status = isset($data['status']) ? $data['status'] : null;
$sign = isset($data['sign']) ? $data['sign'] : null;

$b = verifySign($data);

// 无效请求
if (!$b) {
    $response = array('err' => '请求无效');
    echo json_encode($response);
    exit();
}

// 获取数据库连接
$pdo = connectDB();
$query = $pdo->prepare("SELECT `member_id`,`status` FROM withdraw WHERE business_id = :business_id LIMIT 1");
$query->bindParam(':business_id', $business_id, PDO::PARAM_STR);
$query->execute();
$withdraw = $query->fetch(PDO::FETCH_ASSOC);

// 根据业务id去重，先查询业务id是否存在，不存在或者状态大于3，则直接返回成功
if (!$withdraw || $withdraw["status"] > 3) {
    exit('SUCCESS');
}

// 如果都通过，则做业务处理

// TODO 事务处理开始

// 1、修改状态

// 2.0、如果 回调 $status 状态为打包中，则只修改状态
// 2.1、如果 回调 $status 状态为成功，则有冻结余额，清除冻结余额
// 2.2、如果 回调 $status 状态为失败，则退还余额，清除冻结余额

// TODO 事务处理结束

$pdo = null;
// 返回成功
exit('SUCCESS');
