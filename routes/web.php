<?php

use Illuminate\Routing\Router;

// 手机端
Route::group([
    'prefix' => 'metaverse',
    // 控制器命名空间
    'namespace' => 'Jncinet\Metaverse\Controllers\Wap',
    'middleware' => ['web'],
    'as' => 'wap.metaverse.'
], function (Router $router) {
    // 矿场主页
    $router->get('/', 'HomeController@index')
        ->name('index')
        ->middleware('auth');

    // 矿机列表
    $router->get('/machines', 'MachineController@index')
        ->name('machines.index')
        ->middleware('auth');

    // 订单列表
    $router->get('/powers', 'PowerController@index')
        ->name('powers.index')
        ->middleware('auth');
    // 下单
    $router->post('/powers', 'PowerController@store')
        ->name('powers.store')
        ->middleware('auth');

    // 奖励列表
    $router->get('/rewards', 'RewardController@index')
        ->name('rewards.index')
        ->middleware('auth');
    // 领取当日奖励
    $router->get('/rewards/update', 'RewardController@update')
        ->name('rewards.update')
        ->middleware('auth');

    // 社区
    $router->get('/team', 'TeamController@index')
        ->name('team.index')
        ->middleware('auth');
    $router->get('/team/addition', 'TeamController@addition')
        ->name('team.addition')
        ->middleware('auth');

    // 设置安全密码
    $router->post('/safe-password', 'SafePasswordController@setSafePassword')
        ->middleware('auth')
        ->name('safe.password');
    // 验证安全密码
    $router->post('/verify-safe-password', 'SafePasswordController@verifySafePassword')
        ->middleware('auth')
        ->name('verify.safe.password');

    // 绑定兑出账户
    $router->post('convert/bind-user', 'ConvertController@bindUser')
        ->middleware('auth')
        ->name('convert.bind.user');
    // 兑出
    $router->post('convert', 'ConvertController@index')
        ->middleware('auth')
        ->name('convert');

    /**
     * 后台表单选项
     */
    // 选择矿机
    $router->get('/machines/select', 'MachineController@selectMachine')
        ->name('machines.select');
    // 选择团队等级
    $router->get('/machines/team-level', 'TeamController@selectTeamLevel')
        ->name('team.level.select');
    // 选择会员等级
    $router->get('/machines/user-level', 'ApiController@selectUserLevel')
        ->name('user.level.select');
    // 选择用户
    $router->get('/select/user', 'ApiController@selectUser')
        ->name('user.select');
    // 选择认证项目
    $router->get('/select/authentication', 'MachineController@selectAuthentication')
        ->name('authentication.select');
});

// 后台
Route::group([
    'prefix' => config('admin.route.prefix') . '/metaverse',
    'namespace' => 'Jncinet\Metaverse\Controllers\Admin',
    'middleware' => config('admin.route.middleware'),
    'as' => 'admin.metaverse.'
], function (Router $router) {
    $router->resource('machines', 'MachineController');
    $router->resource('powers', 'PowerController');
    $router->resource('rewards', 'RewardController');
    $router->resource('rankings', 'RankingsController');
    $router->resource('mains', 'MainController');
    $router->resource('exchanges', 'ExchangeController');
    $router->resource('user-levels', 'UserLevelController');
    $router->resource('team-levels', 'TeamLevelController');
    $router->get('config', 'ConfigController@index')->name('config');
});
