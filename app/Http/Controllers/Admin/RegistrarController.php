<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registrar;
use Illuminate\Http\Request;

class RegistrarController extends Controller
{
    public function index()
    {
        $registrars = Registrar::with('user:id,first_name,middle_name,last_name,school_email')
                              ->orderBy('window_number', 'asc')
                              ->paginate(15);
        return view('admin.registrars.index', compact('registrars'));
    }

    // Other actions (create/edit/destroy) can be added later.
}
