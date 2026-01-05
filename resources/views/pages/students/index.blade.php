@extends('layouts.app')

@section('title', 'ESG. NOSSEF | Lista Professores')

@section('content')
<div class="container mt-4">

    <!-- Header Section -->
    <div class="row" style="margin-top: 100px">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-primary mb-1">Lista Estudante</h2>
                <p class="text-muted mb-0">Tinan Akademiku: {{ $academicYear->name ?? 'Not Available' }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 mt-3">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Total Students</h6>
                        <h3 class="fw-bold text-primary" id="totalStudents">{{ $totalStudents }}</h3>
                    </div>
                    <i class="fas fa-users fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Male Students</h6>
                        <h3 class="fw-bold text-info" id="maleStudents">{{ $maleStudents }}</h3>
                    </div>
                    <i class="fas fa-male fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Female Students</h6>
                        <h3 class="fw-bold text-pink" id="femaleStudents">{{ $femaleStudents }}</h3>
                    </div>
                    <i class="fas fa-female fa-2x text-pink opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Classes</h6>
                        <h3 class="fw-bold text-success" id="classes">{{ $classes }}</h3>
                    </div>
                    <i class="fas fa-school fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-2 mb-2">
            <input type="text" id="searchInput" class="form-control" placeholder="Search students...">
        </div>
        <div class="col-md-2 mb-2">
            <select id="classFilter" class="form-select">
                <option value="">All Classes</option>
                @foreach($classOptions as $class)
                    <option value="{{ $class }}">{{ $class }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <select id="turmaFilter" class="form-select">
                <option value="">All Turma</option>
                @foreach($turmaOptions as $turma)
                    <option value="{{ $turma }}">{{ $turma }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select id="majorFilter" class="form-select">
                <option value="">All Majors</option>
                @foreach($majorOptions as $major)
                    <option value="{{ $major }}">{{ $major }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select id="genderFilter" class="form-select">
                <option value="">All Gender</option>
                <option value="m">Mane</option>
                <option value="f">Feto</option>
            </select>
        </div>
    </div>

    <!-- Students Table -->
    <div id="studentsTable">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nu</th>
                            <th>NRE</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Class</th>
                            <th>Turma</th>
                            <th>Major</th>
                        </tr>
                    </thead>
                    <tbody id="studentsBody">
                        @forelse ($students as $student)
                            <tr>
                                <td>{{ $loop->iteration + $students->firstItem() - 1 }}</td>
                                <td>{{ $student->nre }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->sex == 'm' ? 'Mane' : 'Feto' }}</td>
                                <td>{{ $student->classRoom->level ?? '-' }}</td>
                                <td>{{ $student->classRoom->turma ?? '-' }}</td>
                                <td>{{ $student->classRoom->major->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center" id="paginationLinks">
                <div>Showing {{ $students->count() }} of {{ $students->total() }} students</div>
                <div>{{ $students->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
const searchInput = document.getElementById('searchInput');
const classFilter = document.getElementById('classFilter');
const turmaFilter = document.getElementById('turmaFilter');
const majorFilter = document.getElementById('majorFilter');
const genderFilter = document.getElementById('genderFilter');

function loadStudents(page = 1){
    const params = {
        search: searchInput.value,
        class: classFilter.value,
        turma: turmaFilter.value,
        major: majorFilter.value,
        gender: genderFilter.value,
        page: page
    };
    axios.get('{{ route("students.index") }}', { params })
    .then(response=>{
        const parser = new DOMParser();
        const htmlDoc = parser.parseFromString(response.data,'text/html');

        // Replace table body
        document.getElementById('studentsBody').innerHTML = htmlDoc.getElementById('studentsBody').innerHTML;

        // Replace pagination
        document.getElementById('paginationLinks').innerHTML = htmlDoc.getElementById('paginationLinks').innerHTML;

        // Replace stats
        ['totalStudents','maleStudents','femaleStudents','classes'].forEach(id=>{
            document.getElementById(id).innerText = htmlDoc.getElementById(id).innerText;
        });

        // Pagination links click
        document.querySelectorAll('#paginationLinks a').forEach(link=>{
            link.addEventListener('click', function(e){
                e.preventDefault();
                const page = this.getAttribute('href').split('page=')[1];
                loadStudents(page);
            });
        });
    })
    .catch(err=>console.error(err));
}

[searchInput,classFilter,turmaFilter,majorFilter,genderFilter].forEach(el=>{
    el.addEventListener('input', loadStudents);
    el.addEventListener('change', loadStudents);
});

// Initial load
loadStudents();
</script>

<style>
.text-pink { color: #e83e8c; }
.table > :not(caption) > * > * { padding: 0.75rem 0.5rem; }
.card { border-radius: 0.5rem; }
.btn { border-radius: 0.375rem; }
</style>
@endsection
