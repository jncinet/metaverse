<?php

namespace Jncinet\Metaverse\Controllers\Admin;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Jncinet\Metaverse\Models\MetaverseReward;

class RewardController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '奖励记录管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MetaverseReward());

        $grid->model()->orderBy('id', 'desc')->latest();

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->where(function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('username', 'like', "%{$this->input}%");
                });
            }, __('metaverse::reward.user_id'));
            $filter->equal('type', __('metaverse::reward.type'))
                ->select(__('metaverse::reward.type_value'));
            $filter->between('amount', __('metaverse::reward.amount'));
            $filter->equal('status', __('metaverse::reward.status'))
                ->select(__('metaverse::reward.status_value'));
        });

        $grid->column('id', 'ID');
        $grid->column('type', __('metaverse::reward.type'))
            ->using(__('metaverse::reward.type_value'));
        $grid->column('user.username', __('metaverse::reward.user_id'));
        $grid->column('amount', __('metaverse::reward.amount'));
        $grid->column('status', __('metaverse::reward.status'))
            ->using(__('metaverse::reward.status_value'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(MetaverseReward::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('user.username', __('metaverse::reward.user_id'));
        $show->field('metaverse_power_id', __('metaverse::reward.metaverse_power_id'));
        $show->field('metaverse_main_id', __('metaverse::reward.metaverse_main_id'));
        $show->field('metaverse_reward_id', __('metaverse::reward.metaverse_reward_id'));
        $show->field('type', __('metaverse::reward.type'))
            ->using(__('metaverse::reward.type_value'));
        $show->field('amount', __('metaverse::reward.amount'));
        $show->field('status', __('metaverse::reward.status'))
            ->using(__('metaverse::reward.status_value'));

        $show->field('created_at', __('admin.created_at'));
        $show->field('updated_at', __('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MetaverseReward());

        $form->select('user_id', __('metaverse::reward.user_id'))
            ->options(function ($id) {
                $user = User::find($id);
                if ($user) {
                    return [$user->id => $user->username];
                }
            })->ajax(route('wap.metaverse.user.select'));
        $form->select('type', __('metaverse::reward.type'))
            ->options(__('metaverse::reward.type_value'));
        $form->number('metaverse_power_id', __('metaverse::reward.metaverse_power_id'))
            ->default(0);
        $form->number('metaverse_main_id', __('metaverse::reward.metaverse_main_id'))
            ->default(0);
        $form->number('metaverse_reward_id', __('metaverse::reward.metaverse_reward_id'))
            ->default(0);
        $form->decimal('amount', __('metaverse::reward.amount'))
            ->default(0);
        $form->select('status', __('metaverse::reward.status'))
            ->options(__('metaverse::reward.status_value'));

        return $form;
    }
}
