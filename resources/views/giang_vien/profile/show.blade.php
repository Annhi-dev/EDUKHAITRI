@extends('layouts.giang_vien')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cột trái: Avatar & Thống kê -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 text-center relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-full h-24 bg-emerald-600"></div>
                
                <div class="relative z-10 mb-6 mt-4">
                    <form action="{{ route('gv.profile.update_gv') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                        @csrf @method('PATCH')
                        <div class="relative inline-block">
                            <img id="avatarPreview" class="h-32 w-32 rounded-[2.5rem] object-cover border-4 border-white shadow-xl mx-auto" src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=10b981&color=fff&size=256' }}" alt="">
                            <label for="avatarInput" class="absolute bottom-0 right-0 p-2 bg-emerald-500 text-white rounded-xl shadow-lg cursor-pointer hover:bg-emerald-600 transition duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <input type="file" id="avatarInput" name="avatar" class="hidden" onchange="previewAvatar(this)">
                            </label>
                        </div>
                    </form>
                </div>

                <h3 class="text-xl font-black text-slate-800">{{ $user->name }}</h3>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mb-8">{{ $profile->chuyen_mon ?? 'Giảng viên' }}</p>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Số lớp</p>
                        <p class="text-xl font-black text-slate-800">{{ $thongKe['so_lop'] }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Học viên</p>
                        <p class="text-xl font-black text-slate-800">{{ $thongKe['so_hv'] }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 col-span-2">
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Buổi đã giảng dạy</p>
                        <p class="text-xl font-black text-slate-800">{{ $thongKe['so_buoi'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Thông tin & Bảo mật -->
        <div class="lg:col-span-2 space-y-6" x-data="{ tab: 'info' }">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="flex border-b border-slate-50">
                    <button @click="tab = 'info'" :class="tab === 'info' ? 'text-emerald-600 border-b-2 border-emerald-500 bg-emerald-50/30' : 'text-slate-400 hover:bg-slate-50'" class="flex-1 py-4 text-sm font-black uppercase tracking-widest transition duration-200">Thông tin cá nhân</button>
                    <button @click="tab = 'password'" :class="tab === 'password' ? 'text-emerald-600 border-b-2 border-emerald-500 bg-emerald-50/30' : 'text-slate-400 hover:bg-slate-50'" class="flex-1 py-4 text-sm font-black uppercase tracking-widest transition duration-200">Đổi mật khẩu</button>
                </div>

                <div class="p-8">
                    <!-- Tab: Thông tin -->
                    <div x-show="tab === 'info'" class="space-y-6">
                        <form action="{{ route('gv.profile.update_gv') }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Họ và tên (*)</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                                </div>
                                <div class="space-y-2 opacity-60">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Email (Không đổi)</label>
                                    <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-2xl border-slate-200 bg-slate-50 font-bold cursor-not-allowed">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Số điện thoại</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Ngày sinh</label>
                                    <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh', $profile->ngay_sinh ?? '') }}" class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Giới tính</label>
                                    <select name="gioi_tinh" class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                                        <option value="nam" {{ ($profile->gioi_tinh ?? '') == 'nam' ? 'selected' : '' }}>Nam</option>
                                        <option value="nu" {{ ($profile->gioi_tinh ?? '') == 'nu' ? 'selected' : '' }}>Nữ</option>
                                        <option value="khac" {{ ($profile->gioi_tinh ?? '') == 'khac' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Học vị</label>
                                    <input type="text" name="hoc_vi" value="{{ old('hoc_vi', $profile->hoc_vi ?? '') }}" placeholder="VD: Thạc sĩ, Tiến sĩ..." class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Chuyên môn</label>
                                    <input type="text" name="chuyen_mon" value="{{ old('chuyen_mon', $profile->chuyen_mon ?? '') }}" placeholder="VD: Lập trình Web, Quản trị mạng..." class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Địa chỉ liên hệ</label>
                                    <textarea name="dia_chi" rows="3" class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-medium">{{ old('dia_chi', $profile->dia_chi ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-10 py-3 rounded-2xl font-black text-sm transition shadow-xl shadow-emerald-100 hover:scale-105 active:scale-95">
                                    LƯU THAY ĐỔI
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab: Bảo mật -->
                    <div x-show="tab === 'password'" class="space-y-6 hidden">
                        <form action="{{ route('gv.profile.password') }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="max-w-md space-y-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Mật khẩu hiện tại</label>
                                    <input type="password" name="current_password" required class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Mật khẩu mới</label>
                                    <input type="password" name="password" required class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">Xác nhận mật khẩu mới</label>
                                    <input type="password" name="password_confirmation" required class="w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                                </div>
                                
                                <div class="pt-4">
                                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white px-10 py-3 rounded-2xl font-black text-sm transition shadow-xl shadow-slate-200 hover:scale-105 active:scale-95">
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
                // Tự động submit form khi chọn ảnh xong
                document.getElementById('avatarForm').submit();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
