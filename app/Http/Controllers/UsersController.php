<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    // GET /users
    public function index(Request $request)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        return $this->paginated(User::query(), 'Daftar pengguna', $format);
    }

    // GET /users/{id}
    public function show(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        $user = User::find($id);
        if (!$user) {
            return $this->notFound('Pengguna', $id, $format);
        }

        return $this->respond([
            'message' => 'Detail pengguna',
            'data'    => $user->toArray(),
        ], 200, $format);
    }

    // POST /users
    public function store(Request $request)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        if (!$this->isJsonContent($request)) {
            return response('Unsupported Media Type', 415);
        }

        if ($error = $this->validationFailed($request, $this->rules(), $format)) {
            return $error;
        }

        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),   // di-hash dulu
        ]);

        return $this->respond([
            'message' => 'Pengguna berhasil dibuat',
            'data'    => $user->fresh()->toArray(),
        ], 201, $format);
    }

    // PUT /users/{id}
    public function update(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        if (!$this->isJsonContent($request)) {
            return response('Unsupported Media Type', 415);
        }

        $user = User::find($id);
        if (!$user) {
            return $this->notFound('Pengguna', $id, $format);
        }

        if ($error = $this->validationFailed($request, $this->rules($id), $format)) {
            return $error;
        }

        $data = $request->only(['name', 'email']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));   // di-hash dulu
        }

        $user->update($data);

        return $this->respond([
            'message' => 'Pengguna berhasil diperbarui',
            'data'    => $user->fresh()->toArray(),
        ], 200, $format);
    }

    // DELETE /users/{id}
    public function destroy(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        $user = User::find($id);
        if (!$user) {
            return $this->notFound('Pengguna', $id, $format);
        }

        $user->delete();

        return $this->respond([
            'message' => 'Pengguna berhasil dihapus',
        ], 200, $format);
    }

    // Aturan validasi pengguna (email: id diabaikan saat update; password opsional saat update)
    private function rules($id = null)
    {
        return [
            'name'     => 'required|string|min:3|max:100',
            'email'    => 'required|email|unique:users,email' . ($id ? ',' . $id : ''),
            'password' => $id ? 'nullable|min:6' : 'required|min:6',
        ];
    }
}
