@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex items-center">
        <a href="{{ route('admin.hoc_vien.index') }}" class="text-purple-600 hover:text-purple-800 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Chi tiết Học viên</h2>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('admin.hoc_vien.edit', $hocVien->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg transition duration-150 shadow-md">
            Chỉnh sửa
        </a>
        <form action="{{ route('admin.hoc_vien.reset_password', $hocVien->id) }}" method="POST">
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
                    <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-md mx-auto" src="{{ $hocVien->avatar ? asset('storage/'.$hocVien->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($hocVien->name).'&size=128' }}" alt="{{ $hocVien->name }}">
                    <span class="absolute bottom-1 right-1 h-5 w-5 rounded-full border-2 border-white {{ $hocVien->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                </div>
                <h3 class="mt-4 text-xl font-bold text-gray-900">{{ $hocVien->name }}</h3>
                <p class="text-purple-600 font-mono font-bold">{{ $hocVien->hocVienProfile->ma_hoc_vien }}</p>
                <div class="mt-4 flex justify-center space-x-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">{{ $hocVien->hocVienProfile->trang_thai == 'dang_hoc' ? 'Đang học' : ($hocVien->hocVienProfile->trang_thai == 'bao_luu' ? 'Bảo lưu' : ($hocVien->hocVienProfile->trang_thai == 'da_tot_nghiep' ? 'Đã tốt nghiệp' : 'Đã nghỉ')) }}</span>
                </div>
            </div>
            <div class="border-t border-gray-100 px-6 py-4">
                <div class="space-y-3">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        {{ $hocVien->email }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        {{ $hocVien->phone ?? 'Chưa cập nhật' }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $hocVien->hocVienProfile->dia_chi ?? 'Chưa cập nhật' }}
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
                <button @click="tab = 'learning'" :class="{ 'border-purple-600 text-purple-600 bg-purple-50': tab === 'learning' }" class="px-6 py-4 text-sm font-bold border-b-2 border-transparent hover:bg-gray-50 transition duration-150">Kết quả học tập</button>
                <button @click="tab = 'schedule'" :class="{ 'border-purple-600 text-purple-600 bg-purple-50': tab === 'schedule' }" class="px-6 py-4 text-sm font-bold border-b-2 border-transparent hover:bg-gray-50 transition duration-150">Lịch học</button>
            </div>

            <div class="p-6">
                <!-- Tab: Thông tin cá nhân -->
                <div x-show="tab === 'personal'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Trường tốt nghiệp</h4>
                            <p class="text-gray-900 font-medium">{{ $hocVien->hocVienProfile->truong_tot_nghiep ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Ngày nhập học</h4>
                            <p class="text-gray-900 font-medium">{{ $hocVien->hocVienProfile->ngay_nhap_hoc ? \Carbon\Carbon::parse($hocVien->hocVienProfile->ngay_nhap_hoc)->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Ngày sinh</h4>
                            <p class="text-gray-900 font-medium">{{ $hocVien->hocVienProfile->ngay_sinh ? \Carbon\Carbon::parse($hocVien->hocVienProfile->ngay_sinh)->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Giới tính</h4>
                            <p class="text-gray-900 font-medium">{{ ucfirst($hocVien->hocVienProfile->gioi_tinh ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Số CMND/CCCD</h4>
                            <p class="text-gray-900 font-medium">{{ $hocVien->hocVienProfile->so_cmnd ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tab: Kết quả học tập -->
                <div x-show="tab === 'learning'" class="text-center py-10 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                    Chưa có kết quả học tập.
                </div>

                <!-- Tab: Lịch học -->
                <div x-show="tab === 'schedule'" class="text-center py-10 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Chưa có lịch học trong hệ thống.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
