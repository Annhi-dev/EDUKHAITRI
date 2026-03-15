@extends('layouts.hoc_vien')

@section('title', 'Bảng điều khiển học tập')

@section('content')
<div class="space-y-8">
    <!-- Banner chào mừng -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-3xl font-black mb-2 text-white">Xin chào, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-blue-100 font-medium">Hôm nay là {{ now()->locale('vi')->isoFormat('dddd, [ngày] D [tháng] M [năm] YYYY') }}</p>
            <div class="mt-6 flex items-center space-x-4">
                <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-xl text-xs font-black uppercase tracking-widest border border-white/10">
                    Mã HV: {{ Auth::user()->hocVienProfile->ma_hoc_vien ?? 'N/A' }}
                </span>
                <span class="px-4 py-1.5 bg-emerald-500 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-900/20">
                    Tài khoản: Đang hoạt động
                </span>
            </div>
        </div>
        <!-- Trang trí -->
        <div class="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-48 h-48 bg-blue-400/20 rounded-full blur-2xl"></div>
    </div>

    <!-- 4 Card thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-4 bg-blue-50 text-blue-600 rounded-2xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Lớp đang học</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $lopDangHocCount }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Điểm TB</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $diemTrungBinh ? number_format($diemTrungBinh, 1) : '--' }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Chuyên cần</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $tileChuyenCan }}%</h3>
            </div>
        </div>

        <a href="{{ route('hv.danh_gia.index') }}" class="bg-amber-50 p-6 rounded-[2rem] shadow-sm border border-amber-100 flex items-center space-x-4 hover:shadow-lg transition group">
            <div class="p-4 bg-amber-500 text-white rounded-2xl group-hover:scale-110 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            </div>
            <div>
                <p class="text-amber-600 text-[10px] font-black uppercase tracking-widest mb-1">Chờ đánh giá</p>
                <h3 class="text-2xl font-black text-amber-700">{{ $chuaDanhGiaCount }}</h3>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cột trái: Lịch học hôm nay -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-black text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Lịch học hôm nay
                </h3>
                <a href="{{ route('hv.lich_hoc.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-700 underline underline-offset-4">Xem lịch đầy đủ &rarr;</a>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                @if($lichHomNay->isEmpty())
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-50 text-blue-500 rounded-full mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="text-slate-800 font-black">Hôm nay bạn không có lịch học</h4>
                        <p class="text-slate-400 text-sm mt-1">Tận dụng thời gian để ôn lại kiến thức nhé! 🎉</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($lichHomNay as $lich)
                            <div class="p-6 hover:bg-slate-50/50 transition flex items-center justify-between">
                                <div class="flex items-center space-x-6">
                                    <div class="flex flex-col items-center justify-center w-24 h-24 bg-blue-50 text-blue-700 rounded-[2rem] border border-blue-100 shadow-sm">
                                        <span class="text-xs font-black uppercase">{{ substr($lich->gio_bat_dau, 0, 5) }}</span>
                                        <div class="w-4 h-0.5 bg-blue-200 my-1"></div>
                                        <span class="text-xs font-black uppercase">{{ substr($lich->gio_ket_thuc, 0, 5) }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-black text-slate-800 leading-tight">{{ $lich->lopHoc->ten_lop }}</h4>
                                        <p class="text-blue-600 text-[10px] font-black uppercase tracking-widest mb-2">{{ $lich->lopHoc->khoaHoc->ten_khoa_hoc }}</p>
                                        <div class="flex flex-wrap gap-4">
                                            <span class="flex items-center text-xs font-bold text-slate-500">
                                                <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                P.{{ $lich->phong_hoc }}
                                            </span>
                                            <span class="flex items-center text-xs font-bold text-slate-500">
                                                <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                {{ $lich->lopHoc->giangVien->name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                @php 
                                    $dd = \App\Models\DiemDanh::where('lich_hoc_id', $lich->id)->where('hoc_vien_id', Auth::id())->first();
                                @endphp
                                @if($dd)
                                    <span class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Đã có mặt
                                    </span>
                                @else
                                    <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-widest">
                                        Chưa điểm danh
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Cột phải: Lịch tuần & Kết quả gần đây -->
        <div class="space-y-8">
            <!-- Lịch tuần -->
            <div class="space-y-4">
                <h3 class="text-lg font-black text-slate-800">Lịch tuần này</h3>
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 flex justify-between">
                    @php
                        $startOfWeek = now()->startOfWeek();
                    @endphp
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $date = $startOfWeek->copy()->addDays($i);
                            $isToday = $date->isToday();
                            $hasLich = $lichTuanNay->contains(fn($l) => \Carbon\Carbon::parse($l->ngay_hoc)->isSameDay($date));
                        @endphp
                        <div class="flex flex-col items-center">
                            <span class="text-[10px] font-black uppercase tracking-tighter {{ $isToday ? 'text-blue-600' : 'text-slate-400' }}">{{ $date->locale('vi')->isoFormat('ddd') }}</span>
                            <div class="mt-2 w-8 h-10 rounded-xl flex items-center justify-center font-black text-sm transition duration-300 {{ $isToday ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-slate-700 hover:bg-slate-50' }}">
                                {{ $date->day }}
                            </div>
                            @if($hasLich)
                                <div class="mt-1 w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Kết quả học tập gần đây -->
            <div class="space-y-4">
                <div class="flex items-center justify-between px-1">
                    <h3 class="text-lg font-black text-slate-800">Kết quả gần đây</h3>
                    <a href="{{ route('hv.ket_qua.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Tất cả</a>
                </div>
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="divide-y divide-slate-50">
                        @forelse($ketQuaGanDay as $kq)
                            <div class="p-4 hover:bg-slate-50/50 transition">
                                <div class="flex justify-between items-center">
                                    <div class="overflow-hidden">
                                        <h5 class="text-sm font-black text-slate-800 truncate">{{ $kq->lopHoc->ten_lop }}</h5>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $kq->lopHoc->khoaHoc->ten_khoa_hoc }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-black text-blue-600">{{ number_format($kq->diem_trung_binh, 1) }}</span>
                                        <p class="text-[8px] font-black uppercase tracking-tighter text-slate-400">{{ str_replace('_', ' ', $kq->xep_loai) }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 text-xs italic">Chưa có kết quả học tập.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
