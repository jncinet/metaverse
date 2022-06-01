<?php

namespace Jncinet\Metaverse\Controllers\Admin;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Jncinet\Metaverse\Models\MetaverseUserLevel;

class UserLevelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户等级管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MetaverseUserLevel());

        $grid->model()->orderBy('sort', 'desc')->latest();

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('name', __('metaverse::user-level.name'));
            $filter->between('power', __('metaverse::user-level.power'));
        });

        $grid->column('id', 'ID');
        $grid->column('name', __('metaverse::user-level.name'));
        $grid->column('power', __('metaverse::user-level.power'));
        $grid->column('subordinates_number', __('metaverse::user-level.subordinates_number'));
        $grid->column('gas_fee_rate', __('metaverse::user-level.gas_fee_rate'))->suffix('%');

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
        $show = new Show(MetaverseUserLevel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('name', __('metaverse::user-level.name'));
        $show->field('power', __('metaverse::user-level.power'));
        $show->field('subordinates_number', __('metaverse::user-level.subordinates_number'));
        $show->field('sort', __('metaverse::user-level.sort'));
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
        $form = new Form(new MetaverseUserLevel());

        $form->text('name', __('metaverse::user-level.name'));
        $form->decimal('power', __('metaverse::user-level.power'))
            ->default(0)
            ->help('最多四位小数');
        $form->number('subordinates_number', __('metaverse::user-level.subordinates_number'))
            ->default(0);
        $form->rate('gas_fee_rate', __('metaverse::user-level.gas_fee_rate'))
            ->default(0);
        $form->number('sort', __('metaverse::user-level.sort'))
            ->default(0);

        return $form;
    }
}
