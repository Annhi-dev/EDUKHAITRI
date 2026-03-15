@extends('layouts.admin')

@section('title', 'Báo cáo Học viên')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-slate-800">Kết quả Học tập Học viên</h2>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mt-1">Danh sách tổng hợp và xếp loại</p>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6">Học viên</th>
                        <th class="px-4 py-6 text-center">Số lớp tham gia</th>
                        <th class="px-4 py-6 text-center text-blue-600">Điểm TB</th>
                        <th class="px-8 py-6 text-right">Xếp loại</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($hocViens as $hv)
                        @php
                            $soLop = $hv->hocVienLopHocs->count();
                            $diemTB = \App\Models\BangDiem::where('hoc_vien_id', $hv->id)->avg('diem_trung_binh') ?? 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded-xl object-cover mr-4 shadow-sm" src="{{ $hv->avatar ? asset('storage/'.$hv->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($hv->name) }}" alt="">
                                    <div>
                                        <p class="text-sm font-black text-slate-800 leading-tight">{{ $hv->name }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $hv->hocVienProfile->ma_hoc_vien ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-6 text-center text-sm font-bold text-slate-600">{{ $soLop }} lớp</td>
                            <td class="px-4 py-6 text-center">
                                <span class="text-lg font-black text-blue-600">{{ number_format($diemTB, 1) }}</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                @php 
                                    $xepLoai = 'Chưa xếp loại';
                                    if ($diemTB >= 9) $xepLoai = 'XUẤT SẮC';
                                    elseif ($diemTB >= 8) $xepLoai = 'GIỎI';
                                    elseif ($diemTB >= 6.5) $xepLoai = 'KHÁ';
                                    elseif ($diemTB >= 5) $xepLoai = 'TRUNG BÌNH';
                                    elseif ($diemTB > 0) $xepLoai = 'YẾU';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                                    {{ match($xepLoai) {
                                        'XUẤT SẮC' => 'bg-amber-100 text-amber-700',
                                        'GIỎI' => 'bg-emerald-100 text-emerald-700',
                                        'KHÁ' => 'bg-blue-100 text-blue-700',
                                        'TRUNG BÌNH' => 'bg-slate-100 text-slate-700',
                                        'YẾU' => 'bg-red-100 text-red-700',
                                        default => 'bg-slate-50 text-slate-400'
                                    } }}">
                                    {{ $xepLoai }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($hocViens->hasPages())
            <div class="p-8 bg-slate-50/50 border-t border-slate-100">
                {{ $hocViens->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
