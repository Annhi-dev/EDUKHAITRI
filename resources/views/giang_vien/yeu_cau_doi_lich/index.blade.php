@extends('layouts.giang_vien')

@section('title', 'Yêu cầu đổi lịch')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-black text-slate-800">Yêu cầu đổi lịch</h2>
        <p class="text-slate-500 font-medium">Theo dõi các yêu cầu thay đổi buổi dạy của bạn</p>
    </div>
</div>

<div x-data="{ tab: 'cho_duyet' }">
    <!-- Tabs -->
    <div class="flex space-x-1 bg-slate-200 p-1 rounded-2xl mb-8 w-fit">
        <button @click="tab = 'cho_duyet'" :class="tab === 'cho_duyet' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-600 hover:bg-slate-300'" class="px-6 py-2 rounded-xl text-sm font-black transition duration-200 flex items-center">
            Chờ duyệt
            <span class="ml-2 bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-lg text-[10px]">{{ $yeuCaus->where('trang_thai', 'cho_duyet')->count() }}</span>
        </button>
        <button @click="tab = 'da_duyet'" :class="tab === 'da_duyet' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-600 hover:bg-slate-300'" class="px-6 py-2 rounded-xl text-sm font-black transition duration-200">Đã duyệt</button>
        <button @click="tab = 'tu_choi'" :class="tab === 'tu_choi' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-600 hover:bg-slate-300'" class="px-6 py-2 rounded-xl text-sm font-black transition duration-200">Từ chối / Hủy</button>
    </div>

    <!-- Tab Content -->
    <div class="space-y-4">
        @foreach(['cho_duyet', 'da_duyet', 'tu_choi'] as $status)
            <div x-show="tab === '{{ $status }}'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($yeuCaus->where('trang_thai', $status) as $yc)
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition duration-200 relative overflow-hidden">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-slate-50 rounded-2xl text-emerald-600 mr-4 border border-slate-100">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-800">{{ $yc->lichHoc->lopHoc->ten_lop }}</h4>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $yc->lichHoc->lopHoc->ma_lop }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                                {{ $status === 'cho_duyet' ? 'bg-amber-100 text-amber-700' : ($status === 'da_duyet' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700') }}">
                                {{ $status === 'cho_duyet' ? 'Đang chờ' : ($status === 'da_duyet' ? 'Đã duyệt' : 'Từ chối') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Lịch gốc</p>
                                <p class="text-xs font-black text-slate-500 line-through">{{ \Carbon\Carbon::parse($yc->lichHoc->ngay_hoc)->format('d/m/Y') }}</p>
                                <p class="text-xs font-bold text-slate-500 line-through">{{ substr($yc->lichHoc->gio_bat_dau, 0, 5) }}</p>
                            </div>
                            <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-100">
                                <p class="text-[10px] text-emerald-400 font-bold uppercase mb-1">Lịch mới</p>
                                <p class="text-xs font-black text-emerald-700">{{ \Carbon\Carbon::parse($yc->ngay_muon_doi)->format('d/m/Y') }}</p>
                                <p class="text-xs font-bold text-emerald-700">{{ substr($yc->gio_bat_dau_moi, 0, 5) }}</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Lý do đổi</p>
                            <p class="text-sm text-slate-600 bg-slate-50 p-3 rounded-xl italic border border-slate-50">"{{ $yc->ly_do }}"</p>
                        </div>

                        @if($yc->ghi_chu_admin)
                            <div class="mb-6">
                                <p class="text-[10px] text-red-400 font-bold uppercase mb-1">Phản hồi từ Admin</p>
                                <p class="text-sm text-red-600 bg-red-50 p-3 rounded-xl italic border border-red-50">"{{ $yc->ghi_chu_admin }}"</p>
                            </div>
                        @endif

                        <div class="flex justify-between items-center text-[10px] text-slate-400 font-bold">
                            <span>Gửi lúc: {{ $yc->created_at->format('H:i d/m/Y') }}</span>
                            @if($status === 'cho_duyet')
                                <form action="{{ route('gv.yeu_cau.destroy', $yc->id) }}" method="POST" onsubmit="return confirm('Bạn muốn hủy yêu cầu này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-black uppercase tracking-tighter">Hủy yêu cầu &times;</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400 font-medium italic">
                        Không có yêu cầu nào trong mục này.
                    </div>
                @ forelse ($yeuCaus->where('trang_thai', $status) as $yc)
                @endforelse
            </div>
        @endforeach
    </div>
    
    <div class="mt-8">
        {{ $yeuCaus->links() }}
    </div>
</div>
@endsection
