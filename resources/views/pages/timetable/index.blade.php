@extends('layouts.app')

@section('title', 'ESG. Nossef | Horariu')

@section('content')
<div class="container" style="margin-top: 100px">
    <!-- Filter -->
    <div class="filter-section">
        <div class="card border shadow-sm">
            <div class="card-header py-3" style="background-color: #0099ff;">
                <h6 class="mb-0 text-white"><i class="fas fa-filter me-2"></i>Filter Orariu</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Kelas -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-primary"><i class="fas fa-door-open me-2"></i>Hili Klase</label>
                        <select class="form-select border" id="class-select">
                            <option value="all" selected>Klase Hotu</option>
                            @foreach($classes as $class)
                                <option value="{{ $class }}">{{ $class }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Turma -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-primary"><i class="fas fa-users me-2"></i>Hili Turma</label>
                        <select class="form-select border" id="turma-select">
                            <option value="all" selected>Turma Hotu</option>
                            @foreach($turmas as $turma)
                                <option value="{{ $turma }}">{{ $turma }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jurusan -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-primary"><i class="fas fa-graduation-cap me-2"></i>Hili Area Estudu</label>
                        <select class="form-select border" id="major-select">
                            <option value="all" selected>Area Estudu Hotu</option>
                            @foreach($majors as $major)
                                <option value="{{ $major }}">{{ $major }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Guru -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary"><i class="fas fa-chalkboard-teacher me-2"></i>Hili Professor</label>
                        <select class="form-select border" id="teacher-select">
                            <option value="all" selected>Professor Hotu</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher }}">{{ $teacher }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Period -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary"><i class="fas fa-chalkboard-teacher me-2"></i>Hili Periodu</label>
                        <select class="form-select border" id="period-select">
                            <option value="all">Períudu Hotu</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" {{ isset($activePeriod) && $activePeriod->id == $period->id ? 'selected' : '' }}>
                                    {{ $period->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    

                    <!-- Academic Year -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-primary"><i class="fas fa-calendar-alt me-2"></i>Hili Tinan Akademiku</label>
                        <select class="form-select border" id="year-select">
                            <option value="all" selected>Tinan Akademiku Hotu</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Hari -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary mb-2"><i class="fas fa-calendar-day me-2"></i>Hili Loron</label>
                        <div class="d-flex flex-wrap gap-1">
                            @php
                                $days = [
                                    'all' => ['icon' => 'fas fa-list', 'text' => 'Hotu'],
                                    'Monday' => ['icon' => 'fas fa-calendar', 'text' => 'Segunda'],
                                    'Tuesday' => ['icon' => 'fas fa-calendar', 'text' => 'Tersa'],
                                    'Wednesday' => ['icon' => 'fas fa-calendar', 'text' => 'Kuarta'],
                                    'Thursday' => ['icon' => 'fas fa-calendar', 'text' => 'Kinta'],
                                    'Friday' => ['icon' => 'fas fa-calendar', 'text' => 'Sexta'],
                                    'Saturday' => ['icon' => 'fas fa-calendar', 'text' => 'Sabadu']
                                ];
                            @endphp
                            @foreach($days as $dayKey => $dayInfo)
                                <button class="day-tab btn btn-outline-primary btn-sm rounded-pill px-3 {{ $dayKey === 'all' ? 'active' : '' }}" 
                                        data-day="{{ $dayKey }}">
                                    <i class="{{ $dayInfo['icon'] }} me-1"></i>
                                    {{ $dayInfo['text'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Jadwal -->
    <div class="card border shadow-sm mt-3">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background-color: #0099ff;">
            <h6 class="mb-0 text-white d-flex align-items-center">
                <i class="fas fa-table me-2"></i>Orariu Materia
            </h6>
            <div class="d-flex align-items-center gap-2">
                <span class="badge text-white" id="schedule-count" style="background-color: #006fd6;">
                    {{ count($timetables) }} Horariu
                </span>
                <button id="download-btn" class="btn btn-light btn-sm shadow-sm">
                    <i class="fas fa-download me-1"></i>Download Orariu
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="schedule-table">
                    <thead class="text-white" style="background-color: #0099ff;">
                        <tr>
                            <th>Loron</th>
                            <th>Oras</th>
                            <th>Materia</th>
                            <th>Professor</th>
                            <th>Klase</th>
                            <th>Turma</th>
                            <th>Area Estudu</th>
                            <th>Períudu</th>
                            <th>Tinan Akademiku</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $dayNames = [
                                'Monday' => 'Segunda',
                                'Tuesday' => 'Tersa', 
                                'Wednesday' => 'Kuarta',
                                'Thursday' => 'Kinta',
                                'Friday' => 'Sexta',
                                'Saturday' => 'Sabadu',
                            ];
                        @endphp
                        @foreach($timetables as $tt)
                        <tr data-class="{{ optional($tt->classRoom)->level ?? '-' }}"
                            data-turma="{{ optional($tt->classRoom)->turma ?? '-' }}"
                            data-major="{{ optional(optional($tt->classRoom)->major)->name ?? '-' }}"
                            data-teacher="{{ optional(optional($tt->subjectAssignment)->teacher)->name ?? '-' }}"
                            data-day="{{ $tt->day }}"
                            data-period="{{ $tt->period_id ?? 'all' }}"
                            data-academic-year="{{ $tt->academic_year_id ?? 'all' }}">
                            <td>{{ $dayNames[$tt->day] ?? $tt->day }}</td>
                            <td>{{ $tt->start_time }} - {{ $tt->end_time }}</td>
                            <td>{{ optional($tt->subject)->name ?? '-' }}</td>
                            <td>{{ optional(optional($tt->subjectAssignment)->teacher)->name ?? '-' }}</td>
                            <td>{{ optional($tt->classRoom)->level ?? '-' }}</td>
                            <td>{{ optional($tt->classRoom)->turma ?? '-' }}</td>
                            <td>{{ optional(optional($tt->classRoom)->major)->name ?? '-' }}</td>
                            <td>{{ optional($tt->period)->name ?? '-' }}</td>
                            <td>{{ optional($tt->academicYear)->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="empty-state" class="text-center py-5" style="display: none;">
        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
        <h5 class="text-muted mb-2">Orariu la eziste</h5>
        <p class="text-muted mb-3">Koko muda filter atu haree orariu seluk</p>
        <button class="btn text-white btn-sm" style="background-color: #0099ff;" onclick="resetFilters()">
            <i class="fas fa-refresh me-1"></i>Reset Filter
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class-select');
    const turmaSelect = document.getElementById('turma-select');
    const teacherSelect = document.getElementById('teacher-select');
    const majorSelect = document.getElementById('major-select');
    const periodSelect = document.getElementById('period-select');
    const yearSelect = document.getElementById('year-select');
    const dayTabs = document.querySelectorAll('.day-tab');
    const rows = document.querySelectorAll('#schedule-table tbody tr');
    const scheduleCount = document.getElementById('schedule-count');
    const emptyState = document.getElementById('empty-state');
    const scheduleTable = document.getElementById('schedule-table');

    function applyFilters() {
        const selectedClass = classSelect.value;
        const selectedTurma = turmaSelect.value;
        const selectedTeacher = teacherSelect.value;
        const selectedMajor = majorSelect.value;
        const selectedPeriod = periodSelect.value;
        const selectedYear = yearSelect.value;
        const activeDay = document.querySelector('.day-tab.active').dataset.day;
        let visible = 0;

        const dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const visibleRows = [];

        rows.forEach(row => {
            const matches = 
                (selectedClass === 'all' || row.dataset.class === selectedClass) &&
                (selectedTurma === 'all' || row.dataset.turma === selectedTurma) &&
                (selectedTeacher === 'all' || row.dataset.teacher === selectedTeacher) &&
                (selectedMajor === 'all' || row.dataset.major === selectedMajor) &&
                (selectedPeriod === 'all' || row.dataset.period == selectedPeriod) &&
                (selectedYear === 'all' || row.dataset.academicYear == selectedYear) &&
                (activeDay === 'all' || row.dataset.day === activeDay);

            if (matches) {
                visibleRows.push(row);
                visible++;
            }
            row.style.display = matches ? '' : 'none';
        });

        visibleRows.sort((a, b) => dayOrder.indexOf(a.dataset.day) - dayOrder.indexOf(b.dataset.day));
        const tbody = scheduleTable.querySelector('tbody');
        
        visibleRows.forEach(row => {
            tbody.appendChild(row);
        });

        scheduleCount.textContent = `${visible} Orariu`;
        scheduleTable.style.display = visible ? 'table' : 'none';
        emptyState.style.display = visible ? 'none' : 'block';
    }

    [classSelect, turmaSelect, teacherSelect, majorSelect, periodSelect, yearSelect].forEach(el => el.addEventListener('change', applyFilters));
    dayTabs.forEach(tab => tab.addEventListener('click', () => {
        dayTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        applyFilters();
    }));

    window.resetFilters = () => {
        classSelect.value = turmaSelect.value = teacherSelect.value = majorSelect.value = periodSelect.value = yearSelect.value = 'all';
        dayTabs.forEach(t => t.classList.remove('active'));
        document.querySelector('.day-tab[data-day="all"]').classList.add('active');
        applyFilters();
    };

    applyFilters();

    document.getElementById('download-btn').addEventListener('click', function() {
        const query = new URLSearchParams({
            class: classSelect.value,
            turma: turmaSelect.value,
            teacher: teacherSelect.value,
            major: majorSelect.value,
            period: periodSelect.value,
            year: yearSelect.value,
            day: document.querySelector('.day-tab.active').dataset.day
        }).toString();

        window.location.href = `/horariu/download?${query}`;
    });
});
</script>
@endsection
