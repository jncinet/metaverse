<?php

namespace Jncinet\Metaverse\Controllers\Admin;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Jncinet\Metaverse\Models\MetaverseRanking;

class RankingsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '排行榜';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MetaverseRanking());

        $grid->model()->orderBy('total', 'desc')->orderBy('id', 'desc')->latest();

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->where(function ($query) {
                $query->whereHas('parent', function ($query) {
                    $query->where('username', 'like', "%{$this->input}%");
                });
            }, __('metaverse::ranking.parent_id'));
            $filter->where(function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('username', 'like', "%{$this->input}%");
                });
            }, __('metaverse::ranking.user_id'));
            $filter->equal('metaverse_user_level_id', __('metaverse::ranking.metaverse_user_level_id'))
                ->select(route('wap.metaverse.user.level.select'));
            $filter->equal('metaverse_team_level_id', __('metaverse::ranking.metaverse_team_level_id'))
                ->select(route('wap.metaverse.team.level.select'));
            $filter->between('basic', __('metaverse::ranking.basic'));
            $filter->between('addition', __('metaverse::ranking.addition'));
            $filter->between('total', __('metaverse::ranking.total'));
            $filter->between('big_space', __('metaverse::ranking.big_space'));
            $filter->between('small_space', __('metaverse::ranking.small_space'));
        });

        $grid->column('id', 'ID');
        $grid->column('parent.username', __('metaverse::ranking.parent_id'));
        $grid->column('user.username', __('metaverse::ranking.user_id'));
        $grid->column('metaverse_user_level.name', __('metaverse::ranking.metaverse_user_level_id'));
        $grid->column('metaverse_team_level.name', __('metaverse::ranking.metaverse_team_level_id'));
        $grid->column('basic', __('metaverse::ranking.basic'));
        $grid->column('addition', __('metaverse::ranking.addition'));
        $grid->column('total', __('metaverse::ranking.total'));
        $grid->column('big_space', __('metaverse::ranking.big_space'));
        $grid->column('small_space', __('metaverse::ranking.small_space'));
        $grid->column('api_username', __('metaverse::ranking.api_username'));
        $grid->column('api_user_id', __('metaverse::ranking.api_user_id'));

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
        $show = new Show(MetaverseRanking::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('parent.username', __('metaverse::ranking.parent_id'));
        $show->field('user.username', __('metaverse::ranking.user_id'));
        $show->field('metaverse_power_id', __('metaverse::ranking.metaverse_power_id'));
        $show->field('metaverse_user_level.name', __('metaverse::ranking.metaverse_user_level_id'));
        $show->field('metaverse_team_level.name', __('metaverse::ranking.metaverse_team_level_id'));
        $show->field('basic', __('metaverse::ranking.basic'));
        $show->field('addition', __('metaverse::ranking.addition'));
        $show->field('total', __('metaverse::ranking.total'));
        $show->field('big_space', __('metaverse::ranking.big_space'));
        $show->field('small_space', __('metaverse::ranking.small_space'));
        $show->field('api_username', __('metaverse::ranking.api_username'));
        $show->field('api_user_id', __('metaverse::ranking.api_user_id'));

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
        $form = new Form(new MetaverseRanking());

        $form->select('parent_id', __('metaverse::ranking.parent_id'))
            ->options(function ($id) {
                $user = User::find($id);
                if ($user) {
                    return [$user->id => $user->username];
                }
            })->ajax(route('wap.metaverse.user.select'));
        $form->select('user_id', __('metaverse::ranking.user_id'))
            ->options(function ($id) {
                $user = User::find($id);
                if ($user) {
                    return [$user->id => $user->username];
                }
            })->ajax(route('wap.metaverse.user.select'));
        $form->select('metaverse_user_level_id', __('metaverse::ranking.metaverse_user_level_id'))
            ->options(route('wap.metaverse.user.level.select'));
        $form->select('metaverse_team_level_id', __('metaverse::ranking.metaverse_team_level_id'))
            ->options(route('wap.metaverse.team.level.select'));
        $form->decimal('basic', __('metaverse::ranking.basic'))
            ->default(0);
        $form->decimal('addition', __('metaverse::ranking.addition'))
            ->default(0);
        $form->decimal('total', __('metaverse::ranking.total'))
            ->default(0);
        $form->decimal('big_space', __('metaverse::ranking.big_space'))
            ->default(0);
        $form->decimal('small_space', __('metaverse::ranking.small_space'))
            ->default(0);
        $form->text('api_username', __('metaverse::ranking.api_username'))
            ->default(0);
        $form->text('api_user_id', __('metaverse::ranking.api_user_id'))
            ->default(0);

        return $form;
    }
}
