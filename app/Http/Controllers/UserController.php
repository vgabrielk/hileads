<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Você não pode deletar sua própria conta!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário removido com sucesso!');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Você não pode desativar sua própria conta!');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'ativado' : 'desativado';
        
        return redirect()->back()
            ->with('success', "Usuário {$status} com sucesso!");
    }

    /**
     * Regenerate user API token.
     */
    public function regenerateToken(User $user)
    {
        $token = $user->generateApiToken();

        return redirect()->back()
            ->with('success', 'Token regenerado com sucesso!')
            ->with('new_token', $token);
    }
}
