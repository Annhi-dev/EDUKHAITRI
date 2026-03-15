@extends('layouts.hoc_vien')

@section('title', 'Đánh giá khóa học')

@section('content')
<div class="space-y-12">
    <!-- Khóa học chờ đánh giá -->
    <section class="space-y-6">
        <h3 class="text-xl font-black text-slate-800 flex items-center">
            <span class="w-8 h-8 bg-amber-500 text-white rounded-lg flex items-center justify-center mr-3 shadow-lg shadow-amber-200 text-sm">⭐</span>
            Khóa học chờ đánh giá của bạn ({{ $chuaDanhGia->count() }})
        </h3>

        @if($chuaDanhGia->isEmpty())
            <div class="bg-emerald-50 border border-emerald-100 p-8 rounded-[2rem] text-center">
                <p class="text-emerald-700 font-bold italic">🎉 Tuyệt vời! Bạn đã hoàn thành đánh giá cho tất cả các khóa học.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($chuaDanhGia as $item)
                    <div class="bg-white border-2 border-amber-100 rounded-[2.5rem] p-8 flex flex-col justify-between hover:shadow-xl hover:shadow-amber-900/5 transition duration-300 relative overflow-hidden group">
                        <div class="relative z-10">
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-black uppercase tracking-widest mb-4 inline-block">CHỜ ĐÁNH GIÁ</span>
                            <h4 class="text-xl font-black text-slate-800 group-hover:text-amber-600 transition">{{ $item->lopHoc->ten_lop }}</h4>
                            <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">{{ $item->lopHoc->khoaHoc->ten_khoa_hoc }}</p>
                            
                            <div class="mt-6 flex items-center space-x-4">
                                <img class="h-10 w-10 rounded-xl object-cover border-2 border-slate-50" src="{{ $item->lopHoc->giangVien->avatar ? asset('storage/'.$item->lopHoc->giangVien->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($item->lopHoc->giangVien->name) }}" alt="">
                                <div>
                                    <p class="text-xs font-black text-slate-700">{{ $item->lopHoc->giangVien->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400">Giảng viên phụ trách</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 relative z-10">
                            <a href="{{ route('hv.danh_gia.create', $item->lop_hoc_id) }}" class="block w-full text-center py-4 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-2xl transition shadow-lg shadow-amber-200 uppercase tracking-widest text-xs">
                                ĐÁNH GIÁ NGAY &rarr;
                            </a>
                        </div>
                        <!-- Trang trí -->
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-amber-50 rounded-full opacity-50 group-hover:scale-150 transition duration-700"></div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Danh sách đánh giá đã gửi -->
    <section class="space-y-6">
        <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest flex items-center">
            <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            Lịch sử đánh giá
        </h3>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                            <th class="px-8 py-6">Khóa học / Lớp</th>
                            <th class="px-4 py-6 text-center">Nội dung</th>
                            <th class="px-4 py-6 text-center">Giảng viên</th>
                            <th class="px-4 py-6 text-center text-blue-600">Điểm TB</th>
                            <th class="px-8 py-6 text-right">Ngày gửi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($daDanhGia as $dg)
                            <tr class="hover:bg-slate-50/50 transition cursor-pointer group" onclick="window.location='{{ route('hv.danh_gia.show', $dg->id) }}'">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-800 leading-tight group-hover:text-blue-600 transition">{{ $dg->lopHoc->ten_lop }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $dg->lopHoc->khoaHoc->ten_khoa_hoc }}</p>
                                </td>
                                <td class="px-4 py-6 text-center">
                                    <div class="flex items-center justify-center text-amber-400">
                                        @for($i=1; $i<=5; $i++)
                                            <svg class="w-3 h-3 {{ $i <= $dg->diem_noi_dung ? 'fill-current' : 'text-slate-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-4 py-6 text-center">
                                    <div class="flex items-center justify-center text-amber-400">
                                        @for($i=1; $i<=5; $i++)
                                            <svg class="w-3 h-3 {{ $i <= $dg->diem_giang_vien ? 'fill-current' : 'text-slate-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-4 py-6 text-center">
                                    <span class="text-sm font-black text-blue-600">{{ number_format($dg->diem_trung_binh, 1) }}</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="text-xs font-bold text-slate-400">{{ $dg->created_at->format('d/m/Y') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-8 py-12 text-center text-slate-400 italic">Bạn chưa thực hiện đánh giá nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
