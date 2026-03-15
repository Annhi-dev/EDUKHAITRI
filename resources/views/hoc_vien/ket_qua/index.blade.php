@extends('layouts.hoc_vien')

@section('title', 'Kết quả học tập')

@section('content')
<div class="space-y-8">
    <!-- Tổng quan thành tích -->
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-[2rem] p-8 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="flex items-center space-x-8">
            <div class="text-center bg-white/10 backdrop-blur-md p-6 rounded-[2.5rem] border border-white/20 min-w-[160px]">
                <p class="text-[10px] font-black uppercase tracking-widest text-blue-200 mb-2">Điểm TB toàn khóa</p>
                <h2 class="text-5xl font-black text-white">{{ number_format($diemTBToanKhoa, 1) }}</h2>
            </div>
            <div>
                <p class="text-blue-200 text-xs font-bold uppercase tracking-widest mb-2">Xếp loại chung</p>
                <span class="px-6 py-2 bg-amber-400 text-amber-900 rounded-full text-sm font-black uppercase tracking-widest shadow-lg shadow-amber-900/20">
                    {{ $xepLoaiChung }}
                </span>
                <div class="mt-6 flex space-x-6">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-tighter text-blue-200">Đã hoàn thành</p>
                        <p class="text-xl font-black">{{ $lopDaHoanThanh }} lớp</p>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-tighter text-blue-200">Đang học</p>
                        <p class="text-xl font-black">{{ $lopDangHoc }} lớp</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Radar Chart Placeholder (Biểu đồ năng lực) -->
        <div class="w-full md:w-64 h-64 bg-white/5 rounded-[2rem] p-4 flex items-center justify-center border border-white/10 relative overflow-hidden">
            <canvas id="radarChart"></canvas>
        </div>
    </div>

    <!-- Bảng kết quả theo lớp -->
    <div class="space-y-6">
        <h3 class="text-lg font-black text-slate-800 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            Chi tiết kết quả theo lớp học
        </h3>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="px-8 py-6">Lớp học / Khóa học</th>
                            <th class="px-4 py-6 text-center">CC</th>
                            <th class="px-4 py-6 text-center">KT1</th>
                            <th class="px-4 py-6 text-center">KT2</th>
                            <th class="px-4 py-6 text-center">GK</th>
                            <th class="px-4 py-6 text-center text-blue-600">CK</th>
                            <th class="px-4 py-6 text-center bg-blue-50/50 text-blue-700">TB</th>
                            <th class="px-4 py-6 text-center">Xếp loại</th>
                            <th class="px-8 py-6 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($bangDiems as $bd)
                            <tr class="hover:bg-slate-50/50 transition group">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-800 leading-tight">{{ $bd->lopHoc->ten_lop }}</p>
                                    <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mt-1">{{ $bd->lopHoc->khoaHoc->ten_khoa_hoc }}</p>
                                </td>
                                <td class="px-4 py-6 text-center text-xs font-bold text-slate-500">{{ $bd->diem_chuyen_can ?? '--' }}</td>
                                <td class="px-4 py-6 text-center text-xs font-bold text-slate-500">{{ $bd->diem_kiem_tra_1 ?? '--' }}</td>
                                <td class="px-4 py-6 text-center text-xs font-bold text-slate-500">{{ $bd->diem_kiem_tra_2 ?? '--' }}</td>
                                <td class="px-4 py-6 text-center text-xs font-bold text-slate-500">{{ $bd->diem_giua_ky ?? '--' }}</td>
                                <td class="px-4 py-6 text-center text-sm font-black text-blue-600">{{ $bd->diem_cuoi_ky ?? '--' }}</td>
                                <td class="px-4 py-6 text-center bg-blue-50/30">
                                    <span class="text-sm font-black {{ ($bd->diem_trung_binh >= 5) ? 'text-blue-700' : 'text-red-500' }}">
                                        {{ $bd->diem_trung_binh ? number_format($bd->diem_trung_binh, 1) : '--' }}
                                    </span>
                                </td>
                                <td class="px-4 py-6 text-center">
                                    @if($bd->xep_loai)
                                        @php 
                                            $colors = [
                                                'xuat_sac' => 'bg-amber-100 text-amber-700',
                                                'gioi' => 'bg-emerald-100 text-emerald-700',
                                                'kha' => 'bg-blue-100 text-blue-700',
                                                'trung_binh' => 'bg-slate-100 text-slate-700',
                                                'yeu' => 'bg-red-100 text-red-700'
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $colors[$bd->xep_loai] ?? 'bg-slate-100' }}">
                                            {{ str_replace('_', ' ', $bd->xep_loai) }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">Đang học</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end items-center space-x-2 opacity-0 group-hover:opacity-100 transition">
                                        <a href="{{ route('hv.ket_qua.chi_tiet', $bd->lop_hoc_id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition" title="Xem chi tiết">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        @if($bd->diem_cuoi_ky)
                                            <a href="{{ route('hv.ket_qua.pdf', $bd->lop_hoc_id) }}" class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition" title="In bảng điểm PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-8 py-12 text-center text-slate-400 italic">Chưa có kết quả học tập nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('radarChart').getContext('2d');
        
        // Tính điểm TB thành phần cho tất cả các môn
        @php 
            $avgCC = $bangDiems->avg('diem_chuyen_can') ?? 0;
            $avgKT = ($bangDiems->avg('diem_kiem_tra_1') + $bangDiems->avg('diem_kiem_tra_2')) / 2;
            $avgGK = $bangDiems->avg('diem_giua_ky') ?? 0;
            $avgCK = $bangDiems->avg('diem_cuoi_ky') ?? 0;
        @endphp

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Chuyên cần', 'Kiểm tra', 'Giữa kỳ', 'Cuối kỳ', 'Thái độ'],
                datasets: [{
                    label: 'Năng lực trung bình',
                    data: [{{ $avgCC }}, {{ $avgKT }}, {{ $avgGK }}, {{ $avgCK }}, 8.5], // Thái độ mock
                    fill: true,
                    backgroundColor: 'rgba(255, 255, 255, 0.2)',
                    borderColor: 'rgba(255, 255, 255, 0.8)',
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    r: {
                        min: 0,
                        max: 10,
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                        pointLabels: { color: '#fff', font: { size: 8, weight: 'bold' } },
                        ticks: { display: false, stepSize: 2 }
                    }
                }
            }
        });
    });
</script>
@endsection
