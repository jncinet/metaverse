<?php

return [
    // 矿机产出：1算力产出积分数
    'miner_output' => env('MINER_OUTPUT', 0.3),
    // 今日任务
    'miner_today_tasks' => env('MINER_TODAY_TASKS', ''),
    // 直推奖励
    'miner_recommender_reward' => env('MINER_RECOMMENDER_REWARD', 5),
    // 奖励周期，秒计时
    'miner_cycle_second' => env('MINER_CYCLE_SECOND', 86400),
    // 矿池分红金额
    'miner_main_reward_amount' => env('MINER_MAIN_REWARD_AMOUNT', 0),
    // 矿池团队奖励比例
    'miner_main_reward_team_rate' => env('MINER_MAIN_REWARD_TEAM_RATE', 70),
    // 矿池区域节点奖励比例
    'miner_main_reward_item_rate' => env('MINER_MAIN_REWARD_ITEM_RATE', 20),
    // 矿池销毁比例
    'miner_main_reward_destroy_rate' => env('MINER_MAIN_REWARD_DESTROY_RATE', 10),
    // 接口地址
    'miner_convert_api' => env('MINER_CONVERT_API', ''),
    // 接口KEY
    'miner_convert_api_key' => env('MINER_CONVERT_API_KEY', ''),
    // 需完成认证
    'miner_authentications' => env('MINER_AUTHENTICATIONS', ''),
];
