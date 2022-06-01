<?php

namespace Jncinet\Metaverse\Controllers\Admin;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Jncinet\Metaverse\Models\MetaverseMachine;

class MachineController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '矿机管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MetaverseMachine());

        $grid->model()->orderBy('sort', 'desc')->latest();

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('name', __('metaverse::machine.name'));
            $filter->between('count', __('metaverse::machine.count'));
            $filter->between('price', __('metaverse::machine.price'));
            $filter->between('power', __('metaverse::machine.power'));
            $filter->equal('status', __('metaverse::machine.status'))
                ->radio([
                    '' => '不限',
                    0 => __('metaverse::machine.status_value.0'),
                    1 => __('metaverse::machine.status_value.1'),
                ]);
        });

        $grid->column('id', 'ID');
        $grid->column('name', __('metaverse::machine.name'));
        $grid->column('count', __('metaverse::machine.count'));
        $grid->column('price', __('metaverse::machine.price'));
        $grid->column('power', __('metaverse::machine.power'));
        $grid->column('status', __('metaverse::machine.status'))
            ->using(__('metaverse::machine.status_value'));

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
        $show = new Show(MetaverseMachine::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('name', __('metaverse::machine.name'));
        $show->field('count', __('metaverse::machine.count'));
        $show->field('price', __('metaverse::machine.price'));
        $show->field('power', __('metaverse::machine.power'));
        $show->field('status', __('metaverse::machine.status'))
            ->using(__('metaverse::machine.status_value'));
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
        $form = new Form(new MetaverseMachine());

        $form->text('name', __('metaverse::machine.name'));
        $form->number('count', __('metaverse::machine.count'))
            ->default(30);
        $form->decimal('price', __('metaverse::machine.price'))
            ->default(0)
            ->help('最多二位小数');
        $form->decimal('power', __('metaverse::machine.power'))
            ->default(0)
            ->help('最多四位小数');
        $form->radio('status', __('metaverse::machine.status'))
            ->default(1)
            ->options(__('metaverse::machine.status_value'));
        $form->number('sort', __('metaverse::machine.sort'))
            ->default(0);

        return $form;
    }
}
