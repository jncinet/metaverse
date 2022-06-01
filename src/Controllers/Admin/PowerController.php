<?php

namespace Jncinet\Metaverse\Controllers\Admin;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Jncinet\Metaverse\Models\MetaverseMachine;
use Jncinet\Metaverse\Models\MetaversePower;

class PowerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '质押订单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MetaversePower());

        $grid->model()->orderBy('id', 'desc')->latest();

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->where(function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('username', 'like', "%{$this->input}%");
                });
            }, __('metaverse::power.user_id'));
            $filter->equal('metaverse_machine_id', __('metaverse::power.metaverse_machine_id'))
                ->select(route('wap.metaverse.machines.select'));
            $filter->between('quantity', __('metaverse::power.quantity'));
            $filter->between('remaining_count', __('metaverse::power.remaining_count'));
            $filter->between('total_price', __('metaverse::power.total_price'));
            $filter->between('total_power', __('metaverse::power.total_power'));
        });

        $grid->column('id', 'ID');
        $grid->column('user.username', __('metaverse::power.user_id'));
        $grid->column('metaverse_machine.name', __('metaverse::power.metaverse_machine_id'));
        $grid->column('quantity', __('metaverse::power.quantity'));
        $grid->column('count', __('metaverse::power.count'));
        $grid->column('price', __('metaverse::power.price'));
        $grid->column('power', __('metaverse::power.power'));
        $grid->column('remaining_count', __('metaverse::power.remaining_count'));
        $grid->column('total_price', __('metaverse::power.total_price'));
        $grid->column('total_power', __('metaverse::power.total_power'));

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
        $show = new Show(MetaversePower::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('user.username', __('metaverse::power.user_id'));
        $show->field('metaverse_machine.name', __('metaverse::power.metaverse_machine_id'));
        $show->field('quantity', __('metaverse::power.quantity'));
        $show->field('count', __('metaverse::power.count'));
        $show->field('price', __('metaverse::power.price'));
        $show->field('power', __('metaverse::power.power'));
        $show->field('remaining_count', __('metaverse::power.remaining_count'));
        $show->field('total_price', __('metaverse::power.total_price'));
        $show->field('total_power', __('metaverse::power.total_power'));

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
        $form = new Form(new MetaversePower());

        $form->select('user_id', __('metaverse::power.user_id'))
            ->options(function ($id) {
                $user = User::find($id);
                if ($user) {
                    return [$user->id => $user->username];
                }
            })->ajax(route('wap.metaverse.user.select'));
        $form->select('metaverse_machine_id', __('metaverse::power.metaverse_machine_id'))
            ->options(route('wap.metaverse.machines.select'));
        $form->number('quantity', __('metaverse::power.quantity'))
            ->default(1);

        // 编辑时可编辑所有数据
        if ($form->isEditing()) {
            $form->number('count', __('metaverse::power.count'))
                ->default(30);
            $form->decimal('price', __('metaverse::power.price'))
                ->default(0)
                ->help('最多二位小数');
            $form->decimal('power', __('metaverse::power.power'))
                ->default(0)
                ->help('最多四位小数');
            $form->number('remaining_count', __('metaverse::power.remaining_count'))
                ->default(0);
            $form->decimal('total_price', __('metaverse::power.total_price'))
                ->default(0)
                ->help('最多二位小数');
            $form->decimal('total_power', __('metaverse::power.total_power'))
                ->default(0)
                ->help('最多四位小数');
        } else {
            $form->hidden('count')->default(0);
            $form->hidden('price')->default(0);
            $form->hidden('power')->default(0);
            $form->hidden('remaining_count')->default(0);
            $form->hidden('total_price')->default(0);
            $form->hidden('total_power')->default(0);
        }

        // 创建时
        if ($form->isCreating()) {
            $form->saving(function (Form $form) {
                $machine = MetaverseMachine::select('count', 'price', 'power')
                    ->where('id', $form->metaverse_machine_id)
                    ->first();
                $form->count = $machine->count;
                $form->price = $machine->price;
                $form->power = $machine->power;
                $form->remaining_count = $machine->count;
                $form->total_price = bcmul($form->price, $form->quantity, 2);
                $form->total_power = bcmul($form->power, $form->quantity, 8);
            });
        }

        return $form;
    }
}
