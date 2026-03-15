@extends('layouts.admin')

@section('title', 'Báo cáo Giảng viên')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-slate-800">Hiệu quả Giảng dạy</h2>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mt-1">Phân tích năng lực và sự hài lòng</p>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6">Giảng viên</th>
                        <th class="px-4 py-6 text-center">Số lớp</th>
                        <th class="px-4 py-6 text-center">Học viên</th>
                        <th class="px-4 py-6 text-center">Chuyên cần lớp</th>
                        <th class="px-4 py-6 text-center text-blue-600">Điểm Đánh giá</th>
                        <th class="px-8 py-6 text-right">Xếp loại</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($giangViens as $gv)
                        @php
                            $soLop = $gv->lopHocs->count();
                            $soHV = $gv->lopHocs->sum(fn($l) => $l->hocViens->count());
                            $diemDG = \App\Models\DanhGiaKhoaHoc::whereIn('lop_hoc_id', $gv->lopHocs->pluck('id'))->avg('diem_giang_vien') ?? 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded-xl object-cover mr-4" src="{{ $gv->avatar ? asset('storage/'.$gv->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($gv->name) }}" alt="">
                                    <div>
                                        <p class="text-sm font-black text-slate-800 leading-tight">{{ $gv->name }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $gv->giangVienProfile->chuyen_mon ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-6 text-center text-sm font-bold text-slate-600">{{ $soLop }}</td>
                            <td class="px-4 py-6 text-center text-sm font-bold text-slate-600">{{ $soHV }}</td>
                            <td class="px-4 py-6 text-center">
                                <span class="text-sm font-bold text-emerald-600">--%</span>
                            </td>
                            <td class="px-4 py-6 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-lg font-black text-blue-600">{{ number_format($diemDG, 1) }}</span>
                                    <div class="flex text-amber-400">
                                        @for($i=1; $i<=5; $i++)
                                            <svg class="w-2.5 h-2.5 {{ $i <= round($diemDG) ? 'fill-current' : 'text-slate-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        @endfor
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                    {{ $diemDG >= 4 ? 'XUẤT SẮC' : ($diemDG >= 3 ? 'TỐT' : 'KHÁ') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
