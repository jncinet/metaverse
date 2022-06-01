<?php

namespace Jncinet\Metaverse\Controllers\Admin;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Jncinet\Metaverse\Models\MetaverseTeamLevel;

class TeamLevelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '团队等级管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MetaverseTeamLevel());

        $grid->model()->orderBy('sort', 'desc')->latest();

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('name', __('metaverse::team-level.name'));
            $filter->between('big_power', __('metaverse::team-level.big_power'));
            $filter->between('small_power', __('metaverse::team-level.small_power'));
            $filter->between('main_reward_rate', __('metaverse::team-level.main_reward_rate'));
        });

        $grid->column('id', 'ID');
        $grid->column('name', __('metaverse::team-level.name'));
        $grid->column('big_power', __('metaverse::team-level.big_power'));
        $grid->column('small_power', __('metaverse::team-level.small_power'));
        $grid->column('main_reward_rate', __('metaverse::team-level.main_reward_rate'))->suffix('%');

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
        $show = new Show(MetaverseTeamLevel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('name', __('metaverse::team-level.name'));
        $show->field('big_power', __('metaverse::team-level.big_power'));
        $show->field('small_power', __('metaverse::team-level.small_power'));
        $show->field('main_reward_rate', __('metaverse::team-level.main_reward_rate'));
        $show->field('sort', __('metaverse::team-level.sort'));
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
        $form = new Form(new MetaverseTeamLevel());

        $form->text('name', __('metaverse::team-level.name'));
        $form->decimal('big_power', __('metaverse::team-level.big_power'))
            ->default(0)
            ->help('最多四位小数');
        $form->decimal('small_power', __('metaverse::team-level.small_power'))
            ->default(0)
            ->help('最多四位小数');
        $form->rate('main_reward_rate', __('metaverse::team-level.main_reward_rate'));
        $form->number('sort', __('metaverse::team-level.sort'))
            ->default(0);

        return $form;
    }
}
