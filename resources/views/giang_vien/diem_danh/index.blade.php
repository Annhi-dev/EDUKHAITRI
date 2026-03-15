@extends('layouts.giang_vien')

@section('title', 'Quản lý Điểm danh')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Điểm danh lớp học</h2>
        <p class="text-slate-500 font-medium">Theo dõi và thực hiện điểm danh cho các buổi dạy</p>
    </div>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
        <form action="{{ route('gv.diem_danh.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="min-w-[200px]">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Lọc theo lớp</label>
                <select name="lop_hoc_id" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-200 text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Tất cả các lớp --</option>
                    @foreach($lopHocs as $lop)
                        <option value="{{ $lop->id }}" {{ request('lop_hoc_id') == $lop->id ? 'selected' : '' }}>{{ $lop->ma_lop }} - {{ $lop->ten_lop }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <a href="{{ route('gv.diem_danh.index') }}" class="px-6 py-2 bg-slate-200 hover:bg-slate-300 text-slate-600 rounded-2xl text-sm font-black transition">XÓA LỌC</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                    <th class="px-8 py-6">Thời gian</th>
                    <th class="px-8 py-6">Lớp học / Khóa học</th>
                    <th class="px-8 py-6">Phòng</th>
                    <th class="px-8 py-6 text-center">Trạng thái</th>
                    <th class="px-8 py-6 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($lichHocs as $lich)
                    @php 
                        $isToday = \Carbon\Carbon::parse($lich->ngay_hoc)->isToday();
                        $hasDiemDanh = $lich->diemDanhs->count() > 0;
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition {{ $isToday && !$hasDiemDanh ? 'bg-emerald-50/30' : '' }}">
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-slate-700">{{ \Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y') }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter italic">Thứ {{ $lich->thu_trong_tuan }} ({{ substr($lich->gio_bat_dau, 0, 5) }})</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-slate-700 leading-tight">{{ $lich->lopHoc->ten_lop }}</p>
                            <p class="text-[10px] font-medium text-slate-400 leading-tight uppercase tracking-widest">{{ $lich->lopHoc->ma_lop }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-bold text-slate-500">{{ $lich->phong_hoc ?? '---' }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @if($hasDiemDanh)
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-widest">
                                    Đã điểm danh ({{ $lich->diemDanhs->where('trang_thai', 'co_mat')->count() }}/{{ $lich->lopHoc->hocViens->count() }})
                                </span>
                            @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-400 rounded-full text-[10px] font-black uppercase tracking-widest {{ $isToday ? 'animate-pulse ring-2 ring-emerald-200 text-emerald-600' : '' }}">
                                    Chưa điểm danh
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end space-x-2">
                                @if($hasDiemDanh)
                                    <a href="{{ route('gv.diem_danh.show', $lich->id) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                @endif
                                
                                @if($isToday || !$hasDiemDanh)
                                    <a href="{{ route('gv.diem_danh.create', ['lich_hoc_id' => $lich->id]) }}" class="px-4 py-2 {{ $hasDiemDanh ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-emerald-600 text-white shadow-lg shadow-emerald-100' }} rounded-xl text-xs font-black uppercase tracking-widest transition hover:scale-105 active:scale-95">
                                        {{ $hasDiemDanh ? 'Sửa' : 'Điểm danh' }}
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-8 py-12 text-center text-slate-400 italic">Không có buổi học nào cần điểm danh.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-8 bg-slate-50/50">
        {{ $lichHocs->links() }}
    </div>
</div>
@endsection
