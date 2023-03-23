<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        /** @var Grid */
        $grid = new Grid(new User());

        $grid->disableCreateButton();

        $grid->column('id', __('Id')); /* @phpstan-ignore-line */
        $grid->column('rio_id', __('Rio id')); /* @phpstan-ignore-line */
        $grid->column('username', __('Username')); /* @phpstan-ignore-line */
        $grid->column('email', __('Email')); /* @phpstan-ignore-line */
        $grid->column('password', __('Password')); /* @phpstan-ignore-line */
        $grid->column('remember_token', __('Remember token')); /* @phpstan-ignore-line */
        $grid->column('login_failed', __('Login failed')); /* @phpstan-ignore-line */
        $grid->column('lock', __('Lock')); /* @phpstan-ignore-line */
        $grid->column('last_login', __('Last login')); /* @phpstan-ignore-line */
        $grid->column('secret_question', __('Secret question')); /* @phpstan-ignore-line */
        $grid->column('secret_answer', __('Secret answer')); /* @phpstan-ignore-line */
        $grid->column('two_factor_authentication', __('Two factor authentication')); /* @phpstan-ignore-line */
        $grid->column('created_at', __('Created at')); /* @phpstan-ignore-line */
        $grid->column('updated_at', __('Updated at')); /* @phpstan-ignore-line */
        $grid->column('deleted_at', __('Deleted at')); /* @phpstan-ignore-line */

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
        /** @var User */
        $user = User::findOrFail($id);

        /** @var Show */
        $show = new Show($user);

        $show->field('id', __('Id')); /* @phpstan-ignore-line */
        $show->field('rio_id', __('Rio id')); /* @phpstan-ignore-line */
        $show->field('username', __('Username')); /* @phpstan-ignore-line */
        $show->field('email', __('Email')); /* @phpstan-ignore-line */
        $show->field('password', __('Password')); /* @phpstan-ignore-line */
        $show->field('remember_token', __('Remember token')); /* @phpstan-ignore-line */
        $show->field('login_failed', __('Login failed')); /* @phpstan-ignore-line */
        $show->field('lock', __('Lock')); /* @phpstan-ignore-line */
        $show->field('last_login', __('Last login')); /* @phpstan-ignore-line */
        $show->field('secret_question', __('Secret question')); /* @phpstan-ignore-line */
        $show->field('secret_answer', __('Secret answer')); /* @phpstan-ignore-line */
        $show->field('two_factor_authentication', __('Two factor authentication')); /* @phpstan-ignore-line */
        $show->field('created_at', __('Created at')); /* @phpstan-ignore-line */
        $show->field('updated_at', __('Updated at')); /* @phpstan-ignore-line */
        $show->field('deleted_at', __('Deleted at')); /* @phpstan-ignore-line */

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        /** @var Form */
        $form = new Form(new User());

        $form->number('rio_id', __('Rio id')); /* @phpstan-ignore-line */
        $form->text('username', __('Username')); /* @phpstan-ignore-line */
        $form->email('email', __('Email')); /* @phpstan-ignore-line */
        $form->password('password', __('Password')); /* @phpstan-ignore-line */
        $form->text('remember_token', __('Remember token')); /* @phpstan-ignore-line */
        $form->number('login_failed', __('Login failed')); /* @phpstan-ignore-line */
        $form->number('lock', __('Lock')); /* @phpstan-ignore-line */
        $form->datetime('last_login', __('Last login'))->default(date('Y-m-d H:i:s')); /* @phpstan-ignore-line */
        $form->text('secret_question', __('Secret question')); /* @phpstan-ignore-line */
        $form->text('secret_answer', __('Secret answer')); /* @phpstan-ignore-line */
        $form->number('two_factor_authentication', __('Two factor authentication')); /* @phpstan-ignore-line */

        return $form;
    }
}
