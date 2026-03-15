@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800">Bảng điều khiển Admin</h2>
    <p class="text-gray-600">Tổng quan hệ thống EDUKHAITRI</p>
</div>

<!-- Thống kê nhanh -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-600">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase">Giảng viên</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalGiangVien }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-600">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase">Học viên</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalHocVien }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-600">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase">Lớp đang dạy</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalLopHoc }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-amber-600">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-amber-100 text-amber-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase">Khóa học</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalKhoaHoc }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Yêu cầu đổi lịch mới nhất -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Yêu cầu đổi lịch chờ duyệt</h3>
            <a href="{{ route('admin.yeu_cau.index') }}" class="text-xs text-purple-600 font-bold hover:underline">Xem tất cả</a>
        </div>
        <div class="p-0">
            <table class="w-full text-left">
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentRequests as $yc)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $yc->giangVien->name }}</div>
                                <div class="text-xs text-gray-500">Lớp: {{ $yc->lichHoc->lopHoc->ma_lop }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <span class="text-red-500 line-through">{{ \Carbon\Carbon::parse($yc->lichHoc->ngay_hoc)->format('d/m/Y') }}</span>
                                <svg class="w-3 h-3 inline mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                <span class="text-green-600 font-bold">{{ \Carbon\Carbon::parse($yc->ngay_muon_doi)->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.yeu_cau.index') }}" class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase">Xử lý</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-6 py-10 text-center text-gray-500 italic text-sm">Không có yêu cầu nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Phím tắt nhanh -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Thao tác nhanh</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('admin.giang_vien.create') }}" class="p-4 border rounded-xl hover:bg-purple-50 hover:border-purple-200 transition text-center group">
                <div class="bg-purple-100 text-purple-600 w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Thêm Giảng viên</span>
            </a>
            <a href="{{ route('admin.hoc_vien.create') }}" class="p-4 border rounded-xl hover:bg-blue-50 hover:border-blue-200 transition text-center group">
                <div class="bg-blue-100 text-blue-600 w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Thêm Học viên</span>
            </a>
            <a href="{{ route('admin.lop_hoc.create') }}" class="p-4 border rounded-xl hover:bg-green-50 hover:border-green-200 transition text-center group">
                <div class="bg-green-100 text-green-600 w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-9-4h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Mở lớp mới</span>
            </a>
            <a href="{{ route('admin.danh_gia.tieu_chi') }}" class="p-4 border rounded-xl hover:bg-amber-50 hover:border-amber-200 transition text-center group">
                <div class="bg-amber-100 text-amber-600 w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Cài đặt Đánh giá</span>
            </a>
        </div>
    </div>
</div>
@endsection
