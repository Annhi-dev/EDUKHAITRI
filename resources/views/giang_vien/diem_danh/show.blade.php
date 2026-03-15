@extends('layouts.giang_vien')

@section('title', 'Kết quả điểm danh')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('gv.diem_danh.index') }}" class="text-emerald-600 hover:text-emerald-800 flex items-center font-bold text-sm transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quay lại
    </a>
    @if(\Carbon\Carbon::parse($lichHoc->ngay_hoc)->isToday())
        <a href="{{ route('gv.diem_danh.create', ['lich_hoc_id' => $lichHoc->id]) }}" class="px-6 py-2 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-100 transition hover:scale-105 active:scale-95">
            CHỈNH SỬA
        </a>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                <h2 class="text-2xl font-black text-slate-800">{{ $lichHoc->lopHoc->ten_lop }}</h2>
                <p class="text-sm font-bold text-slate-500 italic">
                    Kết quả điểm danh ngày {{ \Carbon\Carbon::parse($lichHoc->ngay_hoc)->format('d/m/Y') }}
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                            <th class="px-8 py-4">Học viên</th>
                            <th class="px-8 py-4">Trạng thái</th>
                            <th class="px-8 py-4">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($diemDanhs as $dd)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-8 py-4">
                                    <div class="flex items-center">
                                        <img class="h-8 w-8 rounded-full object-cover mr-3" src="{{ $dd->hocVien->avatar ? asset('storage/'.$dd->hocVien->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($dd->hocVien->name) }}" alt="">
                                        <span class="text-sm font-bold text-slate-700">{{ $dd->hocVien->name }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    @php 
                                        $colors = [
                                            'co_mat' => 'bg-emerald-100 text-emerald-700',
                                            'vang_co_phep' => 'bg-amber-100 text-amber-700',
                                            'vang_khong_phep' => 'bg-red-100 text-red-700',
                                            'di_muon' => 'bg-purple-100 text-purple-700',
                                            've_som' => 'bg-blue-100 text-blue-700'
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $colors[$dd->trang_thai] }}">
                                        {{ str_replace('_', ' ', $dd->trang_thai) }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-xs font-medium text-slate-500 italic">
                                    {{ $dd->ghi_chu ?? '---' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Thống kê -->
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Thống kê buổi học</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-slate-600">Tổng sĩ số</span>
                    <span class="text-lg font-black text-slate-800">{{ $diemDanhs->count() }}</span>
                </div>
                <div class="flex justify-between items-center text-emerald-600">
                    <span class="text-sm font-bold">Có mặt</span>
                    <span class="text-lg font-black">{{ $thongKe['co_mat'] }}</span>
                </div>
                <div class="flex justify-between items-center text-red-500">
                    <span class="text-sm font-bold">Vắng mặt</span>
                    <span class="text-lg font-black">{{ $thongKe['vang_co_phep'] + $thongKe['vang_khong_phep'] }}</span>
                </div>
                <div class="flex justify-between items-center text-purple-600">
                    <span class="text-sm font-bold">Đi muộn / Về sớm</span>
                    <span class="text-lg font-black">{{ $thongKe['di_muon'] + $thongKe['ve_som'] }}</span>
                </div>
            </div>
            
            <!-- Biểu đồ tỷ lệ (CSS thuần) -->
            <div class="mt-8 pt-8 border-t border-slate-50">
                <div class="flex h-4 w-full rounded-full overflow-hidden bg-slate-100">
                    @php 
                        $pCoMat = ($thongKe['co_mat'] / $diemDanhs->count()) * 100;
                        $pVang = (($thongKe['vang_co_phep'] + $thongKe['vang_khong_phep']) / $diemDanhs->count()) * 100;
                    @endphp
                    <div class="h-full bg-emerald-500" style="width: {{ $pCoMat }}%"></div>
                    <div class="h-full bg-red-400" style="width: {{ $pVang }}%"></div>
                    <div class="h-full bg-purple-400" style="width: {{ 100 - $pCoMat - $pVang }}%"></div>
                </div>
                <div class="mt-4 flex justify-center space-x-4 text-[10px] font-black uppercase tracking-tighter">
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-emerald-500 mr-1"></span> Có mặt</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-400 mr-1"></span> Vắng</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-purple-400 mr-1"></span> Khác</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
