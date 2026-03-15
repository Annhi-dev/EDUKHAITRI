@extends('layouts.hoc_vien')

@section('title', 'Chi tiết kết quả')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 text-xs font-bold uppercase tracking-widest">
                    <li class="inline-flex items-center">
                        <a href="{{ route('hv.ket_qua.index') }}" class="text-slate-400 hover:text-blue-600 transition">KẾT QUẢ</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-slate-300 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="text-blue-600">{{ $bangDiem->lopHoc->ten_lop }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-3xl font-black text-slate-800">{{ $bangDiem->lopHoc->ten_lop }}</h2>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $bangDiem->lopHoc->khoaHoc->ten_khoa_hoc }}</p>
        </div>
        
        @if($bangDiem->diem_cuoi_ky)
            <a href="{{ route('hv.ket_qua.pdf', $bangDiem->lop_hoc_id) }}" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition shadow-lg shadow-red-900/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                XUẤT BẢNG ĐIỂM PDF
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cột trái: Bảng điểm & Trọng số -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Bảng tính điểm chi tiết</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                                <th class="px-8 py-4">Thành phần điểm</th>
                                <th class="px-4 py-4 text-center">Trọng số</th>
                                <th class="px-4 py-4 text-center">Điểm số</th>
                                <th class="px-8 py-4 text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @php 
                                $components = [
                                    ['label' => 'Chuyên cần (CC)', 'weight' => 10, 'score' => $bangDiem->diem_chuyen_can],
                                    ['label' => 'Kiểm tra 1 (KT1)', 'weight' => 15, 'score' => $bangDiem->diem_kiem_tra_1],
                                    ['label' => 'Kiểm tra 2 (KT2)', 'weight' => 15, 'score' => $bangDiem->diem_kiem_tra_2],
                                    ['label' => 'Giữa kỳ (GK)', 'weight' => 20, 'score' => $bangDiem->diem_giua_ky],
                                    ['label' => 'Cuối kỳ (CK)', 'weight' => 40, 'score' => $bangDiem->diem_cuoi_ky],
                                ];
                            @endphp
                            @foreach($components as $cp)
                                <tr>
                                    <td class="px-8 py-5 text-sm font-bold text-slate-700">{{ $cp['label'] }}</td>
                                    <td class="px-4 py-5 text-center text-xs font-black text-slate-400">{{ $cp['weight'] }}%</td>
                                    <td class="px-4 py-5 text-center">
                                        <span class="text-sm font-black {{ ($cp['score'] >= 5 || !$cp['score']) ? 'text-slate-800' : 'text-red-500' }}">
                                            {{ $cp['score'] ?? '--' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right text-sm font-black text-blue-600">
                                        {{ $cp['score'] ? number_format($cp['score'] * ($cp['weight'] / 100), 2) : '--' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-blue-50/50">
                                <td colspan="2" class="px-8 py-6 text-sm font-black text-blue-800 uppercase tracking-widest">ĐIỂM TRUNG BÌNH CUỐI KHÓA</td>
                                <td colspan="2" class="px-8 py-6 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="text-4xl font-black text-blue-700">{{ number_format($bangDiem->diem_trung_binh, 2) }}</span>
                                        <span class="mt-1 px-4 py-1 bg-blue-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-900/20">
                                            Xếp loại: {{ str_replace('_', ' ', $bangDiem->xep_loai ?? 'Chưa xếp loại') }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Chuyên cần chi tiết -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Theo dõi chuyên cần</h3>
                    <span class="text-xs font-black text-blue-600 uppercase">Tỷ lệ: {{ $tileChuyenCan }}%</span>
                </div>
                
                <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden p-1 border border-slate-50">
                    <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all duration-1000" style="width: {{ $tileChuyenCan }}%"></div>
                </div>

                <div class="mt-8 grid grid-cols-5 md:grid-cols-10 gap-3">
                    @foreach($diemDanhs as $dd)
                        @php 
                            $color = match($dd->trang_thai) {
                                'co_mat' => 'bg-emerald-500',
                                'vang_co_phep' => 'bg-amber-500',
                                'vang_khong_phep' => 'bg-red-500',
                                'di_muon' => 'bg-purple-500',
                                've_som' => 'bg-blue-500',
                                default => 'bg-slate-200'
                            };
                        @endphp
                        <div class="aspect-square {{ $color }} rounded-xl flex items-center justify-center text-white text-[8px] font-black shadow-sm group relative cursor-pointer hover:scale-110 transition" 
                             title="{{ \Carbon\Carbon::parse($dd->lichHoc->ngay_hoc)->format('d/m') }}: {{ str_replace('_', ' ', $dd->trang_thai) }}">
                            {{ $loop->iteration }}
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8 flex flex-wrap gap-4 text-[8px] font-black text-slate-400 uppercase tracking-widest">
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span> Có mặt</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5"></span> Vắng (CP)</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-1.5"></span> Vắng (KP)</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-purple-500 mr-1.5"></span> Đi muộn</span>
                </div>
            </div>
        </div>

        <!-- Cột phải: Thông tin Giảng viên & Nhận xét -->
        <div class="space-y-8">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 text-center">
                <img class="h-24 w-24 rounded-[2rem] object-cover border-4 border-white shadow-xl mx-auto mb-4" src="{{ $bangDiem->lopHoc->giangVien->avatar ? asset('storage/'.$bangDiem->lopHoc->giangVien->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($bangDiem->lopHoc->giangVien->name) }}" alt="">
                <h4 class="text-lg font-black text-slate-800">{{ $bangDiem->lopHoc->giangVien->name }}</h4>
                <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">{{ $bangDiem->lopHoc->giangVien->giangVienProfile->hoc_vi ?? 'Giảng viên' }}</p>
                <div class="mt-6 pt-6 border-t border-slate-50 flex justify-center space-x-4">
                    <a href="mailto:{{ $bangDiem->lopHoc->giangVien->email }}" class="p-3 bg-blue-50 text-blue-600 rounded-2xl hover:bg-blue-600 hover:text-white transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </a>
                </div>
            </div>

            @if($danhGia)
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-[2rem] shadow-xl p-8 text-white">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-6 text-indigo-100">Nhận xét từ giảng viên</h3>
                    <div class="relative">
                        <svg class="absolute -top-4 -left-2 w-10 h-10 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H15.017C14.4647 8 14.017 8.44772 14.017 9V12C14.017 12.5523 13.5693 13 13.017 13H12.017V21H14.017ZM5.017 21L5.017 18C5.017 16.8954 5.91243 16 7.017 16H10.017C10.5693 16 11.017 15.5523 11.017 15V9C11.017 8.44772 10.5693 8 10.017 8H6.017C5.46472 8 5.017 8.44772 5.017 9V12C5.017 12.5523 4.56928 13 4.017 13H3.017V21H5.017Z"></path></svg>
                        <p class="text-sm italic leading-relaxed relative z-10 pl-4 font-medium">
                            {{ $danhGia->nhan_xet ?? 'Chưa có nhận xét chi tiết.' }}
                        </p>
                    </div>
                    <div class="mt-8 flex items-center justify-between pt-6 border-t border-white/10">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-indigo-200">Xếp loại đánh giá</p>
                            <p class="text-lg font-black uppercase tracking-tighter">{{ str_replace('_', ' ', $danhGia->xep_loai) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center font-black">
                            {{ number_format($danhGia->diem_trung_binh, 1) }}
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 text-center">
                    <p class="text-slate-400 text-xs italic">Giảng viên chưa gửi nhận xét cho khóa học này.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
