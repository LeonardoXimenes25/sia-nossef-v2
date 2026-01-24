@extends('layouts.app')

@section('title', 'ESG. Nossef | Horariu')

@section('content')
<div class="container" style="margin-top: 100px">

    <!-- FILTER -->
    <div class="card border shadow-sm mb-3">
        <div class="card-header py-3" style="background-color:#0099ff;">
            <h6 class="mb-0 text-white">
                <i class="fas fa-filter me-2"></i>Filter Orariu
            </h6>
        </div>

        <div class="card-body">
            <div class="row g-3">

                <!-- Kelas -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-primary">Hili Klase</label>
                    <select class="form-select" id="class-select">
                        <option value="all">Klase Hotu</option>
                        @foreach($classes as $class)
                            <option value="{{ $class }}">{{ $class }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Turma -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-primary">Hili Turma</label>
                    <select class="form-select" id="turma-select">
                        <option value="all">Turma Hotu</option>
                        @foreach($turmas as $turma)
                            <option value="{{ $turma }}">{{ $turma }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Major -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-primary">Hili Area Estudu</label>
                    <select class="form-select" id="major-select">
                        <option value="all">Area Estudu Hotu</option>
                        @foreach($majors as $major)
                            <option value="{{ $major }}">{{ $major }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Teacher -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary">Hili Professor</label>
                    <select class="form-select" id="teacher-select">
                        <option value="all">Professor Hotu</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher }}">{{ $teacher }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Period -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary">Hili Periodu</label>
                    <select class="form-select" id="period-select">
                        <option value="all">Períudu Hotu</option>
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}"
                                {{ isset($activePeriod) && $activePeriod->id == $period->id ? 'selected' : '' }}>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Academic Year -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-primary">Hili Tinan Akademiku</label>
                    <select class="form-select" id="year-select">
                        <option value="all">Tinan Akademiku Hotu</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- DAY (DROPDOWN + AUTO TODAY) -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary">Hili Loron</label>
                    <select class="form-select" id="day-select">
                        <option value="all">Loron Hotu</option>
                        <option value="Monday">Segunda</option>
                        <option value="Tuesday">Tersa</option>
                        <option value="Wednesday">Kuarta</option>
                        <option value="Thursday">Kinta</option>
                        <option value="Friday">Sexta</option>
                        <option value="Saturday">Sabadu</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card border shadow-sm">
        <div class="card-header d-flex justify-content-between" style="background:#0099ff;">
            <h6 class="mb-0 text-white">Orariu Materia</h6>
            <div>
                <span class="badge bg-primary" id="schedule-count">{{ count($timetables) }} Orariu</span>
                <button id="download-btn" class="btn btn-light btn-sm ms-2">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0" id="schedule-table">
                <thead class="text-white" style="background:#0099ff;">
                    <tr>
                        <th>Loron</th>
                        <th>Oras</th>
                        <th>Materia</th>
                        <th>Professor</th>
                        <th>Klase</th>
                        <th>Turma</th>
                        <th>Area Estudu</th>
                        <th>Periodu</th>
                        <th>Tinan Akademiku</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timetables as $tt)
                    <tr
                        data-class="{{ optional($tt->classRoom)->level }}"
                        data-turma="{{ optional($tt->classRoom)->turma }}"
                        data-major="{{ optional(optional($tt->classRoom)->major)->name }}"
                        data-teacher="{{ optional(optional($tt->subjectAssignment)->teacher)->name }}"
                        data-day="{{ $tt->day }}"
                        data-period="{{ $tt->period_id }}"
                        data-academic-year="{{ $tt->academic_year_id }}"
                    >
                        <td>{{ $tt->day }}</td>
                        <td>{{ $tt->start_time }} - {{ $tt->end_time }}</td>
                        <td>{{ optional($tt->subject)->name }}</td>
                        <td>{{ optional(optional($tt->subjectAssignment)->teacher)->name }}</td>
                        <td>{{ optional($tt->classRoom)->level }}</td>
                        <td>{{ optional($tt->classRoom)->turma }}</td>
                        <td>{{ optional(optional($tt->classRoom)->major)->name }}</td>
                        <td>{{ optional($tt->period)->name }}</td>
                        <td>{{ optional($tt->academicYear)->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="empty-state" class="text-center py-5" style="display:none;">
        <h5 class="text-muted">Orariu la eziste</h5>
        <button onclick="resetFilters()" class="btn btn-primary btn-sm">Reset Filter</button>
    </div>
</div>

<!-- ================= JAVASCRIPT ================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const selects = {
        class: document.getElementById('class-select'),
        turma: document.getElementById('turma-select'),
        teacher: document.getElementById('teacher-select'),
        major: document.getElementById('major-select'),
        period: document.getElementById('period-select'),
        year: document.getElementById('year-select'),
        day: document.getElementById('day-select'),
    };

    const rows = document.querySelectorAll('#schedule-table tbody tr');
    const count = document.getElementById('schedule-count');
    const empty = document.getElementById('empty-state');

    function setToday() {
        const map = {1:'Monday',2:'Tuesday',3:'Wednesday',4:'Thursday',5:'Friday',6:'Saturday'};
        selects.day.value = map[new Date().getDay()] ?? 'all';
    }

    function applyFilters() {
        let visible = 0;

        rows.forEach(row => {
            const show =
                (selects.class.value === 'all' || row.dataset.class === selects.class.value) &&
                (selects.turma.value === 'all' || row.dataset.turma === selects.turma.value) &&
                (selects.teacher.value === 'all' || row.dataset.teacher === selects.teacher.value) &&
                (selects.major.value === 'all' || row.dataset.major === selects.major.value) &&
                (selects.period.value === 'all' || row.dataset.period == selects.period.value) &&
                (selects.year.value === 'all' || row.dataset.academicYear == selects.year.value) &&
                (selects.day.value === 'all' || row.dataset.day === selects.day.value);

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        count.textContent = visible + ' Orariu';
        empty.style.display = visible ? 'none' : 'block';
    }

    Object.values(selects).forEach(el => el.addEventListener('change', applyFilters));

    window.resetFilters = function () {
        Object.values(selects).forEach(el => el.value = 'all');
        setToday();
        applyFilters();
    };

    setToday();      // ⬅️ DEFAULT = HARI INI
    applyFilters();  // ⬅️ AUTO FILTER
});
</script>
@endsection
