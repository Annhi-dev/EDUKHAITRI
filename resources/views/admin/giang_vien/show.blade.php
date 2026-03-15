@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex items-center">
        <a href="{{ route('admin.giang_vien.index') }}" class="text-purple-600 hover:text-purple-800 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Chi tiết Giảng viên</h2>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('admin.giang_vien.edit', $giangVien->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg transition duration-150 shadow-md">
            Chỉnh sửa
        </a>
        <form action="{{ route('admin.giang_vien.reset_password', $giangVien->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg transition duration-150 shadow-md">
                Reset mật khẩu
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Cột trái: Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-purple-600 h-24"></div>
            <div class="px-6 pb-6 text-center">
                <div class="relative -mt-12 inline-block">
                    <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-md mx-auto" src="{{ $giangVien->avatar ? asset('storage/'.$giangVien->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($giangVien->name).'&size=128' }}" alt="{{ $giangVien->name }}">
                    <span class="absolute bottom-1 right-1 h-5 w-5 rounded-full border-2 border-white {{ $giangVien->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                </div>
                <h3 class="mt-4 text-xl font-bold text-gray-900">{{ $giangVien->name }}</h3>
                <p class="text-purple-600 font-mono font-bold">{{ $giangVien->giangVienProfile->ma_giang_vien }}</p>
                <div class="mt-4 flex justify-center space-x-2">
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">{{ $giangVien->giangVienProfile->hoc_vi ?? 'N/A' }}</span>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">{{ $giangVien->giangVienProfile->trang_thai == 'dang_day' ? 'Đang dạy' : ($giangVien->giangVienProfile->trang_thai == 'nghi_phep' ? 'Nghỉ phép' : 'Đã nghỉ') }}</span>
                </div>
            </div>
            <div class="border-t border-gray-100 px-6 py-4">
                <div class="space-y-3">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        {{ $giangVien->email }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        {{ $giangVien->phone ?? 'Chưa cập nhật' }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $giangVien->giangVienProfile->dia_chi ?? 'Chưa cập nhật' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột phải: Tabs & Information -->
    <div class="lg:col-span-2">
        <div x-data="{ tab: 'personal' }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex border-b border-gray-100">
                <button @click="tab = 'personal'" :class="{ 'border-purple-600 text-purple-600 bg-purple-50': tab === 'personal' }" class="px-6 py-4 text-sm font-bold border-b-2 border-transparent hover:bg-gray-50 transition duration-150">Thông tin cá nhân</button>
                <button @click="tab = 'schedule'" :class="{ 'border-purple-600 text-purple-600 bg-purple-50': tab === 'schedule' }" class="px-6 py-4 text-sm font-bold border-b-2 border-transparent hover:bg-gray-50 transition duration-150">Lịch dạy</button>
                <button @click="tab = 'classes'" :class="{ 'border-purple-600 text-purple-600 bg-purple-50': tab === 'classes' }" class="px-6 py-4 text-sm font-bold border-b-2 border-transparent hover:bg-gray-50 transition duration-150">Lớp phụ trách</button>
            </div>

            <div class="p-6">
                <!-- Tab: Thông tin cá nhân -->
                <div x-show="tab === 'personal'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Chuyên môn</h4>
                            <p class="text-gray-900 font-medium">{{ $giangVien->giangVienProfile->chuyen_mon ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Học vị</h4>
                            <p class="text-gray-900 font-medium">{{ $giangVien->giangVienProfile->hoc_vi ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Ngày sinh</h4>
                            <p class="text-gray-900 font-medium">{{ $giangVien->giangVienProfile->ngay_sinh ? \Carbon\Carbon::parse($giangVien->giangVienProfile->ngay_sinh)->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Giới tính</h4>
                            <p class="text-gray-900 font-medium">{{ ucfirst($giangVien->giangVienProfile->gioi_tinh ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Số CMND/CCCD</h4>
                            <p class="text-gray-900 font-medium">{{ $giangVien->giangVienProfile->so_cmnd ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Ngày vào làm</h4>
                            <p class="text-gray-900 font-medium">{{ $giangVien->giangVienProfile->ngay_vao_lam ? \Carbon\Carbon::parse($giangVien->giangVienProfile->ngay_vao_lam)->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tab: Lịch dạy -->
                <div x-show="tab === 'schedule'" class="text-center py-10 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Chưa có lịch dạy trong hệ thống.
                </div>

                <!-- Tab: Lớp phụ trách -->
                <div x-show="tab === 'classes'" class="text-center py-10 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Giảng viên chưa phụ trách lớp nào.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
