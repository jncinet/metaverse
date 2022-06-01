<?php

namespace Jncinet\Metaverse\Controllers\Admin;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Qihucms\EditEnv\EditEnv;

class ConfigForm extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '矿场模块设置';

    public function handle(Request $request)
    {
        $data = $request->all();

        if (app(EditEnv::class)->setEnv($data)) {
            admin_success(trans('metaverse::metaverse.processing_succeeded'));
        } else {
            admin_error(trans('metaverse::metaverse.processing_failed'));
        }

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->decimal('miner_output', __('metaverse::config.miner_output'))
            ->default(0.3);
        $this->rate('miner_recommender_reward', __('metaverse::config.miner_recommender_reward'))
            ->default(5);
        $this->text('miner_today_tasks', __('metaverse::config.miner_today_tasks'))
            ->help('多个任务以英文状态下的逗号分隔');
        $this->text('miner_authentications', __('metaverse::config.miner_authentications'))
            ->help('需同时完成多个认证以英文状态下的逗号分隔');
//        $this->number('miner_cycle_second', __('metaverse::config.miner_cycle_second'))
//            ->help('秒计时，1天=86400秒');
        $this->decimal('miner_main_reward_amount', __('metaverse::config.miner_main_reward_amount'))
            ->help('当为-1时使用系统真实收入分红，为0时关闭团队分红，其它数值时使用当前设置的数值分红');
        $this->rate('miner_main_reward_team_rate', __('metaverse::config.miner_main_reward_team_rate'));
        $this->rate('miner_main_reward_item_rate', __('metaverse::config.miner_main_reward_item_rate'));
        $this->rate('miner_main_reward_destroy_rate', __('metaverse::config.miner_main_reward_destroy_rate'));
        $this->url('miner_convert_api', __('metaverse::config.miner_convert_api'));
        $this->text('miner_convert_api_key', __('metaverse::config.miner_convert_api_key'));

    }

    public function data()
    {
        return [
            'miner_output' => config('metaverse.miner_output'),
            'miner_today_tasks' => config('metaverse.miner_today_tasks'),
            'miner_recommender_reward' => config('metaverse.miner_recommender_reward'),
//            'miner_cycle_second' => config('metaverse.miner_cycle_second'),
            'miner_main_reward_amount' => config('metaverse.miner_main_reward_amount'),
            'miner_main_reward_team_rate' => config('metaverse.miner_main_reward_team_rate'),
            'miner_main_reward_item_rate' => config('metaverse.miner_main_reward_item_rate'),
            'miner_main_reward_destroy_rate' => config('metaverse.miner_main_reward_destroy_rate'),
            'miner_convert_api' => config('metaverse.miner_convert_api'),
            'miner_convert_api_key' => config('metaverse.miner_convert_api_key'),
            'miner_authentications' => config('metaverse.miner_authentications'),
        ];
    }
}
