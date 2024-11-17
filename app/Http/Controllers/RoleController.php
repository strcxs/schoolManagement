<?php

namespace App\Http\Controllers;

use App\Models\role\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $data;
    public function index(){
        $this->data['role'] = Role::get();
        return view('Role.index', $this->data);
    }
}
