<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HocVienProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class HocVienController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('hoc_vien')->with('hocVienProfile');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhereHas('hocVienProfile', function($pq) use ($search) {
                      $pq->where('ma_hoc_vien', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('trang_thai')) {
            $trangThai = $request->trang_thai;
            $query->whereHas('hocVienProfile', function($q) use ($trangThai) {
                $q->where('trang_thai', $trangThai);
            });
        }

        $hocViens = $query->paginate(15);
        $filters = $request->only(['search', 'trang_thai']);

        return view('admin.hoc_vien.index', compact('hocViens', 'filters'));
    }

    public function create()
    {
        return view('admin.hoc_vien.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|digits:10',
            'avatar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'ma_hoc_vien' => 'required|unique:hoc_vien_profiles',
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|in:nam,nu,khac',
            'so_cmnd' => 'nullable|string',
            'dia_chi' => 'nullable|string',
            'truong_tot_nghiep' => 'nullable|string',
            'ngay_nhap_hoc' => 'nullable|date',
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
                'role' => 'hoc_vien',
            ]);

            $user->assignRole('hoc_vien');

            HocVienProfile::create([
                'user_id' => $user->id,
                'ma_hoc_vien' => $request->ma_hoc_vien,
                'ngay_sinh' => $request->ngay_sinh,
                'gioi_tinh' => $request->gioi_tinh,
                'so_cmnd' => $request->so_cmnd,
                'dia_chi' => $request->dia_chi,
                'truong_tot_nghiep' => $request->truong_tot_nghiep,
                'ngay_nhap_hoc' => $request->ngay_nhap_hoc,
                'trang_thai' => 'dang_hoc',
            ]);
        });

        return redirect()->route('admin.hoc_vien.index')->with('success', 'Thêm học viên thành công!');
    }

    public function show($id)
    {
        $hocVien = User::with('hocVienProfile')->findOrFail($id);
        return view('admin.hoc_vien.show', compact('hocVien'));
    }

    public function edit($id)
    {
        $hocVien = User::with('hocVienProfile')->findOrFail($id);
        return view('admin.hoc_vien.edit', compact('hocVien'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $profile = $user->hocVienProfile;

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
            'phone' => 'nullable|digits:10',
            'avatar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'ma_hoc_vien' => ['required', Rule::unique('hoc_vien_profiles')->ignore($profile->id)],
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|in:nam,nu,khac',
            'so_cmnd' => 'nullable|string',
            'dia_chi' => 'nullable|string',
            'truong_tot_nghiep' => 'nullable|string',
            'ngay_nhap_hoc' => 'nullable|date',
            'trang_thai' => 'required|in:dang_hoc,bao_luu,da_tot_nghiep,da_nghi',
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
                'ma_hoc_vien' => $request->ma_hoc_vien,
                'ngay_sinh' => $request->ngay_sinh,
                'gioi_tinh' => $request->gioi_tinh,
                'so_cmnd' => $request->so_cmnd,
                'dia_chi' => $request->dia_chi,
                'truong_tot_nghiep' => $request->truong_tot_nghiep,
                'ngay_nhap_hoc' => $request->ngay_nhap_hoc,
                'trang_thai' => $request->trang_thai,
            ]);
        });

        return redirect()->route('admin.hoc_vien.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = false;
        $user->save();

        return redirect()->back()->with('success', 'Đã tạm ngừng hoạt động của học viên.');
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
