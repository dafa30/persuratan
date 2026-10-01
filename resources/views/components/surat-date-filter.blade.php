<form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-end mb-4 surat-date-filter">
    <div class="col-12 col-sm-4 col-lg-3">
        <label for="filterYear" class="form-label small mb-1">Tahun</label>
        <select id="filterYear" name="year" class="form-select">
            <option value="">Semua tahun</option>
            @foreach($years as $year)
                <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>{{ $year }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-sm-4 col-lg-3">
        <label for="filterMonth" class="form-label small mb-1">Bulan</label>
        <select id="filterMonth" name="month" class="form-select">
            <option value="">Semua bulan</option>
            @foreach(range(1, 12) as $month)
                <option value="{{ $month }}" @selected((int) request('month') === $month)>
                    {{ \Carbon\Carbon::create()->month($month)->isoFormat('MMMM') }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-sm-4 col-lg-3">
        <label for="filterDay" class="form-label small mb-1">Tanggal</label>
        <select id="filterDay" name="day" class="form-select">
            <option value="">Semua tanggal</option>
            @foreach(range(1, 31) as $day)
                <option value="{{ $day }}" @selected((int) request('day') === $day)>{{ $day }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-lg-auto d-flex gap-2">
        <button type="submit" class="btn btn-dark"><i class="bi bi-funnel me-1" aria-hidden="true"></i>Filter</button>
        @if(request()->hasAny(['year', 'month', 'day']))
            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
        @endif
    </div>
</form>