@extends('layouts.hoc_vien')

@section('title', 'Chi tiết điểm danh')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 text-xs font-bold uppercase tracking-widest">
                    <li class="inline-flex items-center">
                        <a href="{{ route('hv.diem_danh.index') }}" class="text-slate-400 hover:text-blue-600 transition">ĐIỂM DANH</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-slate-300 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="text-blue-600">{{ $lopHoc->ten_lop }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-3xl font-black text-slate-800">{{ $lopHoc->ten_lop }}</h2>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mt-1">Lịch sử điểm danh buổi học</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Bảng lịch sử (Cột trái) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                                <th class="px-8 py-6 w-16">STT</th>
                                <th class="px-8 py-6">Thời gian</th>
                                <th class="px-8 py-6 text-center">Trạng thái</th>
                                <th class="px-8 py-6">Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($lichHocs as $index => $lich)
                                @php 
                                    $dd = $diemDanhs[$lich->id] ?? null;
                                    $isFuture = \Carbon\Carbon::parse($lich->ngay_hoc)->isFuture();
                                    $isToday = \Carbon\Carbon::parse($lich->ngay_hoc)->isToday();
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition {{ $isToday ? 'bg-blue-50/30' : '' }}">
                                    <td class="px-8 py-6 text-sm font-bold text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-8 py-6">
                                        <p class="text-sm font-black text-slate-700 leading-tight">
                                            {{ \Carbon\Carbon::parse($lich->ngay_hoc)->locale('vi')->isoFormat('dddd, DD/MM/Y') }}
                                        </p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mt-1 italic">
                                            {{ substr($lich->gio_bat_dau, 0, 5) }} - {{ substr($lich->gio_ket_thuc, 0, 5) }} | Phòng {{ $lich->phong_hoc }}
                                        </p>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if($isFuture)
                                            <span class="px-3 py-1 bg-slate-100 text-slate-300 rounded-full text-[10px] font-black uppercase tracking-widest">CHƯA DIỄN RA</span>
                                        @elseif($dd)
                                            @php 
                                                $colors = [
                                                    'co_mat' => 'bg-emerald-100 text-emerald-700',
                                                    'vang_co_phep' => 'bg-amber-100 text-amber-700',
                                                    'vang_khong_phep' => 'bg-red-100 text-red-700',
                                                    'di_muon' => 'bg-purple-100 text-purple-700',
                                                    've_som' => 'bg-blue-100 text-blue-700'
                                                ];
                                            @endphp
                                            <span class="px-3 py-1 {{ $colors[$dd->trang_thai] ?? 'bg-slate-100' }} rounded-full text-[10px] font-black uppercase tracking-widest">
                                                {{ str_replace('_', ' ', $dd->trang_thai) }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-slate-100 text-slate-400 rounded-full text-[10px] font-black uppercase tracking-widest">CHƯA ĐIỂM DANH</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-xs font-medium text-slate-500 italic">{{ $dd->ghi_chu ?? '---' }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Thống kê & Doughnut (Cột phải) -->
        <div class="space-y-8">
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-8">Phân bổ chuyên cần</h3>
                <div class="h-64 relative">
                    <canvas id="attendanceChart"></canvas>
                </div>
                
                <div class="mt-8 space-y-4">
                    @php 
                        $total = $diemDanhs->count();
                        $stats = [
                            ['label' => 'Có mặt', 'val' => $diemDanhs->where('trang_thai', 'co_mat')->count(), 'color' => 'bg-emerald-500'],
                            ['label' => 'Vắng mặt', 'val' => $diemDanhs->whereIn('trang_thai', ['vang_co_phep', 'vang_khong_phep'])->count(), 'color' => 'bg-red-500'],
                            ['label' => 'Đi muộn / Về sớm', 'val' => $diemDanhs->whereIn('trang_thai', ['di_muon', 've_som'])->count(), 'color' => 'bg-amber-500'],
                        ];
                    @endphp
                    @foreach($stats as $s)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full {{ $s['color'] }}"></div>
                                <span class="text-xs font-bold text-slate-600">{{ $s['label'] }}</span>
                            </div>
                            <span class="text-sm font-black text-slate-800">{{ $s['val'] }} buổi</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-blue-600 p-8 rounded-[2rem] shadow-xl text-white">
                <h4 class="text-xs font-black uppercase tracking-widest text-blue-200 mb-4">Thông tin lớp học</h4>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-bold text-blue-300 uppercase tracking-widest">Giảng viên</p>
                        <p class="text-sm font-black">{{ $lopHoc->giangVien->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-blue-300 uppercase tracking-widest">Thời gian khóa học</p>
                        <p class="text-sm font-black">
                            {{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }} 
                            &rarr; 
                            {{ \Carbon\Carbon::parse($lopHoc->ngay_ket_thuc)->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Có mặt', 'Vắng mặt', 'Muộn/Sớm'],
                datasets: [{
                    data: [
                        {{ $diemDanhs->where('trang_thai', 'co_mat')->count() }},
                        {{ $diemDanhs->whereIn('trang_thai', ['vang_co_phep', 'vang_khong_phep'])->count() }},
                        {{ $diemDanhs->whereIn('trang_thai', ['di_muon', 've_som'])->count() }}
                    ],
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false } },
                maintainAspectRatio: false
            }
        });
    });
</script>
@endsection
