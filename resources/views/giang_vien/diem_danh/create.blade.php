@extends('layouts.giang_vien')

@section('title', 'Thực hiện điểm danh')

@section('content')
<div class="mb-6">
    <a href="{{ route('gv.diem_danh.index') }}" class="text-emerald-600 hover:text-emerald-800 flex items-center font-bold text-sm transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quay lại
    </a>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-24">
    <!-- Header -->
    <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-50 mb-2 inline-block">
                Điểm danh buổi học
            </span>
            <h2 class="text-2xl font-black text-slate-800">{{ $lichHoc->lopHoc->ten_lop }}</h2>
            <p class="text-sm font-bold text-slate-500 italic">
                {{ \Carbon\Carbon::parse($lichHoc->ngay_hoc)->format('d/m/Y') }} ({{ substr($lichHoc->gio_bat_dau, 0, 5) }} - {{ substr($lichHoc->gio_ket_thuc, 0, 5) }})
            </p>
        </div>
        
        <div class="flex items-center space-x-3" x-data="{}">
            <button type="button" @click="setAll('co_mat')" class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-black uppercase tracking-widest border border-emerald-100 hover:bg-emerald-100 transition">Tất cả có mặt</button>
            <button type="button" @click="setAll('vang_khong_phep')" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-xs font-black uppercase tracking-widest border border-red-100 hover:bg-red-100 transition">Tất cả vắng</button>
        </div>
    </div>

    <!-- Search -->
    <div class="px-8 py-4 border-b border-slate-50">
        <div class="relative max-w-md">
            <input type="text" id="searchHocVien" placeholder="Tìm tên học viên..." class="pl-10 pr-4 py-2 w-full rounded-2xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-bold">
            <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
    </div>

    <form action="{{ route('gv.diem_danh.store') }}" method="POST" id="diemDanhForm">
        @csrf
        <input type="hidden" name="lich_hoc_id" value="{{ $lichHoc->id }}">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="hvTable">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                        <th class="px-8 py-4 w-16">STT</th>
                        <th class="px-8 py-4">Học viên</th>
                        <th class="px-8 py-4">Trạng thái</th>
                        <th class="px-8 py-4">Ghi chú</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($hocViens as $index => $hv)
                        @php $prev = $diemDanhCu[$hv->id] ?? null; @endphp
                        <tr class="hv-row hover:bg-slate-50/50 transition" data-name="{{ strtolower($hv->name) }}">
                            <td class="px-8 py-6 text-sm font-bold text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <input type="hidden" name="diem_danhs[{{ $index }}][hoc_vien_id]" value="{{ $hv->id }}">
                                    <img class="h-10 w-10 rounded-full object-cover mr-3 border-2 border-white shadow-sm" src="{{ $hv->avatar ? asset('storage/'.$hv->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($hv->name) }}" alt="">
                                    <div>
                                        <p class="text-sm font-black text-slate-700 leading-tight">{{ $hv->name }}</p>
                                        <p class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $hv->hocVienProfile->ma_hoc_vien }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-wrap gap-2">
                                    @foreach([
                                        'co_mat' => ['label' => 'Có mặt', 'color' => 'emerald'],
                                        'vang_co_phep' => ['label' => 'Vắng (CP)', 'color' => 'amber'],
                                        'vang_khong_phep' => ['label' => 'Vắng (KP)', 'color' => 'red'],
                                        'di_muon' => ['label' => 'Đi muộn', 'color' => 'purple'],
                                        've_som' => ['label' => 'Về sớm', 'color' => 'blue']
                                    ] as $val => $cfg)
                                        <label class="cursor-pointer group">
                                            <input type="radio" name="diem_danhs[{{ $index }}][trang_thai]" value="{{ $val }}" 
                                                {{ ($prev ? $prev->trang_thai : 'co_mat') === $val ? 'checked' : '' }}
                                                class="hidden peer" onchange="updateCounter()">
                                            <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-tighter transition-all border
                                                peer-checked:bg-{{ $cfg['color'] }}-600 peer-checked:text-white peer-checked:border-{{ $cfg['color'] }}-600
                                                bg-white text-{{ $cfg['color'] }}-600 border-{{ $cfg['color'] }}-100 hover:bg-{{ $cfg['color'] }}-50">
                                                {{ $cfg['label'] }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <input type="text" name="diem_danhs[{{ $index }}][ghi_chu]" value="{{ $prev->ghi_chu ?? '' }}" placeholder="..." class="w-full rounded-xl border-slate-100 bg-slate-50 text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>

<!-- Sticky Footer -->
<div class="fixed bottom-0 left-64 right-0 bg-white border-t border-slate-200 px-8 py-4 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] z-50 flex justify-between items-center">
    <div class="flex items-center space-x-6">
        <div class="flex flex-col">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tổng cộng</span>
            <span class="text-sm font-black text-slate-800">{{ $hocViens->count() }} học viên</span>
        </div>
        <div class="h-8 w-px bg-slate-100"></div>
        <div class="flex space-x-4 text-xs font-black uppercase">
            <span class="text-emerald-600">CÓ MẶT: <span id="count-co_mat">0</span></span>
            <span class="text-red-500">VẮNG: <span id="count-vang">0</span></span>
            <span class="text-purple-600">MUỘN: <span id="count-di_muon">0</span></span>
        </div>
    </div>
    <button type="submit" form="diemDanhForm" class="bg-slate-900 hover:bg-slate-800 text-white px-10 py-3 rounded-2xl font-black text-sm transition shadow-xl hover:scale-105 active:scale-95">
        LƯU KẾT QUẢ ĐIỂM DANH
    </button>
</div>

<script>
    function setAll(status) {
        document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(el => {
            el.checked = true;
        });
        updateCounter();
    }

    function updateCounter() {
        const coMat = document.querySelectorAll('input[type="radio"][value="co_mat"]:checked').length;
        const vCP = document.querySelectorAll('input[type="radio"][value="vang_co_phep"]:checked').length;
        const vKP = document.querySelectorAll('input[type="radio"][value="vang_khong_phep"]:checked').length;
        const diMuon = document.querySelectorAll('input[type="radio"][value="di_muon"]:checked').length;
        const veSom = document.querySelectorAll('input[type="radio"][value="ve_som"]:checked').length;

        document.getElementById('count-co_mat').innerText = coMat;
        document.getElementById('count-vang').innerText = vCP + vKP;
        document.getElementById('count-di_muon').innerText = diMuon;

        // Highlight rows
        document.querySelectorAll('.hv-row').forEach(row => {
            const status = row.querySelector('input[type="radio"]:checked').value;
            row.classList.remove('bg-red-50/50', 'bg-emerald-50/30', 'bg-amber-50/30');
            if (status === 'vang_khong_phep') row.classList.add('bg-red-50/50');
            if (status === 'co_mat') row.classList.add('bg-emerald-50/30');
        });
    }

    // Search function
    document.getElementById('searchHocVien').addEventListener('input', function(e) {
        const val = e.target.value.toLowerCase();
        document.querySelectorAll('.hv-row').forEach(row => {
            const name = row.getAttribute('data-name');
            row.style.display = name.includes(val) ? '' : 'none';
        });
    });

    // Init
    window.onload = updateCounter;
</script>
@endsection
