<?php

namespace Jncinet\Metaverse\Controllers\Admin;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Jncinet\Metaverse\Models\MetaverseMain;

class MainController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '矿池记录管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MetaverseMain());

        $grid->model()->orderBy('id', 'desc')->latest();

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->where(function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('username', 'like', "%{$this->input}%");
                });
            }, __('metaverse::main.user_id'));
            $filter->equal('type', __('metaverse::main.type'))
                ->select(__('metaverse::main.type_value'));
            $filter->equal('metaverse_exchange_id', __('metaverse::main.metaverse_exchange_id'));
            $filter->between('amount', __('metaverse::main.amount'));
        });

        $grid->column('id', 'ID');
        $grid->column('type', __('metaverse::main.type'))
            ->using(__('metaverse::main.type_value'));
        $grid->column('user.username', __('metaverse::main.user_id'));
        $grid->column('metaverse_exchange_id', __('metaverse::main.metaverse_exchange_id'));
        $grid->column('amount', __('metaverse::main.amount'));

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
        $show = new Show(MetaverseMain::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('user.username', __('metaverse::main.user_id'));
        $show->field('metaverse_exchange_id', __('metaverse::main.metaverse_exchange_id'));
        $show->field('type', __('metaverse::main.type'))
            ->using(__('metaverse::main.type_value'));
        $show->field('amount', __('metaverse::main.amount'));

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
        $form = new Form(new MetaverseMain());

        $form->select('user_id', __('metaverse::main.user_id'))
            ->options(function ($id) {
                $user = User::find($id);
                if ($user) {
                    return [$user->id => $user->username];
                }
            })->ajax(route('wap.metaverse.user.select'));
        $form->select('type', __('metaverse::main.type'))
            ->options(__('metaverse::main.type_value'));
        $form->number('metaverse_exchange_id', __('metaverse::main.metaverse_exchange_id'))
            ->default(0);
        $form->decimal('amount', __('metaverse::main.amount'))
            ->default(0);

        $form->saving(function (Form $form) {
            if (empty($form->user_id)) {
                $form->user_id = 0;
            }
        });

        return $form;
    }
}
