@extends('layouts.admin')

@section('title', 'Báo cáo tổng hợp')

@section('content')
<div class="space-y-8">
    <!-- Filter Bar -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.bao_cao.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <select name="nam_hoc" class="rounded-2xl border-slate-200 text-xs font-black uppercase tracking-widest focus:ring-blue-500 focus:border-blue-500">
                @for($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ $namHoc == $y ? 'selected' : '' }}>NĂM {{ $y }}</option>
                @endfor
            </select>
            <select name="ky_hoc" class="rounded-2xl border-slate-200 text-xs font-black uppercase tracking-widest focus:ring-blue-500 focus:border-blue-500">
                <option value="1" {{ $kyHoc == 1 ? 'selected' : '' }}>HỌC KỲ 1</option>
                <option value="2" {{ $kyHoc == 2 ? 'selected' : '' }}>HỌC KỲ 2</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition">
                ÁP DỤNG
            </button>
        </form>

        <div class="flex items-center space-x-2">
            <button class="px-6 py-2 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-emerald-100 transition flex items-center shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                XUẤT BÁO CÁO
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-4 bg-blue-50 text-blue-600 rounded-2xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Giảng viên</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $tongQuan['tong_giang_vien'] }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Học viên</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $tongQuan['tong_hoc_vien'] }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Lớp đang mở</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $tongQuan['tong_lop_hoc'] }}</h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center space-x-4">
            <div class="p-4 bg-amber-50 text-amber-600 rounded-2xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Khóa học</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $tongQuan['tong_khoa_hoc'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Highlight Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-blue-600 p-8 rounded-[2.5rem] shadow-xl text-white flex flex-col justify-between">
            <p class="text-[10px] font-black uppercase tracking-widest text-blue-200">Điểm TB hệ thống</p>
            <div class="flex items-end space-x-2 mt-4">
                <h2 class="text-6xl font-black">{{ number_format($tongQuan['diem_tb_he_thong'], 1) }}</h2>
                <p class="text-xl font-bold mb-2">/10</p>
            </div>
            <div class="mt-8 pt-8 border-t border-white/10 flex items-center justify-between text-[10px] font-black uppercase tracking-tighter">
                <span>MỤC TIÊU: 8.0</span>
                <span class="px-2 py-1 bg-white/20 rounded-lg">↑ 0.2%</span>
            </div>
        </div>
        <div class="bg-emerald-500 p-8 rounded-[2.5rem] shadow-xl text-white flex flex-col justify-between">
            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-100">Tỷ lệ chuyên cần</p>
            <div class="flex items-end space-x-2 mt-4">
                <h2 class="text-6xl font-black">{{ $tongQuan['tile_chuyen_can'] }}%</h2>
            </div>
            <div class="mt-8 pt-8 border-t border-white/10 flex items-center justify-between text-[10px] font-black uppercase tracking-tighter">
                <span>MỤC TIÊU: 85%</span>
                <span class="px-2 py-1 bg-white/20 rounded-lg">↑ 1.5%</span>
            </div>
        </div>
        <div class="bg-amber-400 p-8 rounded-[2.5rem] shadow-xl text-amber-900 flex flex-col justify-between">
            <p class="text-[10px] font-black uppercase tracking-widest text-amber-800">Đánh giá mới tháng này</p>
            <div class="flex items-end space-x-2 mt-4">
                <h2 class="text-6xl font-black">{{ $tongQuan['so_danh_gia_thang_nay'] }}</h2>
                <p class="text-xl font-bold mb-2">Phản hồi</p>
            </div>
            <div class="mt-8 pt-8 border-t border-amber-900/10 flex items-center justify-between text-[10px] font-black uppercase tracking-tighter">
                <span>HÀI LÒNG: 92%</span>
                <a href="{{ route('admin.bao_cao.khoa_hoc') }}" class="px-2 py-1 bg-amber-900/10 rounded-lg underline">CHI TIẾT</a>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-8">Học viên mới theo tháng</h3>
            <div class="h-80">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-8">Phân bổ xếp loại học tập</h3>
            <div class="h-80 relative flex items-center justify-center">
                <canvas id="gradingChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Line Chart
        const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
        new Chart(ctxMonthly, {
            type: 'line',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [{
                    label: 'Số học viên mới',
                    data: @json($bieuDoTheoThang),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { weight: 'bold' } } },
                    x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
                }
            }
        });

        // Doughnut Chart
        const ctxGrading = document.getElementById('gradingChart').getContext('2d');
        new Chart(ctxGrading, {
            type: 'doughnut',
            data: {
                labels: ['Xuất sắc', 'Giỏi', 'Khá', 'Trung bình', 'Yếu'],
                datasets: [{
                    data: @json(array_values($phanBoXepLoai)),
                    backgroundColor: ['#fbbf24', '#10b981', '#3b82f6', '#94a3b8', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 10, weight: 'bold' },
                            padding: 20
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
