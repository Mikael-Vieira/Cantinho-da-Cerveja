<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class EmployeeController extends Controller
{
    /**
     * Exibe o formulário de cadastro de funcionário + lista dos já cadastrados.
     */
    public function create()
    {
        $funcionarios = User::where('role', 'funcionario')->orderBy('name')->get();

        return view('admin.employeesCreate', compact('funcionarios'));
    }

    /**
     * Cadastra um novo funcionário.
     * O role é sempre 'funcionario' — nunca vem do formulário,
     * pra ninguém conseguir se promover a admin por aqui.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'funcionario',
        ]);

        return redirect()->route('employees.create')->with('success', 'Funcionário cadastrado com sucesso!');
    }

    /**
     * Remove o acesso de um funcionário (exclui o usuário).
     */
    public function destroy(User $employee)
    {
        if ($employee->role !== 'funcionario') {
            abort(403, 'Só é possível remover contas de funcionário por aqui.');
        }

        $employee->delete();

        return redirect()->route('employees.create')->with('success', 'Funcionário removido do sistema.');
    }
}
