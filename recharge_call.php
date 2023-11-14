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
$business_id = isset($data['business_id']) ? $data['business_id'] : null;
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
$query = $pdo->prepare("SELECT `status` FROM recharge WHERE business_id = :business_id LIMIT 1");
$query->bindParam(':business_id', $business_id, PDO::PARAM_STR);
$query->execute();
$recharge = $query->fetch(PDO::FETCH_ASSOC);

// 根据业务id去重，先查询业务id是否存在，存在并且状态不为0，则直接返回成功
if ($recharge && $recharge["status"] != 0) {
    exit('SUCCESS');
}

// 先查询地址表，确定当前地址绑定的用户id
// 首先尝试从数据库查询
$query = $pdo->prepare("SELECT member_id FROM recharge_address WHERE network_name = :network_name AND coin_symbol = :coin_symbol AND address = :address LIMIT 1");
$query->bindParam(':network_name', $networkName, PDO::PARAM_STR);
$query->bindParam(':coin_symbol', $coinSymbol, PDO::PARAM_STR);
$query->bindParam(':address', $address, PDO::PARAM_STR);
$query->execute();
$recharge_address = $query->fetch(PDO::FETCH_ASSOC);

// 如果没有记录，则证明没有当前地址，则直接返回成功
if (!$recharge_address) {
    exit('SUCCESS');
}

// 如果都通过，则做业务处理

// TODO 事务处理开始

// 如果有这条记录，就修改这个充值记录
if ($recharge) {
    // 修改状态
} else{
    // 没有则增加 recharge 表记录
}

// 增加用户余额

// TODO 事务处理结束

$pdo = null;
// 返回成功
exit('SUCCESS');
