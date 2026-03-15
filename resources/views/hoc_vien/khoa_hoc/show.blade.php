@extends('layouts.hoc_vien')

@section('title', 'Chi tiết khóa học')

@section('content')
<div class="space-y-8">
    <!-- Header Thông tin khóa học -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="h-40 bg-gradient-to-r from-blue-600 to-indigo-700 relative">
            <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
            <div class="absolute -bottom-16 left-12 flex items-end space-x-6">
                <div class="w-32 h-32 bg-white rounded-[2.5rem] shadow-xl flex items-center justify-center p-2 border-4 border-white">
                    <div class="w-full h-full bg-blue-50 rounded-[2rem] flex items-center justify-center text-blue-600">
                        <svg class="w-16 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                </div>
                <div class="mb-4">
                    <h2 class="text-3xl font-black text-slate-800 leading-tight">{{ $lopHoc->ten_lop }}</h2>
                    <p class="text-blue-600 font-black uppercase tracking-widest text-xs mt-1">{{ $lopHoc->khoaHoc->ten_khoa_hoc }}</p>
                </div>
            </div>
        </div>
        <div class="pt-20 pb-8 px-12 flex flex-col md:flex-row justify-between items-end gap-8">
            <div class="flex flex-wrap gap-8">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Mã lớp</p>
                    <p class="text-sm font-black text-slate-700 font-mono">{{ $lopHoc->ma_lop }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Thời gian</p>
                    <p class="text-sm font-black text-slate-700">
                        {{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($lopHoc->ngay_ket_thuc)->format('d/m/y') }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Trạng thái</p>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-widest">
                        {{ str_replace('_', ' ', $lopHoc->trang_thai) }}
                    </span>
                </div>
            </div>
            
            <div class="w-full md:w-64 space-y-2">
                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <span>Tiến độ học tập</span>
                    <span>{{ $tienDo }}%</span>
                </div>
                <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden p-0.5">
                    <div class="h-full bg-blue-600 rounded-full transition-all duration-1000" style="width: {{ $tienDo }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid nội dung -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cột trái: Phím tắt & Kết quả -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Phím tắt nhanh -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('hv.lich_hoc.index', ['lop_id' => $lopHoc->id]) }}" class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg hover:border-blue-200 transition group text-center">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-600">Lịch học</p>
                </a>
                <a href="{{ route('hv.ket_qua.chi_tiet', $lopHoc->id) }}" class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg hover:border-emerald-200 transition group text-center">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-600">Bảng điểm</p>
                </a>
                <a href="{{ route('hv.diem_danh.chi_tiet', $lopHoc->id) }}" class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg hover:border-indigo-200 transition group text-center">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-600">Điểm danh</p>
                </a>
                <a href="{{ route('hv.danh_gia.create', $lopHoc->id) }}" class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg hover:border-amber-200 transition group text-center">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-600">Đánh giá</p>
                </a>
            </div>

            <!-- Tóm tắt kết quả hiện tại -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-8">Kết quả học tập hiện tại</h3>
                @if($bangDiem)
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        @foreach([
                            'CC' => $bangDiem->diem_chuyen_can,
                            'KT1' => $bangDiem->diem_kiem_tra_1,
                            'KT2' => $bangDiem->diem_kiem_tra_2,
                            'GK' => $bangDiem->diem_giua_ky,
                            'CK' => $bangDiem->diem_cuoi_ky
                        ] as $label => $val)
                            <div class="text-center p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase mb-1">{{ $label }}</p>
                                <p class="text-lg font-black {{ $val ? 'text-slate-800' : 'text-slate-300 italic' }}">{{ $val ?? '--' }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8 flex items-center justify-between p-6 bg-blue-50 rounded-[2rem] border border-blue-100">
                        <div>
                            <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Điểm trung bình tạm tính</p>
                            <p class="text-3xl font-black text-blue-700">{{ number_format($bangDiem->diem_trung_binh, 2) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Xếp loại</p>
                            <span class="px-4 py-1.5 bg-blue-600 text-white rounded-full text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-200">
                                {{ str_replace('_', ' ', $bangDiem->xep_loai ?? 'Đang học') }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="py-12 text-center bg-slate-50 rounded-[2rem] border border-slate-100 border-dashed">
                        <p class="text-slate-400 text-xs italic">Chưa có dữ liệu điểm cho khóa học này.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Cột phải: Giảng viên & Thống kê -->
        <div class="space-y-8">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-blue-600"></div>
                <img class="h-24 w-24 rounded-[2.5rem] object-cover border-4 border-white shadow-xl mx-auto mb-4" src="{{ $lopHoc->giangVien->avatar ? asset('storage/'.$lopHoc->giangVien->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($lopHoc->giangVien->name) }}" alt="">
                <h4 class="text-lg font-black text-slate-800 leading-tight">{{ $lopHoc->giangVien->name }}</h4>
                <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mt-1">{{ $lopHoc->giangVien->giangVienProfile->hoc_vi ?? 'Giảng viên' }}</p>
                <p class="text-xs font-bold text-slate-400 mt-4 px-4 line-clamp-2">{{ $lopHoc->giangVien->giangVienProfile->chuyen_mon ?? 'Chuyên gia đào tạo' }}</p>
                
                <div class="mt-8 flex justify-center space-x-3">
                    <a href="mailto:{{ $lopHoc->giangVien->email }}" class="flex-1 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition duration-300">GỬI EMAIL</a>
                </div>
            </div>

            <div class="bg-slate-900 rounded-[2.5rem] shadow-2xl p-8 text-white">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-8 text-slate-400">Thống kê khóa học</h3>
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400">Tổng số buổi:</span>
                        <span class="text-sm font-black">{{ $tongBuoi }} buổi</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400">Đã học:</span>
                        <span class="text-sm font-black text-blue-400">{{ $daDay }} buổi</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400">Sĩ số lớp:</span>
                        <span class="text-sm font-black text-emerald-400">{{ $lopHoc->hocViens->count() }} học viên</span>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-slate-800 flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-[10px] font-medium text-slate-400 leading-relaxed uppercase tracking-tighter">Bạn đang xem dữ liệu khóa học chính thức từ EDUKHAITRI.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
