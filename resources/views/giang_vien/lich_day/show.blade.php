@extends('layouts.giang_vien')

@section('title', 'Chi tiết buổi dạy')

@section('content')
<div class="mb-6">
    <a href="{{ route('gv.lich_day.index') }}" class="text-emerald-600 hover:text-emerald-800 flex items-center font-bold text-sm transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quay lại lịch dạy
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Cột trái: Thông tin buổi học -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="h-24 bg-emerald-600 relative">
                <div class="absolute -bottom-6 left-6">
                    <span class="px-4 py-2 bg-white rounded-2xl shadow-md text-emerald-700 font-black text-xl border border-emerald-50 uppercase tracking-tighter">
                        {{ $lichHoc->lopHoc->ma_lop }}
                    </span>
                </div>
            </div>
            <div class="pt-10 px-6 pb-6">
                <h2 class="text-2xl font-black text-slate-800 mb-1 leading-tight">{{ $lichHoc->lopHoc->ten_lop }}</h2>
                <p class="text-slate-500 font-medium text-sm mb-6">{{ $lichHoc->lopHoc->khoaHoc->ten_khoa_hoc }}</p>
                
                <div class="space-y-4">
                    <div class="flex items-center p-3 bg-slate-50 rounded-2xl">
                        <div class="p-2 bg-white rounded-xl shadow-sm text-emerald-600 mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Ngày học</p>
                            <p class="text-sm font-black text-slate-700">{{ \Carbon\Carbon::parse($lichHoc->ngay_hoc)->translatedFormat('l, d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-slate-50 rounded-2xl">
                        <div class="p-2 bg-white rounded-xl shadow-sm text-emerald-600 mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-12 0 9 9 0 0112 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Thời gian</p>
                            <p class="text-sm font-black text-slate-700">{{ substr($lichHoc->gio_bat_dau, 0, 5) }} - {{ substr($lichHoc->gio_ket_thuc, 0, 5) }}</p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-slate-50 rounded-2xl">
                        <div class="p-2 bg-white rounded-xl shadow-sm text-emerald-600 mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Phòng học</p>
                            <p class="text-sm font-black text-slate-700">{{ $lichHoc->phong_hoc ?? 'Chưa gán' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 space-y-3">
                    @php $isToday = \Carbon\Carbon::parse($lichHoc->ngay_hoc)->isToday(); @endphp
                    
                    @if($isToday && $lichHoc->trang_thai !== 'huy')
                        <a href="{{ route('gv.diem_danh.create', ['lich_hoc_id' => $lichHoc->id]) }}" class="block w-full text-center py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl shadow-lg shadow-emerald-200 transition duration-200">
                            ĐIỂM DANH NGAY
                        </a>
                    @endif

                    @if($lichHoc->trang_thai === 'da_len_lich')
                        <a href="{{ route('gv.yeu_cau.create', ['lich_hoc_id' => $lichHoc->id]) }}" class="block w-full text-center py-4 border-2 border-emerald-100 text-emerald-600 hover:bg-emerald-50 font-black rounded-2xl transition duration-200">
                            YÊU CẦU ĐỔI LỊCH
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cột phải: Danh sách học viên -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-black text-slate-800">Danh sách học viên lớp ({{ $hocViens->count() }})</h3>
                <a href="{{ route('gv.lop_hoc.hoc_vien', $lichHoc->lop_hoc_id) }}" class="text-emerald-600 font-bold text-sm hover:underline">Quản lý lớp &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                            <th class="px-8 py-4">STT</th>
                            <th class="px-8 py-4">Học viên</th>
                            <th class="px-8 py-4">Mã HV</th>
                            <th class="px-8 py-4 text-right">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($hocViens as $index => $hv)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-8 py-4 text-sm font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-8 py-4">
                                    <div class="flex items-center">
                                        <img class="h-10 w-10 rounded-full object-cover mr-3 border-2 border-white shadow-sm" src="{{ $hv->avatar ? asset('storage/'.$hv->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($hv->name).'&background=f1f5f9&color=475569' }}" alt="">
                                        <div>
                                            <p class="text-sm font-black text-slate-700 leading-tight">{{ $hv->name }}</p>
                                            <p class="text-[10px] font-medium text-slate-400 leading-tight">{{ $hv->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-sm font-mono font-bold text-emerald-600">{{ $hv->hocVienProfile->ma_hoc_vien ?? 'N/A' }}</td>
                                <td class="px-8 py-4 text-right">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-700">Đang học</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
