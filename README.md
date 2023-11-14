# wallet-v2-sdk-php

> 钱包Pro版本服务，PHP sdk示例
>
>

# 当前系统对接流程

> `config.php` 配置文件
> 
> `wallet-sdk.sql` 导入这个示例sql，数据库结构可自行调整，本系统只是示例

- `recharge_address.php` 获取充值地址
- `recharge_call.php` 钱包服务监听有充值，则调用这个回调
- `withdraw_create.php` 提现时业务系统创建提现记录，并发送一个请求到钱包服务
- `withdraw_call.php` 钱包处理提现逻辑后调用该接口实现业务对接