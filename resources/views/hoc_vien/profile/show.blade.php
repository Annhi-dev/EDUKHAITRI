@extends('layouts.hoc_vien')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cột trái: Avatar & Thống kê -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 text-center relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-full h-24 bg-blue-600"></div>
                
                <div class="relative z-10 mb-6 mt-4">
                    <form action="{{ route('hv.profile.update_hv') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                        @csrf @method('PATCH')
                        <div class="relative inline-block">
                            <img id="avatarPreview" class="h-32 w-32 rounded-[2.5rem] object-cover border-4 border-white shadow-xl mx-auto" src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=2563eb&color=fff&size=256' }}" alt="">
                            <label for="avatarInput" class="absolute bottom-0 right-0 p-2 bg-blue-500 text-white rounded-xl shadow-lg cursor-pointer hover:bg-blue-600 transition duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <input type="file" id="avatarInput" name="avatar" class="hidden" onchange="previewAvatar(this)">
                            </label>
                        </div>
                    </form>
                </div>

                <h3 class="text-xl font-black text-slate-800">{{ $user->name }}</h3>
                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-8">{{ $profile->ma_hoc_vien ?? 'Học viên' }}</p>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Đang học</p>
                        <p class="text-xl font-black text-slate-800">{{ $thongKe['so_lop'] }} lớp</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Điểm TB</p>
                        <p class="text-xl font-black text-slate-800">{{ number_format($thongKe['diem_tb'], 1) }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 col-span-2">
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Tỷ lệ chuyên cần</p>
                        <p class="text-xl font-black text-slate-800">{{ $thongKe['chuyen_can'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Thông tin & Bảo mật -->
        <div class="lg:col-span-2 space-y-6" x-data="{ tab: 'info' }">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="flex border-b border-slate-50">
                    <button @click="tab = 'info'" :class="tab === 'info' ? 'text-blue-600 border-b-2 border-blue-500 bg-blue-50/30' : 'text-slate-400 hover:bg-slate-50'" class="flex-1 py-4 text-xs font-black uppercase tracking-widest transition duration-200">Thông tin cá nhân</button>
                    <button @click="tab = 'password'" :class="tab === 'password' ? 'text-blue-600 border-b-2 border-blue-500 bg-blue-50/30' : 'text-slate-400 hover:bg-slate-50'" class="flex-1 py-4 text-xs font-black uppercase tracking-widest transition duration-200">Đổi mật khẩu</button>
                </div>

                <div class="p-10">
                    <!-- Tab: Thông tin -->
                    <div x-show="tab === 'info'" class="space-y-6">
                        <form action="{{ route('hv.profile.update_hv') }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Họ và tên (*)</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm">
                                </div>
                                <div class="space-y-2 opacity-60">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email (Không thể đổi)</label>
                                    <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-2xl border-slate-200 bg-slate-50 font-bold text-sm cursor-not-allowed">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Số điện thoại</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Ngày sinh</label>
                                    <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh', $profile->ngay_sinh ?? '') }}" class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Giới tính</label>
                                    <select name="gioi_tinh" class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm">
                                        <option value="nam" {{ ($profile->gioi_tinh ?? '') == 'nam' ? 'selected' : '' }}>Nam</option>
                                        <option value="nu" {{ ($profile->gioi_tinh ?? '') == 'nu' ? 'selected' : '' }}>Nữ</option>
                                        <option value="khac" {{ ($profile->gioi_tinh ?? '') == 'khac' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Địa chỉ thường trú</label>
                                    <textarea name="dia_chi" rows="3" class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-medium text-sm">{{ old('dia_chi', $profile->dia_chi ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-10 flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-12 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition shadow-xl shadow-blue-100 hover:scale-105 active:scale-95">
                                    LƯU THAY ĐỔI
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab: Bảo mật -->
                    <div x-show="tab === 'password'" class="space-y-6" style="display:none">
                        <form action="{{ route('hv.profile.password') }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="max-w-md space-y-8">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mật khẩu hiện tại</label>
                                    <input type="password" name="current_password" required class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mật khẩu mới</label>
                                    <input type="password" name="password" required class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Xác nhận mật khẩu mới</label>
                                    <input type="password" name="password_confirmation" required class="w-full rounded-2xl border-slate-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm">
                                </div>
                                
                                <div class="pt-4">
                                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition shadow-xl shadow-slate-200 hover:scale-105 active:scale-95">
                                        CẬP NHẬT MẬT KHẨU
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
                document.getElementById('avatarForm').submit();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
