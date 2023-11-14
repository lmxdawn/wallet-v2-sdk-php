<?php

require_once './config.php';

// 封装 cURL 请求函数
function sendCurlRequest($url, $data) {
    global $appid, $secretKey;

    $data["appid"] = $appid;
    $data["secretKey"] = $secretKey;
    // 添加签名字段
    $data['sign'] = sign($data);

    // 如果有该值则删除
    if (isset($data['secret_key'])) {
        unset($data['secret_key']);
    }

    // 初始化 cURL
    $ch = curl_init($url);

    // 设置 cURL 选项
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // 设置请求头为 JSON 格式
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // 执行 cURL 请求
    $response = curl_exec($ch);

    // 检查请求是否成功
    if ($response === false) {
        die('cURL 请求失败: ' . curl_error($ch));
    }

    // 关闭 cURL 资源
    curl_close($ch);

    // 返回响应
    return $response;
}

// Sign 签名
function sign($data) {

    $keys = array_keys($data);
    sort($keys);
    $str = "";
    foreach ($keys as $key) {
        if ($key != "sign") {
            $str .= $data[$key];
        }
    }
    return md5($str);
}

// VerifySign 验证签名
function verifySign($data) {
    $signNew = sign($data);
    if (!isset($data["sign"])) {
        return false;
    }

    $sign = $data["sign"];
    return $sign == $signNew;
}



