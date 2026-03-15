@extends('layouts.hoc_vien')

@section('title', 'Lịch học của tôi')

@section('content')
<div class="space-y-6">
    <!-- Header & Bộ lọc -->
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-2 bg-slate-100 p-1 rounded-2xl w-fit">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" 
               class="px-6 py-2 rounded-xl text-sm font-black transition {{ $view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
               📋 DANH SÁCH
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'calendar']) }}" 
               class="px-6 py-2 rounded-xl text-sm font-black transition {{ $view === 'calendar' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
               📅 LỊCH THÁNG
            </a>
        </div>

        <form action="{{ route('hv.lich_hoc.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <input type="hidden" name="view" value="{{ $view }}">
            
            @if($view === 'list')
                <div class="flex items-center space-x-2 bg-slate-50 border border-slate-100 rounded-2xl px-3 py-1">
                    @php 
                        $currentDate = \Carbon\Carbon::now()->setISODate(substr($currentTuan, 0, 4), substr($currentTuan, 6));
                        $prevWeek = $currentDate->copy()->subWeek()->format('Y-\WW');
                        $nextWeek = $currentDate->copy()->addWeek()->format('Y-\WW');
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['tuan' => $prevWeek]) }}" class="p-1 text-slate-400 hover:text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></a>
                    <span class="text-xs font-black text-slate-700 uppercase tracking-widest min-w-[150px] text-center">
                        {{ $currentDate->startOfWeek()->format('d/m') }} - {{ $currentDate->endOfWeek()->format('d/m/Y') }}
                    </span>
                    <a href="{{ request()->fullUrlWithQuery(['tuan' => $nextWeek]) }}" class="p-1 text-slate-400 hover:text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></a>
                </div>
            @endif

            <select name="lop_id" onchange="this.form.submit()" class="rounded-2xl border-slate-200 text-xs font-black uppercase tracking-widest focus:ring-blue-500 focus:border-blue-500 min-w-[200px]">
                <option value="">-- Tất cả lớp --</option>
                @foreach($lopHocs as $lop)
                    <option value="{{ $lop->id }}" {{ request('lop_id') == $lop->id ? 'selected' : '' }}>{{ $lop->ten_lop }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if($view === 'list')
        <!-- CHẾ ĐỘ DANH SÁCH -->
        <div class="space-y-8">
            @php $groupedLich = $lichHocs->groupBy('ngay_hoc'); @endphp
            
            @forelse($groupedLich as $ngay => $items)
                @php 
                    $dateObj = \Carbon\Carbon::parse($ngay);
                    $isToday = $dateObj->isToday();
                @endphp
                <div class="space-y-4">
                    <div class="flex items-center space-x-4 px-4">
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <h3 class="text-sm font-black uppercase tracking-[0.2em] {{ $isToday ? 'text-blue-600' : 'text-slate-400' }}">
                            {{ $dateObj->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}
                            @if($isToday)
                                <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-600 rounded-lg text-[10px]">HÔM NAY</span>
                            @endif
                        </h3>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($items as $lich)
                            <a href="{{ route('hv.lich_hoc.show', $lich->id) }}" class="bg-white p-6 rounded-[2rem] shadow-sm border-l-4 border-y border-r border-slate-100 hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 flex items-center justify-between group
                                {{ $lich->trang_thai === 'huy' ? 'border-l-slate-400 opacity-60' : 'border-l-blue-500' }}">
                                <div class="flex items-center space-x-6">
                                    <div class="text-center min-w-[80px]">
                                        <p class="text-sm font-black text-slate-800">{{ substr($lich->gio_bat_dau, 0, 5) }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ substr($lich->gio_ket_thuc, 0, 5) }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-base font-black text-slate-800 group-hover:text-blue-600 transition">{{ $lich->lopHoc->ten_lop }}</h4>
                                        <p class="text-[10px] text-blue-600 font-black uppercase tracking-widest">{{ $lich->lopHoc->khoaHoc->ten_khoa_hoc }}</p>
                                        <div class="mt-2 flex items-center space-x-4 text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                            <span>P.{{ $lich->phong_hoc }}</span>
                                            <span>GV. {{ $lich->lopHoc->giangVien->name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    @if($lich->trang_thai === 'huy')
                                        <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest">ĐÃ HỦY</span>
                                    @elseif($lich->diem_danh_hv)
                                        @php 
                                            $st = $lich->diem_danh_hv->trang_thai;
                                            $colors = [
                                                'co_mat' => 'bg-emerald-100 text-emerald-700',
                                                'vang_co_phep' => 'bg-amber-100 text-amber-700',
                                                'vang_khong_phep' => 'bg-red-100 text-red-700',
                                                'di_muon' => 'bg-purple-100 text-purple-700',
                                                've_som' => 'bg-blue-100 text-blue-700'
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 {{ $colors[$st] ?? 'bg-slate-100' }} rounded-full text-[10px] font-black uppercase tracking-widest">
                                            {{ str_replace('_', ' ', $st) }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-slate-100 text-slate-400 rounded-full text-[10px] font-black uppercase tracking-widest">CHƯA HỌC</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white p-20 rounded-[2rem] text-center border border-slate-100 shadow-sm">
                    <p class="text-slate-400 italic">Không có lịch học trong khoảng thời gian này.</p>
                </div>
            @endforelse
        </div>
    @else
        <!-- CHẾ ĐỘ LỊCH THÁNG (FULLCALENDAR) -->
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <div id="calendar"></div>
        </div>
    @endif
</div>

<!-- Modal Chi Tiết (Sẽ hiển thị khi click vào event trong calendar) -->
<div id="event-modal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="bg-blue-600 p-6 text-white relative">
            <h3 id="modal-title" class="text-xl font-black uppercase tracking-widest leading-tight"></h3>
            <p id="modal-subtitle" class="text-blue-100 text-xs font-bold mt-1 uppercase tracking-widest"></p>
            <button onclick="closeModal()" class="absolute top-6 right-6 text-white/50 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        <div class="p-8 space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Thời gian</p>
                    <p id="modal-time" class="text-sm font-bold text-slate-700"></p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Phòng học</p>
                    <p id="modal-room" class="text-sm font-bold text-slate-700"></p>
                </div>
                <div class="col-span-2">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Giảng viên</p>
                    <p id="modal-teacher" class="text-sm font-bold text-slate-700"></p>
                </div>
            </div>
            <div id="modal-status-container" class="pt-4 border-t border-slate-50">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Trạng thái điểm danh</p>
                <div id="modal-status" class="inline-block px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest"></div>
            </div>
            <div class="pt-4">
                <a id="modal-link" href="#" class="block w-full text-center py-3 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-2xl transition duration-200 text-xs uppercase tracking-widest">
                    XEM CHI TIẾT BUỔI HỌC
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($view === 'calendar')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'vi',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: "{{ route('hv.lich_hoc.events') }}",
            eventClick: function(info) {
                const props = info.event.extendedProps;
                document.getElementById('modal-title').innerText = props.lop;
                document.getElementById('modal-subtitle').innerText = props.phong;
                document.getElementById('modal-time').innerText = info.event.start.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}) + ' - ' + info.event.end.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
                document.getElementById('modal-room').innerText = props.phong;
                document.getElementById('modal-teacher').innerText = props.giang_vien;
                
                const statusEl = document.getElementById('modal-status');
                const status = props.trang_thai_dd;
                statusEl.innerText = status.replace('_', ' ');
                
                // Color logic
                statusEl.className = "inline-block px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest ";
                if(status === 'co_mat') statusEl.classList.add('bg-emerald-100', 'text-emerald-700');
                else if(status === 'chua_co') statusEl.classList.add('bg-slate-100', 'text-slate-400');
                else statusEl.classList.add('bg-amber-100', 'text-amber-700');

                document.getElementById('modal-link').href = "/hoc-vien/lich-hoc/" + info.event.id;
                document.getElementById('event-modal').classList.remove('hidden');
            }
        });
        calendar.render();
    });

    function closeModal() {
        document.getElementById('event-modal').classList.add('hidden');
    }
</script>
@endif
@endsection

@section('styles')
<style>
    .fc .fc-toolbar-title { font-size: 1.25rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #1e293b; }
    .fc .fc-button-primary { background-color: #2563eb; border-color: #2563eb; font-weight: 800; text-transform: uppercase; font-size: 10px; border-radius: 12px; padding: 8px 16px; }
    .fc .fc-button-primary:hover { background-color: #1d4ed8; border-color: #1d4ed8; }
    .fc .fc-button-active { background-color: #1e40af !important; border-color: #1e40af !important; }
    .fc-event { border-radius: 8px; padding: 2px 4px; font-weight: 700; font-size: 10px; cursor: pointer; transition: transform 0.2s; }
    .fc-event:hover { transform: scale(1.02); }
    .fc .fc-daygrid-day.fc-day-today { background-color: #eff6ff !important; }
</style>
@endsection
