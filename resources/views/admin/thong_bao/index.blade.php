@extends('layouts.admin')

@section('title', 'Quản lý thông báo')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-slate-800">Thông báo hệ thống</h2>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mt-1">Lịch sử các thông báo đã gửi</p>
        </div>
        <a href="{{ route('admin.thong_bao.create') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition shadow-lg shadow-blue-900/20 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            TẠO THÔNG BÁO MỚI
        </a>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6">Tiêu đề / Nội dung</th>
                        <th class="px-4 py-6 text-center">Loại</th>
                        <th class="px-4 py-6 text-center">Người gửi</th>
                        <th class="px-4 py-6 text-center">Gửi tới</th>
                        <th class="px-8 py-6 text-right">Ngày tạo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($thongBaos as $tb)
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-8 py-6 max-w-md">
                                <h4 class="text-sm font-black text-slate-800 leading-tight group-hover:text-blue-600 transition">{{ $tb->tieu_de }}</h4>
                                <p class="text-xs text-slate-400 mt-1 line-clamp-1 italic">{{ $tb->noi_dung }}</p>
                            </td>
                            <td class="px-4 py-6 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                                    {{ match($tb->muc_do) {
                                        'success' => 'bg-emerald-100 text-emerald-700',
                                        'warning' => 'bg-amber-100 text-amber-700',
                                        'danger' => 'bg-red-100 text-red-700',
                                        default => 'bg-blue-100 text-blue-700'
                                    } }}">
                                    {{ $tb->loai }}
                                </span>
                            </td>
                            <td class="px-4 py-6 text-center">
                                <p class="text-xs font-bold text-slate-600">{{ $tb->createdBy->name ?? 'System' }}</p>
                            </td>
                            <td class="px-4 py-6 text-center">
                                <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-tighter">
                                    {{ $tb->gui_tat_ca ? 'TẤT CẢ' : 'CỤ THỂ' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-xs font-bold text-slate-400">{{ $tb->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-8 py-12 text-center text-slate-400 italic">Chưa có thông báo nào được gửi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($thongBaos->hasPages())
            <div class="p-8 bg-slate-50/50 border-t border-slate-100">
                {{ $thongBaos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
