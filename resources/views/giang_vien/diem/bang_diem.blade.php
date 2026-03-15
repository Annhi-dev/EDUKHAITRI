@extends('layouts.giang_vien')

@section('title', 'Nhập điểm lớp học')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <a href="{{ route('gv.diem.index') }}" class="text-emerald-600 hover:text-emerald-800 flex items-center font-bold text-sm transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Danh sách lớp
    </a>
    
    @php $isLocked = $bangDiems->first() && $bangDiems->first()->da_khoa; @endphp
    
    <div class="flex items-center space-x-3">
        <button class="bg-emerald-50 text-emerald-700 px-6 py-2 rounded-2xl text-xs font-black uppercase tracking-widest border border-emerald-100 hover:bg-emerald-100 transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            XUẤT EXCEL
        </button>
        @if(!$isLocked)
            <form action="{{ route('gv.diem.khoa', $lopHoc->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn khóa điểm? Sau khi khóa sẽ không thể chỉnh sửa.')">
                @csrf @method('PATCH')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-red-100 transition">
                    KHÓA ĐIỂM
                </button>
            </form>
        @else
            <span class="bg-slate-100 text-slate-500 px-6 py-2 rounded-2xl text-xs font-black uppercase tracking-widest border border-slate-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                BẢNG ĐIỂM ĐÃ KHÓA
            </span>
        @endif
    </div>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-8">
    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
        <h2 class="text-2xl font-black text-slate-800">{{ $lopHoc->ten_lop }}</h2>
        <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">{{ $lopHoc->khoaHoc->ten_khoa_hoc }}</p>
    </div>

    <form action="{{ route('gv.diem.nhap', $lopHoc->id) }}" method="POST">
        @csrf
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">
                        <th class="px-6 py-4 w-12">#</th>
                        <th class="px-6 py-4 min-w-[200px]">Học viên</th>
                        <th class="px-4 py-4 text-center">CC (10%)</th>
                        <th class="px-4 py-4 text-center">KT1 (15%)</th>
                        <th class="px-4 py-4 text-center">KT2 (15%)</th>
                        <th class="px-4 py-4 text-center">GK (20%)</th>
                        <th class="px-4 py-4 text-center">CK (40%)</th>
                        <th class="px-4 py-4 text-center">TB</th>
                        <th class="px-4 py-4 text-center">Xếp loại</th>
                        <th class="px-6 py-4">Ghi chú</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($hocViens as $index => $hv)
                        @php $bd = $bangDiems[$hv->id] ?? null; @endphp
                        <tr class="hover:bg-slate-50/50 transition row-diem" data-hv-id="{{ $hv->id }}">
                            <td class="px-6 py-4 text-sm font-bold text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <input type="hidden" name="diem_data[{{ $index }}][hoc_vien_id]" value="{{ $hv->id }}">
                                <div class="flex items-center">
                                    <div class="mr-3">
                                        <p class="text-sm font-black text-slate-700 leading-tight">{{ $hv->name }}</p>
                                        <p class="font-mono text-[10px] font-bold text-slate-400 tracking-tighter">{{ $hv->hocVienProfile->ma_hoc_vien }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <input type="number" name="diem_data[{{ $index }}][diem_chuyen_can]" value="{{ $bd ? $bd->diem_chuyen_can : $hv->diem_cc_tu_dong }}" step="0.1" min="0" max="10" {{ $isLocked ? 'disabled' : '' }} class="w-16 p-1 text-center font-black rounded-lg border-slate-100 bg-emerald-50 text-emerald-700 input-diem">
                            </td>
                            <td class="px-4 py-4 text-center">
                                <input type="number" name="diem_data[{{ $index }}][diem_kiem_tra_1]" value="{{ $bd ? $bd->diem_kiem_tra_1 : '' }}" step="0.1" min="0" max="10" {{ $isLocked ? 'disabled' : '' }} class="w-16 p-1 text-center font-bold rounded-lg border-slate-100 focus:ring-emerald-500 input-diem">
                            </td>
                            <td class="px-4 py-4 text-center">
                                <input type="number" name="diem_data[{{ $index }}][diem_kiem_tra_2]" value="{{ $bd ? $bd->diem_kiem_tra_2 : '' }}" step="0.1" min="0" max="10" {{ $isLocked ? 'disabled' : '' }} class="w-16 p-1 text-center font-bold rounded-lg border-slate-100 focus:ring-emerald-500 input-diem">
                            </td>
                            <td class="px-4 py-4 text-center">
                                <input type="number" name="diem_data[{{ $index }}][diem_giua_ky]" value="{{ $bd ? $bd->diem_giua_ky : '' }}" step="0.1" min="0" max="10" {{ $isLocked ? 'disabled' : '' }} class="w-16 p-1 text-center font-bold rounded-lg border-slate-100 focus:ring-emerald-500 input-diem">
                            </td>
                            <td class="px-4 py-4 text-center">
                                <input type="number" name="diem_data[{{ $index }}][diem_cuoi_ky]" value="{{ $bd ? $bd->diem_cuoi_ky : '' }}" step="0.1" min="0" max="10" {{ $isLocked ? 'disabled' : '' }} class="w-16 p-1 text-center font-bold rounded-lg border-slate-100 focus:ring-emerald-500 input-diem">
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="text-sm font-black text-slate-800 span-dtb">{{ $bd ? number_format($bd->diem_trung_binh, 1) : '--' }}</span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter span-xeploai
                                    {{ $bd ? match($bd->xep_loai) {
                                        'xuat_sac' => 'bg-amber-100 text-amber-700',
                                        'gioi' => 'bg-emerald-100 text-emerald-700',
                                        'kha' => 'bg-blue-100 text-blue-700',
                                        'trung_binh' => 'bg-slate-100 text-slate-700',
                                        'yeu' => 'bg-red-100 text-red-700',
                                        default => 'text-slate-300'
                                    } : 'text-slate-300' }}">
                                    {{ $bd ? str_replace('_', ' ', $bd->xep_loai) : '---' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="diem_data[{{ $index }}][ghi_chu]" value="{{ $bd ? $bd->ghi_chu : '' }}" {{ $isLocked ? 'disabled' : '' }} placeholder="..." class="w-full p-1 text-xs font-medium rounded-lg border-slate-100 bg-slate-50 focus:ring-emerald-500">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(!$isLocked)
            <div class="p-8 border-t border-slate-50 text-right">
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-12 py-4 rounded-3xl font-black text-sm transition shadow-xl shadow-slate-200 hover:scale-105 active:scale-95">
                    LƯU TẤT CẢ ĐIỂM SỐ
                </button>
            </div>
        @endif
    </form>
</div>

<script>
    // JS Logic to calculate realtime average
    document.querySelectorAll('.input-diem').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('.row-diem');
            const cc = parseFloat(row.querySelector('input[name*="diem_chuyen_can"]').value) || 0;
            const kt1 = parseFloat(row.querySelector('input[name*="diem_kiem_tra_1"]').value) || 0;
            const kt2 = parseFloat(row.querySelector('input[name*="diem_kiem_tra_2"]').value) || 0;
            const gk = parseFloat(row.querySelector('input[name*="diem_giua_ky"]').value) || 0;
            const ck = parseFloat(row.querySelector('input[name*="diem_cuoi_ky"]').value) || 0;

            const dtb = (cc * 0.1) + (kt1 * 0.15) + (kt2 * 0.15) + (gk * 0.2) + (ck * 0.4);
            row.querySelector('.span-dtb').innerText = dtb.toFixed(1);

            let xl = '---';
            let cls = 'text-slate-300';
            if (dtb >= 9) { xl = 'XUẤT SẮC'; cls = 'bg-amber-100 text-amber-700'; }
            else if (dtb >= 8) { xl = 'GIỎI'; cls = 'bg-emerald-100 text-emerald-700'; }
            else if (dtb >= 6.5) { xl = 'KHÁ'; cls = 'bg-blue-100 text-blue-700'; }
            else if (dtb >= 5) { xl = 'TRUNG BÌNH'; cls = 'bg-slate-100 text-slate-700'; }
            else if (dtb > 0) { xl = 'YẾU'; cls = 'bg-red-100 text-red-700'; }

            const spanXL = row.querySelector('.span-xeploai');
            spanXL.innerText = xl;
            spanXL.className = `px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter span-xeploai ${cls}`;
        });
    });
</script>
@endsection
