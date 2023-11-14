<?php

// 生成充值地址

// 引用数据库连接文件
require_once 'db_connection.php';
require_once 'sign.php';

// 接收参数
$networkName = isset($_GET['network_name']) ? $_GET['network_name'] : null;
$coinSymbol = isset($_GET['coin_symbol']) ? $_GET['coin_symbol'] : null;

// 验证参数
if ($networkName === null || $coinSymbol === null) {
    $response = array('error' => '缺少参数');
    echo json_encode($response);
    exit();
}


// 获取数据库连接
$pdo = connectDB();

// 业务的用户ID
$memberId = "1";
// 回调URL，创建该地址，如果有充值钱包服务就会调用该接口，这个接口地址业务端自己定义，要求不能有其他鉴权，参考微信支付模式
$callUrl = "http://127.0.0.1/recharge_call.php";

// 首先尝试从数据库查询
$query = $pdo->prepare("SELECT address FROM recharge_address WHERE member_id = :member_id AND network_name = :network_name AND coin_symbol = :coin_symbol LIMIT 1");
$query->bindParam(':member_id', $memberId, PDO::PARAM_STR);
$query->bindParam(':network_name', $networkName, PDO::PARAM_STR);
$query->bindParam(':coin_symbol', $coinSymbol, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

// 有值则直接返回
if ($result) {
    // 如果数据库中存在数据，返回地址给调用者
    $response = array('address' => $result['address']);
    echo json_encode($response);
    exit();
}

// 调用钱包服务创建地址的接口
$url = 'http://127.0.0.1:10001/createWallet';
$externalData = array(
    'network_name' => $networkName,
    'coin_symbol' => $coinSymbol,
    'member_id' => $memberId,
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

// 在这里处理外部 API 的响应，假设外部 API 的响应中有 'address' 字段
$externalAddress = $externalData['data']['address'];

// 将外部 API 的地址写入数据库
$insertQuery = $pdo->prepare("INSERT INTO recharge_address (member_id, network_name, coin_symbol, address, create_time, modified_time) VALUES (:member_id, :network_name, :coin_symbol, :address, NOW(), NOW())");
$insertQuery->bindParam(':member_id', $memberId, PDO::PARAM_STR);
$insertQuery->bindParam(':network_name', $networkName, PDO::PARAM_STR);
$insertQuery->bindParam(':coin_symbol', $coinSymbol, PDO::PARAM_STR);
$insertQuery->bindParam(':address', $externalAddress, PDO::PARAM_STR);
$insertQuery->execute();

// 返回地址给调用者
$response = array('address' => $externalAddress);


// 关闭数据库连接
$pdo = null;

// 输出响应
echo json_encode($response);