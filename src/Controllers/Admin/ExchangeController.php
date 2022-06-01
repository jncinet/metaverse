<?php

namespace Jncinet\Metaverse\Controllers\Admin;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Jncinet\Metaverse\Models\MetaverseExchange;

class ExchangeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '兑出记录管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MetaverseExchange());

        $grid->model()->orderBy('id', 'desc')->latest();

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->where(function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('username', 'like', "%{$this->input}%");
                });
            }, __('metaverse::exchange.user_id'));
            $filter->equal('metaverse_user_level_id', __('metaverse::exchange.type'))
                ->select(route('wap.metaverse.team.level.select'));
            $filter->between('amount', __('metaverse::exchange.amount'));
            $filter->between('rate', __('metaverse::exchange.rate'));
            $filter->between('fees', __('metaverse::exchange.fees'));
        });

        $grid->column('id', 'ID');
        $grid->column('user.username', __('metaverse::exchange.user_id'));
        $grid->column('metaverse_user_level.name', __('metaverse::exchange.metaverse_user_level_id'));
        $grid->column('amount', __('metaverse::exchange.amount'));
        $grid->column('rate', __('metaverse::exchange.rate'));
        $grid->column('fees', __('metaverse::exchange.fees'));

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
        $show = new Show(MetaverseExchange::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('user.username', __('metaverse::exchange.user_id'));
        $show->field('metaverse_user_level.name', __('metaverse::exchange.metaverse_user_level_id'));
        $show->field('amount', __('metaverse::exchange.amount'));
        $show->field('rate', __('metaverse::exchange.rate'));
        $show->field('fees', __('metaverse::exchange.fees'));

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
        $form = new Form(new MetaverseExchange());

        $form->select('user_id', __('metaverse::exchange.user_id'))
            ->options(function ($id) {
                $user = User::find($id);
                if ($user) {
                    return [$user->id => $user->username];
                }
            })->ajax(route('wap.metaverse.user.select'));
        $form->select('metaverse_user_level_id', __('metaverse::exchange.metaverse_user_level_id'))
            ->options(route('wap.metaverse.team.level.select'));
        $form->decimal('amount', __('metaverse::exchange.amount'))
            ->default(0);
        $form->decimal('rate', __('metaverse::exchange.rate'))
            ->default(0);
        $form->decimal('fees', __('metaverse::exchange.fees'))
            ->default(0);

        return $form;
    }
}
