@extends('layouts.hoc_vien')

@section('title', 'Khóa học của tôi')

@section('content')
<div class="space-y-8" x-data="{ tab: 'dang_hoc' }">
    <!-- Tabs Navigation -->
    <div class="flex items-center space-x-2 bg-white p-2 rounded-[2rem] shadow-sm border border-slate-100 w-fit">
        @foreach([
            'dang_hoc' => '📚 ĐANG HỌC',
            'da_hoan_thanh' => '✅ HOÀN THÀNH',
            'bao_luu' => '⏸️ BẢO LƯU'
        ] as $key => $label)
            <button @click="tab = '{{ $key }}'" 
                    :class="tab === '{{ $key }}' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-slate-500 hover:bg-slate-50'"
                    class="px-8 py-3 rounded-[1.5rem] text-xs font-black transition duration-300 uppercase tracking-widest">
                {{ $label }} ({{ $lopHocs->where('trang_thai', $key)->count() }})
            </button>
        @endforeach
    </div>

    <!-- Tab Content -->
    @foreach(['dang_hoc', 'da_hoan_thanh', 'bao_luu'] as $key)
        <div x-show="tab === '{{ $key }}'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($lopHocs->where('trang_thai', $key) as $item)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden hover:shadow-2xl hover:shadow-blue-900/5 transition-all duration-500 group flex flex-col">
                        <div class="p-8 flex-1">
                            <div class="flex justify-between items-start mb-6">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">
                                    {{ $item->lopHoc->khoaHoc->ten_khoa_hoc }}
                                </span>
                                @if($key === 'dang_hoc')
                                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                @endif
                            </div>

                            <h4 class="text-xl font-black text-slate-800 group-hover:text-blue-600 transition duration-300 leading-tight">{{ $item->lopHoc->ten_lop }}</h4>
                            <p class="text-xs font-bold text-slate-400 mt-2 uppercase tracking-tighter italic">👨‍🏫 GV. {{ $item->lopHoc->giangVien->name }}</p>

                            <div class="mt-8 space-y-4">
                                <div class="flex justify-between items-end text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    <span>Tiến độ học tập</span>
                                    <span class="text-blue-600">{{ $item->tien_do }}%</span>
                                </div>
                                <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-50">
                                    <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all duration-1000" style="width: {{ $item->tien_do }}%"></div>
                                </div>
                                <p class="text-[10px] font-bold text-slate-400 text-center uppercase tracking-tighter">Đã học {{ $item->so_buoi_da_day }}/{{ $item->tong_so_buoi }} buổi</p>
                            </div>

                            <div class="mt-8 pt-8 border-t border-slate-50 grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Khai giảng</p>
                                    <p class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($item->lopHoc->ngay_bat_dau)->format('d/m/Y') }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Kết thúc</p>
                                    <p class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($item->lopHoc->ngay_ket_thuc)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-slate-50/50 border-t border-slate-50">
                            <a href="{{ route('hv.khoa_hoc.show', $item->lop_hoc_id) }}" class="block w-full text-center py-3 bg-white hover:bg-blue-600 hover:text-white text-blue-600 border border-blue-100 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300">
                                CHI TIẾT KHÓA HỌC &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-[2rem] border border-slate-100 border-dashed">
                        <p class="text-slate-400 text-sm italic font-medium">Không có khóa học nào trong mục này.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
@endsection
