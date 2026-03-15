<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GiangVienProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GiangVienController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('giang_vien')->with('giangVienProfile');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhereHas('giangVienProfile', function($pq) use ($search) {
                      $pq->where('ma_giang_vien', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('trang_thai')) {
            $trangThai = $request->trang_thai;
            $query->whereHas('giangVienProfile', function($q) use ($trangThai) {
                $q->where('trang_thai', $trangThai);
            });
        }

        $giangViens = $query->paginate(15);
        $filters = $request->only(['search', 'trang_thai']);

        return view('admin.giang_vien.index', compact('giangViens', 'filters'));
    }

    public function create()
    {
        return view('admin.giang_vien.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|digits:10',
            'avatar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'ma_giang_vien' => 'required|unique:giang_vien_profiles',
            'chuyen_mon' => 'nullable|string',
            'hoc_vi' => 'nullable|string',
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|in:nam,nu,khac',
            'dia_chi' => 'nullable|string',
            'ngay_vao_lam' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request) {
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'avatar' => $avatarPath,
                'role' => 'giang_vien',
            ]);

            $user->assignRole('giang_vien');

            GiangVienProfile::create([
                'user_id' => $user->id,
                'ma_giang_vien' => $request->ma_giang_vien,
                'chuyen_mon' => $request->chuyen_mon,
                'hoc_vi' => $request->hoc_vi,
                'ngay_sinh' => $request->ngay_sinh,
                'gioi_tinh' => $request->gioi_tinh,
                'dia_chi' => $request->dia_chi,
                'ngay_vao_lam' => $request->ngay_vao_lam,
                'trang_thai' => 'dang_day',
            ]);
        });

        return redirect()->route('admin.giang_vien.index')->with('success', 'Thêm giảng viên thành công!');
    }

    public function show($id)
    {
        $giangVien = User::with('giangVienProfile')->findOrFail($id);
        return view('admin.giang_vien.show', compact('giangVien'));
    }

    public function edit($id)
    {
        $giangVien = User::with('giangVienProfile')->findOrFail($id);
        return view('admin.giang_vien.edit', compact('giangVien'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $profile = $user->giangVienProfile;

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
            'phone' => 'nullable|digits:10',
            'avatar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'ma_giang_vien' => ['required', Rule::unique('giang_vien_profiles')->ignore($profile->id)],
            'chuyen_mon' => 'nullable|string',
            'hoc_vi' => 'nullable|string',
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|in:nam,nu,khac',
            'dia_chi' => 'nullable|string',
            'ngay_vao_lam' => 'nullable|date',
            'trang_thai' => 'required|in:dang_day,nghi_phep,da_nghi',
        ]);

        DB::transaction(function () use ($request, $user, $profile) {
            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            $profile->update([
                'ma_giang_vien' => $request->ma_giang_vien,
                'chuyen_mon' => $request->chuyen_mon,
                'hoc_vi' => $request->hoc_vi,
                'ngay_sinh' => $request->ngay_sinh,
                'gioi_tinh' => $request->gioi_tinh,
                'dia_chi' => $request->dia_chi,
                'ngay_vao_lam' => $request->ngay_vao_lam,
                'trang_thai' => $request->trang_thai,
            ]);
        });

        return redirect()->route('admin.giang_vien.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // TODO: Kiểm tra lớp học đang dạy sau này
        
        $user->is_active = false;
        $user->save();

        return redirect()->back()->with('success', 'Đã tạm ngừng hoạt động của giảng viên.');
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make('password123');
        $user->save();

        return redirect()->back()->with('success', 'Đã reset mật khẩu về password123!');
    }
}
