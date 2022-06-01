<h1 align="center">矿场管理</h1>

## 安装
```shell
$ composer require jncinet/metaverse
```

## 开始
### 数据迁移
```shell
$ php artisan migrate
```

### 发布资源
```shell
$ php artisan vendor:publish --provider="Jncinet\Metaverse\MetaverseServiceProvider"
```

### 后台菜单
+ 矿机列表：`metaverse/machines`
+ 团队等级：`metaverse/team-levels`
+ 用户等级：`metaverse/user-levels`
+ 质押订单：`metaverse/powers`
+ 奖励记录：`metaverse/rewards`
+ 矿池记录：`metaverse/mains`
+ 兑出记录：`metaverse/exchanges`
+ 排行榜：`metaverse/rankings`
+ 模块设置：`metaverse/config`

## 任务调度
在`网站目录/app/Console/Kernel.php`文件中`schedule`方法里添加
```php
use Jncinet\Metaverse\Jobs\MainReward;
// 每天2:00执行一次
$schedule->job(new MainReward)->dailyAt('02:00');
```
并保持队列正在运行中
```shell
$ php artisan queue:work
```
