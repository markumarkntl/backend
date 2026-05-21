<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET /users — hanya admin
    public function index()
    {
        $users = User::select('id', 'name', 'nip', 'role', 'is_active', 'created_at')
            ->orderBy('name')->get();

        return response()->json(['success' => true, 'data' => $users]);
    }

    // POST /users
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'nip'      => 'required|string|unique:users,nip',
            'password' => 'required|string|min:4',
            'role'     => 'required|in:admin,kasir',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'nip'      => $request->nip,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'is_active'=> true,
        ]);

        return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan.', 'data' => $user], 201);
    }

    // PUT /users/{id}
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'      => 'sometimes|string|max:100',
            'nip'       => 'sometimes|string|unique:users,nip,' . $id,
            'password'  => 'sometimes|string|min:4',
            'role'      => 'sometimes|in:admin,kasir',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->only(['name', 'nip', 'role', 'is_active']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['success' => true, 'message' => 'User berhasil diperbarui.', 'data' => $user->fresh()]);
    }

    // DELETE /users/{id}
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Soft delete: nonaktifkan saja
        $user->update(['is_active' => false]);

        return response()->json(['success' => true, 'message' => 'User berhasil dinonaktifkan.']);
    }
}