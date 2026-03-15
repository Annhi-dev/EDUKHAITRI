<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function showGiangVien(): View
    {
        $user = Auth::user();
        $profile = $user->giangVienProfile;
        
        $lopIds = \App\Models\LopHoc::where('giang_vien_id', $user->id)->pluck('id');
        $thongKe = [
            'so_lop' => $lopIds->count(),
            'so_buoi' => \App\Models\LichHoc::whereIn('lop_hoc_id', $lopIds)->where('trang_thai', 'hoan_thanh')->count(),
            'so_hv' => \Illuminate\Support\Facades\DB::table('hoc_vien_lop_hocs')
                ->whereIn('lop_hoc_id', $lopIds)
                ->where('trang_thai', 'dang_hoc')
                ->count(),
        ];
        
        return view('giang_vien.profile.show', compact('user', 'profile', 'thongKe'));
    }

    public function updateGiangVien(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|in:nam,nu,khac',
            'chuyen_mon' => 'nullable|string|max:255',
            'hoc_vi' => 'nullable|string|max:100',
            'dia_chi' => 'nullable|string|max:500',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        $user->giangVienProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'ngay_sinh' => $request->ngay_sinh,
                'gioi_tinh' => $request->gioi_tinh,
                'chuyen_mon' => $request->chuyen_mon,
                'hoc_vi' => $request->hoc_vi,
                'dia_chi' => $request->dia_chi,
            ]
        );

        return Redirect::back()->with('success', 'Đã cập nhật hồ sơ thành công!');
    }

    public function showHocVien(): View
    {
        $user = Auth::user();
        $profile = $user->hocVienProfile;
        
        $lopIds = \App\Models\HocVienLopHoc::where('hoc_vien_id', $user->id)
            ->where('trang_thai', 'dang_hoc')
            ->pluck('lop_hoc_id');

        $thongKe = [
            'so_lop' => $lopIds->count(),
            'diem_tb' => \App\Models\BangDiem::where('hoc_vien_id', $user->id)->avg('diem_trung_binh') ?? 0,
            'chuyen_can' => $this->getTileChuyenCan($user->id, $lopIds),
        ];
        
        return view('hoc_vien.profile.show', compact('user', 'profile', 'thongKe'));
    }

    private function getTileChuyenCan($hvId, $lopIds)
    {
        $tongBuoi = \App\Models\LichHoc::whereIn('lop_hoc_id', $lopIds)->where('trang_thai', 'hoan_thanh')->count();
        if ($tongBuoi == 0) return 100;
        
        $coMat = \App\Models\DiemDanh::where('hoc_vien_id', $hvId)
            ->whereIn('trang_thai', ['co_mat', 'di_muon', 've_som'])
            ->whereHas('lichHoc', fn($q) => $q->whereIn('lop_hoc_id', $lopIds))
            ->count();
            
        return round(($coMat / $tongBuoi) * 100);
    }

    public function updateHocVien(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|in:nam,nu,khac',
            'dia_chi' => 'nullable|string|max:500',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        $user->hocVienProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'ngay_sinh' => $request->ngay_sinh,
                'gioi_tinh' => $request->gioi_tinh,
                'dia_chi' => $request->dia_chi,
            ]
        );

        return Redirect::back()->with('success', 'Đã cập nhật hồ sơ thành công!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return Redirect::back()->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
