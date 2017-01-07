<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/20
 * Time: 9:18
 */

namespace App\Http\Controllers\AlipayOpen;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsersController extends AlipayOpenController
{

    public function users()
    {
        $auth = Auth::user()->can('users');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $user = User::all();
        if ($user) {
            $user = $user->toArray();
        }
        return view('admin.alipayopen.users.users', compact("user"));
    }

    //添加用户
    public function useradd(Request $request)
    {
        $data = $request->all();
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|min:11|max:11',
        ];
        $messages = [
            'required' => '密码不能为空',
            'between' => '密码必须是6~20位之间',
            'confirmed' => '新密码和确认密码不匹配'
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
        ]);
        return redirect('/admin/alipayopen/users');
    }

    public function updateu(Request $request)
    {
        $auth = Auth::user()->can('users');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $user = User::where('id', $request->get('id'))->first();
        if ($user) {
            $user = $user->toArray();
        }
        return view('admin.alipayopen.users.updateu', compact('user'));
    }

    //admin  删除账号
    public function deleteu(Request $request)
    {
        $auth = Auth::user()->can('users');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $user = User::where('id', $request->get('id'))->first();
        if ($user->name != "admin") {
            $user->delete();
        }
        return json_encode(['status' => 1]);
    }

    //admin修改账号信息
    public function updateuSave(Request $request)
    {
        $auth = Auth::user()->can('users');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $email = $request->get('email');
        $user = User::where('email', $email)->first();
        $password_confirm = $request->input('password_confirm');
        $password = $request->input('password');
        //有密码的话修改密码
        if ($password_confirm || $password) {
            $data = $request->all();
            $rules = [
                'password' => 'required|between:6,20|confirmed',
            ];
            $messages = [
                'required' => '密码不能为空',
                'between' => '密码必须是6~20位之间',
                'confirmed' => '新密码和确认密码不匹配'
            ];
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                return back()->withErrors($validator);  //返回一次性错误
            }
            $user->password = bcrypt($password);
            $user->save();
            return redirect(route('users'));
        } //没有密码 跳过验证修改其他信息
        else {
            $user->name = $request->get('name');
            $user->phone = $request->get('phone');
            $user->email = $request->get('email');
            $user->save();
            return redirect(route('users'));
        }
    }
}