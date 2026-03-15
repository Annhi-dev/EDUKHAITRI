@extends('layouts.giang_vien')

@section('title', 'Đánh giá học viên')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Đánh giá học viên</h2>
        <p class="text-slate-500 font-medium">Đánh giá năng lực và thái độ của học viên định kỳ</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    @forelse($lopHocs as $lop)
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100 mb-2 inline-block">
                        {{ $lop->khoaHoc->ten_khoa_hoc }}
                    </span>
                    <h3 class="text-2xl font-black text-slate-800 leading-tight">{{ $lop->ten_lop }}</h3>
                </div>
            </div>

            <div class="mb-8">
                <div class="flex justify-between items-end mb-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <span>Tiến độ đánh giá kỳ này</span>
                    @php
                        $totalHV = $lop->hocViens->count();
                        // Mock counted evaluatons for view
                        $evaluatedCount = \App\Models\DanhGiaHocVien::where('lop_hoc_id', $lop->id)->where('nam_hoc', date('Y'))->count();
                        $percent = $totalHV > 0 ? round(($evaluatedCount / $totalHV) * 100) : 0;
                    @endphp
                    <span>{{ $evaluatedCount }}/{{ $totalHV }} HV</span>
                </div>
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500 transition-all duration-500" style="width: {{ $percent }}%"></div>
                </div>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('gv.danh_gia.create', $lop->id) }}" class="flex-1 text-center py-4 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-2xl transition duration-200 shadow-lg shadow-slate-200 uppercase tracking-widest text-xs">
                    {{ $percent == 100 ? 'XEM ĐÁNH GIÁ' : 'THỰC HIỆN ĐÁNH GIÁ' }}
                </a>
            </div>
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
