<style>
.org-box {
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 2rem 1rem;
    background-color: #fff;
}

/* Card teacher */
.card-teacher {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    padding: 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    margin-bottom: 1rem;
}

/* Foto square */
.photo-square {
    width: 100%;
    height: 100%;
    overflow: hidden;
    background-color: #f1f1f1;
}

.photo-square img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.photo-xl { width: 120px; height: 120px; }
.photo-lg { width: 100px; height: 100px; }
.photo-md { width: 90px; height: 90px; }

/* Hierarchy lines */
.h-line {
    width: 2px;
    height: 30px;
    background-color: #6c757d;
    margin: 0 auto;
}
.level-row {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
}
</style>

<div class="container org-box text-center">

    <h2 class="fw-bold mb-3">Estrutura Organizacional Eskola</h2>
    <p class="text-muted mb-5">Hierarki pimpinan dan staf sekolah</p>

    {{-- LEVEL 1: Kepala Sekolah --}}
    <div class="level-row justify-content-center mb-3">
        @foreach ($teachers as $teacher)
            @if ($teacher->teacherPosition->level == 1)
                <div class="card-teacher">
                    <div class="photo-square photo-xl mb-3">
                        <img src="{{ $teacher->photo ? asset('storage/' . $teacher->photo) : asset('assets/img/default-avatar.png') }}"
                             alt="{{ $teacher->name }}">
                    </div>
                    <h5>{{ $teacher->name }}</h5>
                    <p>{{ $teacher->teacherPosition->name }}</p>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Connector dari Kepala ke Wakil --}}
    <div class="h-line mb-3"></div>

    {{-- LEVEL 2: Wakil Kepala --}}
    <div class="level-row">
        @foreach ($teachers as $teacher)
            @if ($teacher->teacherPosition->level == 2)
                <div class="card-teacher">
                    <div class="photo-square photo-lg mb-3">
                        <img src="{{ $teacher->photo ? asset('storage/' . $teacher->photo) : asset('assets/img/default-avatar.png') }}"
                             alt="{{ $teacher->name }}">
                    </div>
                    <h6>{{ $teacher->name }}</h6>
                    <p>{{ $teacher->teacherPosition->name }}</p>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Connector dari Wakil ke Staf --}}
    <div class="h-line mt-3 mb-3"></div>

    {{-- LEVEL 3+: Guru / Staf --}}
    <div class="level-row flex-wrap">
        @foreach ($teachers as $teacher)
            @if ($teacher->teacherPosition->level >= 3)
                <div class="card-teacher">
                    <div class="photo-square photo-md mb-2">
                        <img src="{{ $teacher->photo ? asset('storage/' . $teacher->photo) : asset('assets/img/default-avatar.png') }}"
                             alt="{{ $teacher->name }}">
                    </div>
                    <h6>{{ $teacher->name }}</h6>
                    <p>{{ $teacher->teacherPosition->name }}</p>
                </div>
            @endif
        @endforeach
    </div>

</div>
