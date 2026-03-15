@extends('layouts.giang_vien')

@section('title', 'Lịch dạy của tôi')

@section('content')
<!-- Banner Tóm tắt -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100 flex items-center">
        <div class="p-4 bg-emerald-100 text-emerald-600 rounded-xl mr-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-12 0 9 9 0 0112 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Hôm nay</p>
            <p class="text-2xl font-bold text-slate-800">{{ $lichHomNay->count() }} buổi dạy</p>
            @if($lichHomNay->count() > 0)
                <p class="text-xs text-emerald-600 font-bold">Bắt đầu: {{ substr($lichHomNay->first()->gio_bat_dau, 0, 5) }}</p>
            @endif
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-blue-100 flex items-center">
        <div class="p-4 bg-blue-100 text-blue-600 rounded-xl mr-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Tuần này</p>
            <p class="text-2xl font-bold text-slate-800">{{ $lichTuanNayCount }} buổi dạy</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-purple-100 flex items-center">
        <div class="p-4 bg-purple-100 text-purple-600 rounded-xl mr-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Tháng này</p>
            <p class="text-2xl font-bold text-slate-800">{{ $tongBuoiThangCount }} buổi dạy</p>
        </div>
    </div>
</div>

<div x-data="{ viewMode: 'list' }">
    <!-- Toggle View -->
    <div class="flex justify-between items-center mb-6">
        <div class="bg-slate-200 p-1 rounded-xl flex">
            <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white shadow-md text-emerald-600' : 'text-slate-600 hover:bg-slate-300'" class="px-6 py-2 rounded-lg text-sm font-bold transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                Danh sách
            </button>
            <button @click="viewMode = 'calendar'; setTimeout(() => renderCalendar(), 100)" :class="viewMode === 'calendar' ? 'bg-white shadow-md text-emerald-600' : 'text-slate-600 hover:bg-slate-300'" class="px-6 py-2 rounded-lg text-sm font-bold transition duration-200 flex items-center ml-1">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Lịch tháng
            </button>
        </div>

        <!-- Filters (chỉ hiện khi ở mode list) -->
        <div x-show="viewMode === 'list'" class="flex space-x-3">
            <form action="{{ route('gv.lich_day.index') }}" method="GET" class="flex space-x-3">
                <select name="lop_hoc_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Tất cả lớp</option>
                    @foreach($lopHocs as $lop)
                        <option value="{{ $lop->id }}" {{ request('lop_hoc_id') == $lop->id ? 'selected' : '' }}>{{ $lop->ma_lop }} - {{ $lop->ten_lop }}</option>
                    @endforeach
                </select>
                <select name="trang_thai" onchange="this.form.submit()" class="rounded-xl border-slate-200 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="da_len_lich" {{ request('trang_thai') == 'da_len_lich' ? 'selected' : '' }}>Sắp dạy</option>
                    <option value="hoan_thanh" {{ request('trang_thai') == 'hoan_thanh' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="huy" {{ request('trang_thai') == 'huy' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </form>
        </div>
    </div>

    <!-- LIST VIEW -->
    <div x-show="viewMode === 'list'" class="space-y-8">
        @forelse($lichHocs->groupBy('ngay_hoc') as $date => $dayLichs)
            <div>
                @php 
                    $carbonDate = \Carbon\Carbon::parse($date);
                    $isToday = $carbonDate->isToday();
                @endphp
                <h3 class="flex items-center text-lg font-bold mb-4 {{ $isToday ? 'text-emerald-600' : 'text-slate-700' }}">
                    <span class="bg-white px-4 py-1 rounded-full shadow-sm border border-slate-100">
                        {{ $carbonDate->translatedFormat('l, d/m/Y') }}
                        @if($isToday) <span class="ml-2 text-xs uppercase bg-emerald-500 text-white px-2 py-0.5 rounded-full">Hôm nay</span> @endif
                    </span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($dayLichs as $lich)
                        <div class="bg-white rounded-2xl shadow-sm border-l-4 overflow-hidden hover:shadow-md transition duration-200 
                            {{ $lich->trang_thai == 'da_len_lich' ? 'border-emerald-500' : ($lich->trang_thai == 'hoan_thanh' ? 'border-slate-400' : 'border-red-500') }}">
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center text-slate-500 text-sm font-bold">
                                        <svg class="w-4 h-4 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-12 0 9 9 0 0112 0z"></path></svg>
                                        {{ substr($lich->gio_bat_dau, 0, 5) }} - {{ substr($lich->gio_ket_thuc, 0, 5) }}
                                    </div>
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full uppercase 
                                        {{ $lich->trang_thai == 'da_len_lich' ? 'bg-emerald-100 text-emerald-700' : ($lich->trang_thai == 'hoan_thanh' ? 'bg-slate-100 text-slate-700' : 'bg-red-100 text-red-700') }}">
                                        {{ $lich->trang_thai }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-slate-800 text-lg mb-1">{{ $lich->lopHoc->ten_lop }}</h4>
                                <p class="text-xs text-slate-500 mb-4">{{ $lich->lopHoc->khoaHoc->ten_khoa_hoc }}</p>
                                
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="flex items-center text-xs text-slate-600">
                                        <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $lich->phong_hoc ?? 'N/A' }}
                                    </div>
                                    <div class="flex items-center text-xs text-slate-600">
                                        <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        {{ $lich->lopHoc->hocViens->count() }} HV
                                    </div>
                                </div>

                                <div class="flex space-x-2 border-t pt-4 mt-2">
                                    <a href="{{ route('gv.lich_day.show', $lich->id) }}" class="flex-1 text-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-lg transition duration-150">Chi tiết</a>
                                    
                                    @if($isToday && $lich->trang_thai !== 'huy')
                                        <a href="{{ route('gv.diem_danh.create', ['lich_hoc_id' => $lich->id]) }}" class="flex-1 text-center py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition duration-150">Điểm danh</a>
                                    @endif

                                    @if($lich->trang_thai === 'da_len_lich')
                                        <a href="{{ route('gv.yeu_cau.create', ['lich_hoc_id' => $lich->id]) }}" class="flex-1 text-center py-2 border border-emerald-200 text-emerald-600 hover:bg-emerald-50 text-xs font-bold rounded-lg transition duration-150">Đổi lịch</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white p-12 rounded-3xl text-center border border-slate-100 shadow-sm">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-slate-500 font-medium">Không có lịch dạy nào trong khoảng thời gian này.</p>
            </div>
        @endforelse
    </div>

    <!-- CALENDAR VIEW -->
    <div x-show="viewMode === 'calendar'" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 hidden" id="calendar-container">
        <div id="calendar"></div>
    </div>
</div>

<!-- Modal Event Detail (Calendar) -->
<div id="event-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-xl font-bold text-slate-800" id="m-title">Chi tiết buổi học</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        <div class="space-y-3 mb-6">
            <div class="flex justify-between">
                <span class="text-slate-500 text-sm font-medium">Lớp:</span>
                <span class="text-slate-800 text-sm font-bold" id="m-lop">---</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 text-sm font-medium">Giờ học:</span>
                <span class="text-slate-800 text-sm font-bold" id="m-gio">---</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 text-sm font-medium">Phòng:</span>
                <span class="text-emerald-600 text-sm font-bold" id="m-phong">---</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 text-sm font-medium">Trạng thái:</span>
                <span id="m-status" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase">---</span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <a href="#" id="btn-detail" class="text-center py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">Xem chi tiết</a>
            <a href="#" id="btn-attendance" class="text-center py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition hidden">Điểm danh</a>
            <a href="#" id="btn-change" class="text-center py-2 border border-emerald-200 text-emerald-600 hover:bg-emerald-50 font-bold rounded-xl transition hidden">Đổi lịch</a>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
<style>
    .fc { --fc-border-color: #f1f5f9; font-size: 0.9rem; }
    .fc-header-toolbar { margin-bottom: 1.5rem !important; }
    .fc-toolbar-title { font-weight: 800 !important; color: #1e293b; font-size: 1.25rem !important; }
    .fc-button-primary { background-color: #fff !important; border-color: #e2e8f0 !important; color: #475569 !important; font-weight: 700 !important; border-radius: 0.75rem !important; }
    .fc-button-primary:hover { background-color: #f8fafc !important; color: #10b981 !important; }
    .fc-button-active { background-color: #10b981 !important; border-color: #10b981 !important; color: #fff !important; }
    .fc-daygrid-day-number { font-weight: 700; color: #64748b; padding: 0.5rem !important; }
    .fc-event { border-radius: 0.5rem !important; padding: 2px 4px !important; border: none !important; font-weight: 600 !important; }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    let calendar = null;
    function renderCalendar() {
        const container = document.getElementById('calendar-container');
        container.classList.remove('hidden');
        
        if (calendar) {
            calendar.updateSize();
            return;
        }
        
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'vi',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: '{{ route("gv.lich_day.events") }}',
            eventClick: function(info) {
                const props = info.event.extendedProps;
                const id = info.event.id;
                
                document.getElementById('m-lop').innerText = props.lop;
                document.getElementById('m-gio').innerText = info.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + ' - ' + info.event.end.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                document.getElementById('m-phong').innerText = props.phong || '---';
                
                const st = document.getElementById('m-status');
                st.innerText = props.trang_thai;
                st.className = 'px-2 py-0.5 rounded text-[10px] font-bold uppercase ' + 
                    (props.trang_thai === 'da_len_lich' ? 'bg-emerald-100 text-emerald-700' : 
                    (props.trang_thai === 'hoan_thanh' ? 'bg-slate-100 text-slate-700' : 'bg-red-100 text-red-700'));

                document.getElementById('btn-detail').href = `/giang-vien/lich-day/${id}`;
                
                const btnAtt = document.getElementById('btn-attendance');
                const btnChange = document.getElementById('btn-change');
                
                // Show attendance if today
                const todayStr = new Date().toISOString().split('T')[0];
                const eventDateStr = info.event.start.toISOString().split('T')[0];
                
                if (todayStr === eventDateStr && props.trang_thai !== 'huy') {
                    btnAtt.classList.remove('hidden');
                    btnAtt.href = `/giang-vien/diem-danh/create?lich_hoc_id=${id}`;
                } else {
                    btnAtt.classList.add('hidden');
                }

                if (props.trang_thai === 'da_len_lich') {
                    btnChange.classList.remove('hidden');
                    btnChange.href = `/giang-vien/yeu-cau-doi-lich/create?lich_hoc_id=${id}`;
                } else {
                    btnChange.classList.add('hidden');
                }

                document.getElementById('event-modal').classList.remove('hidden');
            }
        });
        calendar.render();
    }

    function closeModal() {
        document.getElementById('event-modal').classList.add('hidden');
    }
</script>
@endsection
