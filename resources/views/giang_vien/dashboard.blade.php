@extends('layouts.giang_vien')

@section('title', 'Bảng điều khiển Giảng viên')

@section('content')
<div class="space-y-8">
    <!-- Chào mừng -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Chào buổi sáng, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-slate-500">Hôm nay là {{ now()->locale('vi')->isoFormat('dddd, [ngày] D [tháng] M [năm] YYYY') }}</p>
        </div>
        <div class="hidden md:block">
            <span class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Trạng thái: Đang hoạt động
            </span>
        </div>
    </div>

    <!-- 4 Card thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Lớp đang dạy -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-medium">Lớp đang dạy</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $tongLop }}</h3>
            </div>
        </div>

        <!-- Tổng học viên -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-medium">Tổng học viên</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $tongHocVien }}</h3>
            </div>
        </div>

        <!-- Buổi chưa điểm danh -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 {{ $buoiChuaDiemDanh > 0 ? 'bg-red-50/30' : '' }}">
            <div class="p-3 {{ $buoiChuaDiemDanh > 0 ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-600' }} rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-medium">Chưa điểm danh</p>
                <h3 class="text-2xl font-bold {{ $buoiChuaDiemDanh > 0 ? 'text-red-600' : 'text-slate-800' }}">{{ $buoiChuaDiemDanh }}</h3>
            </div>
        </div>

        <!-- Yêu cầu đổi lịch -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-medium">Yêu cầu đổi lịch</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $yeuCauChoDuyet }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cột trái: Lịch hôm nay (Timeline) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Lịch dạy hôm nay
                </h3>
                <a href="{{ route('gv.lich_day.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Xem tất cả &rarr;</a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                @if($lichHomNay->isEmpty())
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="text-slate-800 font-bold">Hôm nay bạn không có buổi dạy nào</h4>
                        <p class="text-slate-500 text-sm">Hãy dành thời gian chuẩn bị giáo án hoặc nghỉ ngơi nhé!</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($lichHomNay as $lich)
                            <div class="p-6 hover:bg-slate-50 transition duration-150">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex flex-col items-center justify-center w-20 py-2 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100">
                                            <span class="text-xs font-bold uppercase">{{ $lich->gio_bat_dau }}</span>
                                            <span class="text-[10px] text-emerald-500">đến</span>
                                            <span class="text-xs font-bold uppercase">{{ $lich->gio_ket_thuc }}</span>
                                        </div>
                                        <div>
                                            <h4 class="text-slate-800 font-bold group-hover:text-emerald-600 transition">{{ $lich->lopHoc->ten_lop }}</h4>
                                            <p class="text-slate-500 text-sm mb-2">{{ $lich->lopHoc->khoaHoc->ten_khoa_hoc }}</p>
                                            <div class="flex items-center space-x-4">
                                                <span class="inline-flex items-center text-[11px] font-bold text-slate-500">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                    Phòng: {{ $lich->phong_hoc }}
                                                </span>
                                                <span class="inline-flex items-center text-[11px] font-bold text-slate-500">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                    {{ $lich->lopHoc->hocViens->count() }} học viên
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($lich->daDiemDanh())
                                            <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[11px] font-bold">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Đã điểm danh
                                            </span>
                                        @else
                                            <a href="{{ route('gv.diem_danh.create', ['lich_hoc_id' => $lich->id]) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
                                                Điểm danh ngay
                                            </a>
                                        @endif
                                        <a href="{{ route('gv.lich_day.show', $lich->id) }}" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Cột phải: Lịch tuần (Mini Calendar) -->
        <div class="space-y-6">
            <h3 class="text-lg font-bold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Lịch tuần này
            </h3>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="space-y-4">
                    @php
                        $startOfWeek = now()->startOfWeek();
                    @endphp
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $date = $startOfWeek->copy()->addDays($i);
                            $isToday = $date->isToday();
                            $eventsInDay = $lichTuanNay->filter(function($item) use ($date) {
                                return \Carbon\Carbon::parse($item->ngay_hoc)->isSameDay($date);
                            });
                        @endphp
                        <div class="flex items-center space-x-4">
                            <div class="flex flex-col items-center justify-center w-12 py-1 {{ $isToday ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-50 text-slate-500' }} rounded-xl transition duration-200">
                                <span class="text-[10px] uppercase font-bold">{{ $date->locale('vi')->isoFormat('ddd') }}</span>
                                <span class="text-sm font-bold">{{ $date->day }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                @if($eventsInDay->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($eventsInDay as $event)
                                            <p class="text-[11px] font-bold text-slate-700 truncate">
                                                {{ $event->gio_bat_dau }} - {{ $event->lopHoc->ten_lop }}
                                            </p>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 italic">Không có lịch dạy</p>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
                <a href="{{ route('gv.lich_day.index') }}" class="block w-full text-center mt-6 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold rounded-xl transition">
                    Xem lịch chi tiết
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
