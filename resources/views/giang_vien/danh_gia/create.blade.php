@extends('layouts.giang_vien')

@section('title', 'Đánh giá học viên')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <a href="{{ route('gv.danh_gia.index') }}" class="text-emerald-600 hover:text-emerald-800 flex items-center font-bold text-sm transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Danh sách lớp
    </a>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-8" x-data="{ 
    kyHoc: 1, 
    namHoc: {{ date('Y') }},
    openPanel: null,
    updateRating(hvId) {
        let total = 0;
        let count = 0;
        document.querySelectorAll(`.slider-${hvId}`).forEach(s => {
            total += parseInt(s.value);
            count++;
        });
        const dtb = (total / count).toFixed(1);
        const spanDTB = document.getElementById(`dtb-${hvId}`);
        const spanXL = document.getElementById(`xl-${hvId}`);
        
        spanDTB.innerText = dtb;
        
        let xl = 'Trung bình';
        let cls = 'bg-slate-100 text-slate-700';
        if(dtb >= 9) { xl = 'Xuất sắc'; cls = 'bg-amber-100 text-amber-700'; }
        else if(dtb >= 8) { xl = 'Giỏi'; cls = 'bg-emerald-100 text-emerald-700'; }
        else if(dtb >= 6.5) { xl = 'Khá'; cls = 'bg-blue-100 text-blue-700'; }
        else if(dtb < 5) { xl = 'Yếu'; cls = 'bg-red-100 text-red-700'; }
        
        spanXL.innerText = xl;
        spanXL.className = `px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter ${cls}`;
    }
}">
    <!-- Header -->
    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-2xl font-black text-slate-800">{{ $lopHoc->ten_lop }}</h2>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">Thực hiện đánh giá định kỳ</p>
            </div>
            <div class="flex space-x-4">
                <div class="w-32">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Kỳ học</label>
                    <select name="ky_hoc" form="danhGiaForm" class="w-full rounded-2xl border-slate-200 text-sm font-bold focus:ring-purple-500 focus:border-purple-500">
                        <option value="1">Kỳ 1</option>
                        <option value="2">Kỳ 2</option>
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Năm học</label>
                    <select name="nam_hoc" form="danhGiaForm" class="w-full rounded-2xl border-slate-200 text-sm font-bold focus:ring-purple-500 focus:border-purple-500">
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('gv.danh_gia.store') }}" method="POST" id="danhGiaForm" class="p-4 space-y-4">
        @csrf
        <input type="hidden" name="lop_hoc_id" value="{{ $lopHoc->id }}">
        
        @foreach($hocViens as $index => $hv)
            <div class="border border-slate-100 rounded-[2rem] overflow-hidden transition-all duration-300" 
                 :class="openPanel === {{ $index }} ? 'shadow-xl shadow-purple-900/5 ring-2 ring-purple-100' : 'hover:bg-slate-50'">
                
                <!-- Accordion Header -->
                <div @click="openPanel = (openPanel === {{ $index }} ? null : {{ $index }})" 
                     class="px-8 py-5 cursor-pointer flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <img class="h-10 w-10 rounded-2xl object-cover border-2 border-white shadow-sm" src="{{ $hv->avatar ? asset('storage/'.$hv->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($hv->name) }}" alt="">
                        <div>
                            <h4 class="text-sm font-black text-slate-800 leading-tight">{{ $hv->name }}</h4>
                            <p class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $hv->hocVienProfile->ma_hoc_vien ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <div class="text-right hidden sm:block">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Xếp loại dự kiến</p>
                            <span id="xl-{{ $hv->id }}" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-slate-100 text-slate-500">Chưa đánh giá</span>
                        </div>
                        <div class="bg-slate-100 w-10 h-10 rounded-full flex items-center justify-center transition-transform duration-300"
                             :class="openPanel === {{ $index }} ? 'rotate-180 bg-purple-100 text-purple-600' : 'text-slate-400'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Accordion Content -->
                <div x-show="openPanel === {{ $index }}" x-collapse>
                    <div class="px-8 py-8 bg-slate-50/50 border-t border-slate-100">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                            <!-- Tiêu chí -->
                            <div class="space-y-8">
                                @foreach($tieuChis as $tc)
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-end">
                                            <label class="text-xs font-black text-slate-700 uppercase tracking-widest">{{ $tc->ten_tieu_chi }}</label>
                                            <span class="text-sm font-black text-purple-600 bg-white px-3 py-1 rounded-lg shadow-sm border border-purple-50"><span id="val-{{ $hv->id }}-{{ $tc->id }}">5</span>/10</span>
                                        </div>
                                        <input type="range" name="danh_gias[{{ $hv->id }}][chi_tiet][{{ $tc->id }}]" min="1" max="10" value="5" 
                                               class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-purple-600 slider-{{ $hv->id }}"
                                               @input="document.getElementById('val-{{ $hv->id }}-{{ $tc->id }}').innerText = $el.value; updateRating({{ $hv->id }})">
                                        <p class="text-[10px] text-slate-400 italic">{{ $tc->mo_ta }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Nhận xét -->
                            <div class="flex flex-col">
                                <label class="text-xs font-black text-slate-700 uppercase tracking-widest mb-3">Nhận xét chi tiết</label>
                                <textarea name="danh_gias[{{ $hv->id }}][nhan_xet]" rows="8" placeholder="Viết nhận xét về học viên này..." 
                                          class="flex-1 rounded-3xl border-slate-200 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium p-4"></textarea>
                                
                                <div class="mt-8 pt-8 border-t border-slate-100 flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Điểm trung bình</span>
                                        <span class="text-3xl font-black text-slate-800" id="dtb-{{ $hv->id }}">5.0</span>
                                    </div>
                                    <button type="button" @click="openPanel = null" class="px-6 py-2 bg-white text-slate-600 border border-slate-200 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition">
                                        Xong
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="pt-12 text-center pb-8">
            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-20 py-4 rounded-[2rem] font-black text-sm transition shadow-2xl shadow-slate-200 hover:scale-105 active:scale-95 uppercase tracking-widest">
                LƯU TOÀN BỘ ĐÁNH GIÁ
            </button>
        </div>
    </form>
</div>
@endsection
