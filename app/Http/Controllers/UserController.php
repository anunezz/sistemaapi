<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\PermissionsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use PermissionsTrait;

    public function index(Request $filter)
    {
        return  $this->hasPermission('user_management') ? (new UserService())->index($filter->all()) : $this->responseDenied();
    }

    public function search(Request $request)
    {
        return  $this->hasPermission('user_management') ? (new UserService())->search($request->all()) : $this->responseDenied();
    }

    public function store(StoreUserRequest $request)
    {
        return  $this->hasPermission('user_management') ? (new UserService())->store($request->validated()) : $this->responseDenied();
    }

    public function show($id)
    {
        return  $this->hasPermission('user_management') ? (new UserService())->show($id) : $this->responseDenied();
    }

    public function edit($id)
    {
        return $this->hasPermission('user_management') ?  (new UserService())->edit($id) : $this->responseDenied();
    }

    public function update($id, UpdateUserRequest $request)
    {
        return  $this->hasPermission('user_management') ? (new UserService())->update($id, $request->all()) : $this->responseDenied();
    }

    public function changeStatus($id)
    {
        return  $this->hasPermission('user_management') ? (new UserService())->changeStatus($id) : $this->responseDenied();
    }
    public function catUsers()
    {
        try{
                return response()->json([
                    'success' => true,
                    'Results' => User::get()
                ], 200);
        
        } catch (\Exception $e) {
        
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line'    => $e->getline(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
            ], 300);
        }
    }



}
