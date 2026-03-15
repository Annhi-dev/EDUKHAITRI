@extends('layouts.giang_vien')

@section('title', 'Quản lý điểm số')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Bảng điểm lớp học</h2>
        <p class="text-slate-500 font-medium">Nhập và quản lý điểm cho học viên các lớp bạn phụ trách</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    @forelse($lopHocs as $lop)
        @php 
            $totalHV = $lop->hocViens->count();
            $gradedHV = \App\Models\BangDiem::where('lop_hoc_id', $lop->id)->whereNotNull('diem_cuoi_ky')->count();
            $percent = $totalHV > 0 ? round(($gradedHV / $totalHV) * 100) : 0;
            $isLocked = \App\Models\BangDiem::where('lop_hoc_id', $lop->id)->where('da_khoa', true)->exists();
        @endphp
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-2xl font-black text-slate-800 leading-tight">{{ $lop->ten_lop }}</h3>
                    <p class="text-xs font-black text-emerald-600 uppercase tracking-widest">{{ $lop->khoaHoc->ten_khoa_hoc }}</p>
                </div>
                @if($isLocked)
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Đã khóa
                    </span>
                @else
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-black uppercase tracking-widest">Đang nhập</span>
                @endif
            </div>

            <div class="mb-8">
                <div class="flex justify-between items-end mb-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <span>Tiến độ nhập điểm</span>
                    <span>{{ $gradedHV }}/{{ $totalHV }} HV</span>
                </div>
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 transition-all duration-500" style="width: {{ $percent }}%"></div>
                </div>
            </div>

            <a href="{{ route('gv.diem.bang_diem', $lop->id) }}" class="block w-full text-center py-4 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-2xl transition duration-200 shadow-lg shadow-slate-200 uppercase tracking-widest text-xs">
                {{ $isLocked ? 'XEM BẢNG ĐIỂM' : 'NHẬP ĐIỂM LỚP' }}
            </a>
        </div>
    @empty
        <div class="col-span-full bg-white p-12 rounded-[2rem] text-center border border-slate-100 italic text-slate-400">
            Không có lớp học nào.
        </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $lopHocs->links() }}
</div>
@endsection
