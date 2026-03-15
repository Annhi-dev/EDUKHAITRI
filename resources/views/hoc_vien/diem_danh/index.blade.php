@extends('layouts.hoc_vien')

@section('title', 'Theo dõi điểm danh')

@section('content')
<div class="space-y-8">
    <!-- Thống kê tổng quát -->
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="flex-1">
            <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest mb-4">Chuyên cần trung bình</h3>
            @php 
                $avgTile = count($thongKes) > 0 ? round(collect($thongKes)->avg('tile')) : 0;
                $colorClass = $avgTile >= 80 ? 'text-emerald-600' : ($avgTile >= 60 ? 'text-amber-500' : 'text-red-500');
                $bgClass = $avgTile >= 80 ? 'bg-emerald-500' : ($avgTile >= 60 ? 'bg-amber-500' : 'bg-red-500');
            @endphp
            <div class="flex items-end space-x-4 mb-4">
                <span class="text-6xl font-black {{ $colorClass }}">{{ $avgTile }}%</span>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest pb-2">Tất cả các lớp</span>
            </div>
            <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-50">
                <div class="h-full {{ $bgClass }} rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $avgTile }}%"></div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 min-w-[240px]">
            <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 text-center">
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Đạt yêu cầu</p>
                <p class="text-xl font-black text-emerald-700 mt-1">{{ collect($thongKes)->where('tile', '>=', 80)->count() }} LỚP</p>
            </div>
            <div class="p-4 bg-red-50 rounded-2xl border border-red-100 text-center">
                <p class="text-[10px] font-black text-red-600 uppercase tracking-widest">Cần lưu ý</p>
                <p class="text-xl font-black text-red-700 mt-1">{{ collect($thongKes)->where('tile', '<', 80)->count() }} LỚP</p>
            </div>
        </div>
    </div>

    <!-- Danh sách lớp -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @forelse($lopHocs as $lop)
            @php $tk = $thongKes[$lop->id]; @endphp
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 flex flex-col">
                <div class="p-8 flex-1">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100 mb-2 inline-block">
                                {{ $lop->khoaHoc->ten_khoa_hoc }}
                            </span>
                            <h4 class="text-xl font-black text-slate-800">{{ $lop->ten_lop }}</h4>
                            <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-tighter italic">GV. {{ $lop->giangVien->name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-black {{ $tk['tile'] >= 80 ? 'text-emerald-600' : ($tk['tile'] >= 60 ? 'text-amber-500' : 'text-red-500') }}">
                                {{ $tk['tile'] }}%
                            </span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $tk['tile'] >= 80 ? 'bg-emerald-500' : ($tk['tile'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }} rounded-full transition-all duration-500" style="width: {{ $tk['tile'] }}%"></div>
                        </div>
                        
                        <div class="grid grid-cols-5 gap-2">
                            <div class="text-center">
                                <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Có mặt</p>
                                <p class="text-xs font-black text-emerald-600 bg-emerald-50 py-1 rounded-lg border border-emerald-100">{{ $tk['co_mat'] }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Vắng CP</p>
                                <p class="text-xs font-black text-amber-600 bg-amber-50 py-1 rounded-lg border border-amber-100">{{ $tk['vang_cp'] }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Vắng KP</p>
                                <p class="text-xs font-black text-red-600 bg-red-50 py-1 rounded-lg border border-red-100">{{ $tk['vang_kp'] }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Đi muộn</p>
                                <p class="text-xs font-black text-purple-600 bg-purple-50 py-1 rounded-lg border border-purple-100">{{ $tk['muon'] }}</p>
                            </div>
                            <div class="text-center border-l border-slate-100 ml-1 pl-1">
                                <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Còn lại</p>
                                <p class="text-xs font-black text-blue-600 bg-blue-50 py-1 rounded-lg border border-blue-100">{{ $tk['con_lai'] }}</p>
                            </div>
                        </div>
                    </div>

                    @if($tk['tile'] < 80)
                        <div class="mt-6 p-3 bg-red-50 border border-red-100 rounded-xl flex items-start space-x-2">
                            <svg class="w-4 h-4 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <p class="text-[10px] font-bold text-red-700 leading-snug uppercase tracking-tight">Cảnh báo: Tỷ lệ chuyên cần thấp hơn 80%. Bạn cần có mặt đầy đủ các buổi còn lại!</p>
                        </div>
                    @endif
                </div>
                <div class="p-4 bg-slate-50/50 border-t border-slate-50 text-center">
                    <a href="{{ route('hv.diem_danh.chi_tiet', $lop->id) }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 transition">XEM CHI TIẾT TỪNG BUỔI &rarr;</a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-20 rounded-[2rem] text-center border border-slate-100 italic text-slate-400">Bạn chưa tham gia lớp học nào.</div>
        @endforelse
    </div>
</div>
@endsection
