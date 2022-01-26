<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\UserRequest;
use App\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
    /**
     * Display a listing of the users
     *
     * @param User $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {

//        $users = $model->paginate(15);
//        $users = $model->get_user();

        $usersModel = new \App\User;
        $users = $usersModel->get_user();

//        echo '<pre>';
//        echo 'LINE: ' . __LINE__ . ' Module ' . __CLASS__ . '<br>';
//        var_dump($users);
//        echo '</pre>';
//        exit();

        return view('users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $groups = Group::orderBy('order')->get();
        return view('users.create', compact('groups'));
    }

    /**
     * Store a newly created user in storage
     *
     * @param \App\Http\Requests\UserRequest $request
     * @param User                           $model
     * @return RedirectResponse
     */
    public function store(UserRequest $request, User $model)
    {
        //$model->create($request->merge(['password' => Hash::make($request->get('password'))])->all());
        $model = new User();
        $model->name            = $request->get('name');
        $model->email           = $request->get('email');
        $model->password        = Hash::make($request->get('password'));
        $model->is_admin        = (intval($request->get('user_group')) == 1 ? 1 : 0);
        $model->leads_allowed   = intval($request->get('leads_allowed'));
        $model->time_set_init   = $request->get('time_set_init');
        $model->time_set_final  = $request->get('time_set_final');
        $model->user_group      = intval($request->get('user_group'));
        $model->save();

        return redirect()->route('user.index')->withStatus(__('User successfully created.'));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $groups = Group::orderBy('order')->get();
        return view('users.edit', compact('user', 'groups'));
    }

    /**
     * Update the specified user in storage
     *
     * @param \App\Http\Requests\UserRequest $request
     * @param User                           $user
     * @return RedirectResponse
     */
    public function update(UserRequest $request, User  $user)
    {
        /*
        $user->update(
            $request->merge(['password' => Hash::make($request->get('password'))])
                ->except([$request->get('password') ? '' : 'password']
        ));*/

        $model = $user;
        $model->name = $request->get('name');
        $model->email = $request->get('email');
        if (trim($request->get('password')) != '')
        {
            $model->password = Hash::make($request->get('password'));
        }
        $model->is_admin = (intval($request->get('user_group')) == 5 ? 1 : 0);
        $model->leads_allowed = intval($request->get('leads_allowed'));
        $model->time_set_init = $request->get('time_set_init');
        $model->time_set_final = $request->get('time_set_final');
        $model->user_group = intval($request->get('user_group'));
        $model->save();

        return redirect()->route('user.index')->withStatus(__('User successfully updated.'));
    }

    /**
     * Remove the specified user from storage
     *
     * @param User $user
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(User  $user)
    {
        $user->delete();

        return redirect()->route('user.index')->withStatus(__('User successfully deleted.'));
    }
}
