@php
    $layout = 'layouts.admin';
    if(Auth::user()->hasRole('giang_vien')) $layout = 'layouts.giang_vien';
    if(Auth::user()->hasRole('hoc_vien')) $layout = 'layouts.hoc_vien';
@endphp

@extends($layout)

@section('title', 'Thông báo của tôi')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <h2 class="text-3xl font-black text-slate-800">Thông báo</h2>
            @if($soChuaDoc > 0)
                <span class="px-3 py-1 bg-red-500 text-white rounded-full text-xs font-black animate-bounce">
                    {{ $soChuaDoc }} MỚI
                </span>
            @endif
        </div>
        
        <form action="{{ route('thong_bao.doc_tat_ca') }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="text-xs font-black text-blue-600 hover:text-blue-800 uppercase tracking-widest transition">
                ĐÁNH DẤU TẤT CẢ ĐÃ ĐỌC
            </button>
        </form>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="divide-y divide-slate-50">
            @forelse($thongBaos as $tb)
                @php $daDoc = $tb->pivot->da_doc; @endphp
                <div class="p-8 hover:bg-slate-50 transition relative group {{ !$daDoc ? 'bg-blue-50/30' : '' }}">
                    <div class="flex items-start space-x-6">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center 
                            {{ match($tb->muc_do) {
                                'success' => 'bg-emerald-100 text-emerald-600',
                                'warning' => 'bg-amber-100 text-amber-600',
                                'danger' => 'bg-red-100 text-red-600',
                                default => 'bg-blue-100 text-blue-600'
                            } }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($tb->loai == 'lich_hoc')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                @elseif($tb->loai == 'diem_so')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                @endif
                            </svg>
                        </div>

                        <!-- Content -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-base font-black {{ !$daDoc ? 'text-slate-800' : 'text-slate-500' }}">
                                    {{ $tb->tieu_de }}
                                </h4>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                    {{ $tb->pivot->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-sm font-medium {{ !$daDoc ? 'text-slate-600' : 'text-slate-400' }} leading-relaxed">
                                {{ $tb->noi_dung }}
                            </p>
                            
                            @if($tb->url)
                                <div class="mt-4">
                                    <form action="{{ route('thong_bao.doc', $tb->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="inline-flex items-center text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] hover:text-blue-800 transition">
                                            XEM CHI TIẾT &rarr;
                                        </button>
                                    </form>
                                </div>
                            @elseif(!$daDoc)
                                <div class="mt-4">
                                    <form action="{{ route('thong_bao.doc', $tb->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600">
                                            ĐÁNH DẤU ĐÃ ĐỌC
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <!-- Dot -->
                        @if(!$daDoc)
                            <div class="absolute top-8 right-8 w-2 h-2 rounded-full bg-blue-600"></div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-20 text-center text-slate-400 italic font-medium">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    Hộp thư của bạn hiện đang trống.
                </div>
            @endforelse
        </div>
        
        @if($thongBaos->hasPages())
            <div class="p-8 bg-slate-50/50 border-t border-slate-50">
                {{ $thongBaos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
