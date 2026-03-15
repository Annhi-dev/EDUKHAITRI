@extends('layouts.giang_vien')

@section('title', 'Thống kê điểm danh')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Thống kê chuyên cần</h2>
        <p class="text-slate-500 font-medium">Bảng tổng hợp điểm danh chi tiết theo từng lớp học</p>
    </div>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-8">
    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
        <form action="{{ route('gv.diem_danh.thong_ke') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="min-w-[250px]">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Chọn lớp học</label>
                <select name="lop_hoc_id" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-200 text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Chọn lớp để xem thống kê --</option>
                    @foreach(\App\Models\LopHoc::where('giang_vien_id', Auth::id())->get() as $lop)
                        <option value="{{ $lop->id }}" {{ request('lop_hoc_id') == $lop->id ? 'selected' : '' }}>{{ $lop->ma_lop }} - {{ $lop->ten_lop }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if(request('lop_hoc_id') && isset($lopHocs) && $lopHocs->count() > 0)
        @foreach($lopHocs as $lop)
            <div class="p-8">
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <h3 class="text-xl font-black text-slate-800">{{ $lop->ten_lop }}</h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Bảng điểm danh chi tiết ({{ $lop->lichHocs->count() }} buổi đã dạy)</p>
                    </div>
                    <button class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-black uppercase tracking-widest border border-emerald-100 hover:bg-emerald-100 transition flex items-center shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        XUẤT EXCEL
                    </button>
                </div>

                <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-6 py-4 border-b border-r border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest sticky left-0 bg-slate-50 z-10">Học viên</th>
                                @foreach($lop->lichHocs as $lich)
                                    <th class="px-3 py-4 border-b border-slate-100 text-center min-w-[50px]">
                                        <div class="flex flex-col items-center">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">{{ \Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m') }}</span>
                                            <span class="text-[8px] font-bold text-slate-300">{{ substr($lich->gio_bat_dau, 0, 5) }}</span>
                                        </div>
                                    </th>
                                @endforeach
                                <th class="px-6 py-4 border-b border-l border-slate-100 text-[10px] font-black text-emerald-600 uppercase tracking-widest text-center sticky right-0 bg-slate-50 z-10">Tỷ lệ %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($lop->hocViens as $hv)
                                @php 
                                    $countCoMat = 0;
                                    $totalBuoi = $lop->lichHocs->count();
                                @endphp
                                <tr class="hover:bg-slate-50/30 transition">
                                    <td class="px-6 py-4 border-r border-slate-100 sticky left-0 bg-white z-10 group-hover:bg-slate-50">
                                        <div class="flex items-center">
                                            <img class="h-7 w-7 rounded-full object-cover mr-3 border border-slate-100" src="{{ $hv->avatar ? asset('storage/'.$hv->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($hv->name) }}" alt="">
                                            <span class="text-xs font-black text-slate-700 truncate max-w-[150px]">{{ $hv->name }}</span>
                                        </div>
                                    </td>
                                    @foreach($lop->lichHocs as $lich)
                                        @php 
                                            $dd = $lich->diemDanhs->where('hoc_vien_id', $hv->id)->first();
                                            $status = $dd ? $dd->trang_thai : null;
                                            if($status == 'co_mat' || $status == 'di_muon' || $status == 've_som') $countCoMat++;
                                        @endphp
                                        <td class="px-3 py-4 text-center border-slate-50">
                                            @if($status == 'co_mat')
                                                <span class="text-emerald-500 font-bold">✅</span>
                                            @elseif($status == 'vang_co_phep')
                                                <span class="text-amber-500 font-bold">📝</span>
                                            @elseif($status == 'vang_khong_phep')
                                                <span class="text-red-500 font-bold">❌</span>
                                            @elseif($status == 'di_muon')
                                                <span class="text-purple-500 font-bold">⚠️</span>
                                            @elseif($status == 've_som')
                                                <span class="text-blue-500 font-bold">🕒</span>
                                            @else
                                                <span class="text-slate-200 font-bold">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    @php 
                                        $percent = $totalBuoi > 0 ? round(($countCoMat / $totalBuoi) * 100) : 0;
                                        $colorClass = $percent >= 80 ? 'text-emerald-600' : ($percent >= 60 ? 'text-amber-500' : 'text-red-500');
                                    @endphp
                                    <td class="px-6 py-4 border-l border-slate-100 text-center font-black {{ $colorClass }} sticky right-0 bg-white z-10 group-hover:bg-slate-50">
                                        {{ $percent }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Chú thích -->
                <div class="mt-6 flex flex-wrap gap-6 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                    <span class="flex items-center"><span class="mr-1.5 text-xs">✅</span> Có mặt</span>
                    <span class="flex items-center"><span class="mr-1.5 text-xs">❌</span> Vắng (KP)</span>
                    <span class="flex items-center"><span class="mr-1.5 text-xs">📝</span> Vắng (CP)</span>
                    <span class="flex items-center"><span class="mr-1.5 text-xs">⚠️</span> Đi muộn</span>
                    <span class="flex items-center"><span class="mr-1.5 text-xs">🕒</span> Về sớm</span>
                </div>

                <!-- Chart -->
                <div class="mt-12 grid grid-cols-1 lg:grid-cols-1 gap-8">
                    <div class="bg-white p-8 rounded-3xl border border-slate-100">
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-8">Biểu đồ tỷ lệ chuyên cần (%)</h4>
                        <div class="h-80">
                            <canvas id="attendanceChart-{{ $lop->id }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="p-20 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m-9-3H9m12 0h-3.5a1 1 0 01-1-1V5a1 1 0 011-1H21a1 1 0 011 1v3.5a1 1 0 01-1 1zM2 6h12m6 7h1v1a6 6 0 01-12 0v-1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h3 class="text-slate-800 font-black text-xl mb-2">Chưa chọn lớp học</h3>
            <p class="text-slate-500 max-w-sm mx-auto">Vui lòng chọn một lớp học từ danh sách phía trên để xem báo cáo thống kê chuyên cần chi tiết.</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if(request('lop_hoc_id') && isset($lopHocs))
    @foreach($lopHocs as $lop)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('attendanceChart-{{ $lop->id }}').getContext('2d');
            
            const labels = [@foreach($lop->hocViens as $hv) "{{ $hv->name }}", @endforeach];
            const data = [@foreach($lop->hocViens as $hv) 
                @php 
                    $count = 0;
                    $total = $lop->lichHocs->count();
                    foreach($lop->lichHocs as $l) {
                        $st = $l->diemDanhs->where('hoc_vien_id', $hv->id)->first();
                        if($st && in_array($st->trang_thai, ['co_mat', 'di_muon', 've_som'])) $count++;
                    }
                    echo ($total > 0 ? round(($count / $total) * 100) : 0) . ",";
                @endphp
            @endforeach];

            const colors = data.map(v => v >= 80 ? '#10b981' : (v >= 60 ? '#f59e0b' : '#ef4444'));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tỷ lệ chuyên cần (%)',
                        data: data,
                        backgroundColor: colors,
                        borderRadius: 12,
                        barThickness: 30,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) { return context.parsed.y + '%'; }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { weight: 'bold' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { weight: 'bold', size: 10 },
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endforeach
@endif
@endsection
