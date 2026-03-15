@extends('layouts.giang_vien')

@section('title', 'Lớp học của tôi')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Lớp học phụ trách</h2>
        <p class="text-slate-500 font-medium">Danh sách các lớp học bạn đang trực tiếp giảng dạy</p>
    </div>
    <span class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-2xl font-black text-sm border border-emerald-50">
        TỔNG: {{ $lopHocs->total() }} LỚP
    </span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    @forelse($lopHocs as $lop)
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl hover:shadow-emerald-900/5 transition-all duration-300 group">
            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100 mb-2 inline-block">
                            {{ $lop->khoaHoc->ten_khoa_hoc }}
                        </span>
                        <h3 class="text-2xl font-black text-slate-800 group-hover:text-emerald-600 transition-colors duration-200">{{ $lop->ten_lop }}</h3>
                        <p class="font-mono font-bold text-slate-400 text-sm uppercase tracking-tighter">{{ $lop->ma_lop }}</p>
                    </div>
                    <span class="px-4 py-1.5 rounded-2xl text-[10px] font-black uppercase tracking-tighter
                        {{ $lop->trang_thai === 'dang_hoc' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-100' : 'bg-amber-400 text-white' }}">
                        {{ $lop->trang_thai === 'dang_hoc' ? 'Đang học' : 'Sắp mở' }}
                    </span>
                </div>

                <!-- Progress Bar -->
                @php 
                    $percent = $lop->tong_so_buoi > 0 ? round(($lop->so_buoi_da_day / $lop->tong_so_buoi) * 100) : 0;
                @endphp
                <div class="mb-8">
                    <div class="flex justify-between items-end mb-2">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Tiến độ khóa học</p>
                        <p class="text-sm font-black text-emerald-600">{{ $percent }}%</p>
                    </div>
                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-50">
                        <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="text-center p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Học viên</p>
                        <p class="text-lg font-black text-slate-700">{{ $lop->hocViens->count() }}</p>
                    </div>
                    <div class="text-center p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Đã dạy</p>
                        <p class="text-lg font-black text-slate-700">{{ $lop->so_buoi_da_day }}</p>
                    </div>
                    <div class="text-center p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Còn lại</p>
                        <p class="text-lg font-black text-slate-700">{{ $lop->so_buoi_con_lai }}</p>
                    </div>
                </div>

                <div class="flex items-center text-xs text-slate-400 font-bold uppercase tracking-widest mb-8">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Khai giảng: {{ \Carbon\Carbon::parse($lop->ngay_bat_dau)->format('d/m/Y') }}
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('gv.lop_hoc.show', $lop->id) }}" class="flex-1 text-center py-3 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-2xl transition duration-200 shadow-lg shadow-slate-200">
                        CHI TIẾT LỚP
                    </a>
                    <a href="{{ route('gv.lop_hoc.hoc_vien', $lop->id) }}" class="flex-1 text-center py-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-black rounded-2xl transition duration-200 border border-emerald-100">
                        HỌC VIÊN
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white p-12 rounded-[2rem] text-center border border-slate-100 shadow-sm">
            <p class="text-slate-500 font-medium italic">Bạn chưa được phân công phụ trách lớp học nào.</p>
        </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $lopHocs->links() }}
</div>
@endsection
