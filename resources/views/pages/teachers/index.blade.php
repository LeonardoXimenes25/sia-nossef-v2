@extends('layouts.app')

@section('title', 'Lista Professores')

@section('content')
<!-- Team Section -->
<section id="team" class="team section mt-4">

    <style>
        /* 🌟 Style untuk tampilan daftar guru */
        .team {
            padding: 40px 0;
            background-color: #f8f9fa;
        }

        .section-title h2 {
            font-weight: 700;
            font-size: 2rem;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 10px;
        }

        .section-title p {
            text-align: center;
            color: #6c757d;
            margin-bottom: 30px;
        }

        .member-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 25px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .member-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
        }

        .member-image-wrapper {
            width: 100%;
            height: 180px;
            overflow: hidden;
            position: relative;
            flex-shrink: 0;
            cursor: pointer;
        }

        .member-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            border-bottom: 2px solid #f1f1f1;
            transition: transform 0.4s ease;
        }

        .member-image-wrapper:hover img {
            transform: scale(1.03);
        }

        .member-content {
            padding: 15px 12px 12px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .member-info {
            flex-grow: 1;
            margin-bottom: 8px;
        }

        .member-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .member-role {
            display: block;
            font-size: 0.85rem;
            color: #777;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .member-bio {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 12px;
            line-height: 1.3;
        }

        .member-socials {
            margin-top: 5px;
            padding-top: 8px;
            border-top: 1px solid #f0f0f0;
        }

        .member-socials a {
            color: #007bff;
            margin: 0 6px;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .member-socials a:hover {
            color: #0056b3;
            transform: translateY(-2px);
        }

        /* Modal Styles */
        .teacher-modal .modal-dialog {
            max-width: 1000px;
        }

        .teacher-modal .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .teacher-modal .modal-header {
            border-bottom: none;
            padding: 25px 30px 0;
            position: relative;
        }

        .teacher-modal .modal-body {
            padding: 0 30px 30px;
        }

        .teacher-modal .btn-close {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
            background: rgba(0,0,0,0.1);
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .teacher-image-modal {
            height: 350px;
            object-fit: cover;
            object-position: center;
            border-radius: 15px;
            width: 100%;
            margin-bottom: 20px;
        }

        .teacher-header {
            margin-bottom: 25px;
        }

        .teacher-name-modal {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .teacher-title-modal {
            font-size: 1.2rem;
            color: #7f8c8d;
            font-weight: 500;
        }

        /* Grid 2 kolom untuk info */
        .info-grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-column {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .info-item-modal {
            display: flex;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #ecf0f1;
        }

        .info-item-modal:last-child {
            border-bottom: none;
        }

        .info-icon-modal {
            width: 40px;
            height: 40px;
            background: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-icon-modal i {
            color: white;
            font-size: 1.1rem;
        }

        .info-content-modal {
            flex: 1;
        }

        .info-label-modal {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-value-modal {
            color: #34495e;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.4;
        }

        /* Placeholder jika gambar tidak tersedia */
        .image-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #6c757d, #adb5bd);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            font-weight: 600;
        }

        /* Loading state */
        .modal-loading {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .modal-content-loaded {
            display: none;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .member-image-wrapper {
                height: 160px;
            }
        }

        @media (max-width: 992px) {
            .member-image-wrapper {
                height: 150px;
            }
            .member-content {
                padding: 12px 10px 10px;
            }
            .teacher-modal .modal-dialog {
                max-width: 95%;
                margin: 20px auto;
            }
            .info-grid-2col {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            .member-image-wrapper {
                height: 140px;
            }
            .member-name {
                font-size: 1rem;
            }
            .member-socials a {
                margin: 0 4px;
                font-size: 0.95rem;
            }
            .member-socials {
                margin-top: 3px;
                padding-top: 6px;
            }
            .teacher-image-modal {
                height: 250px;
            }
            .teacher-name-modal {
                font-size: 1.6rem;
            }
            .teacher-modal .modal-body {
                padding: 0 20px 20px;
            }
        }

        @media (max-width: 576px) {
            .member-image-wrapper {
                height: 130px;
            }
            .team {
                padding: 30px 0;
            }
            .member-socials {
                margin-top: 2px;
                padding-top: 5px;
            }
            .teacher-modal .modal-header {
                padding: 20px 20px 0;
            }
            .info-item-modal {
                padding: 10px 0;
            }
            .info-icon-modal {
                width: 35px;
                height: 35px;
                margin-right: 12px;
            }
        }
    </style>

    <div class="container section-title mt-4" data-aos="fade-up" style="margin-top: 100px">
        <h2>Lista Professores</h2>
        <p>Our Hardworking Team</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row">
            @foreach ($teachers as $teacher)
            <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                <div class="member-card">
                    <div class="member-image-wrapper" onclick="openTeacherModal({{ $teacher->id }})">
                        @if($teacher->photo && file_exists(public_path('storage/' . $teacher->photo)))
                            <img src="{{ asset('storage/' . $teacher->photo) }}" 
                                class="img-fluid" 
                                alt="{{ $teacher->name }}"
                                loading="lazy">
                        @else
                            <div class="image-placeholder">
                                <span>{{ substr($teacher->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="member-content">
                        <div class="member-info">
                            <h4 class="member-name">{{ $teacher->name }}</h4>
                            <span class="member-role">{{ $teacher->educational_qualification ?? 'Professor' }}</span>
                            <p class="member-bio">
                                {{ $teacher->email }}<br>
                                {{ $teacher->phone }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Single Modal untuk semua guru -->
<div class="modal fade teacher-modal" id="teacherModal" tabindex="-1" aria-labelledby="teacherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Loading State -->
                <div class="modal-loading" id="modalLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat data guru...</p>
                </div>

                <!-- Content yang akan diisi via JavaScript -->
                <div class="modal-content-loaded" id="modalContent">
                    <div class="row">
                        <!-- Kolom Kiri: Foto dan Informasi Pribadi -->
                        <div class="col-md-4">
                            <img id="modalTeacherImage" 
                                class="teacher-image-modal" 
                                alt="Teacher"
                                loading="lazy">
                            
                            <div class="teacher-header">
                                <h2 class="teacher-name-modal" id="modalTeacherName"></h2>
                                <div class="teacher-title-modal" id="modalTeacherTitle"></div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Informasi Detail dalam 2 Kolom -->
                        <div class="col-md-8">
                            <div class="info-grid-2col" id="modalTeacherInfo">
                                <!-- Kolom 1 -->
                                <div class="info-column" id="infoColumn1">
                                    <!-- Informasi akan diisi via JavaScript -->
                                </div>
                                
                                <!-- Kolom 2 -->
                                <div class="info-column" id="infoColumn2">
                                    <!-- Informasi akan diisi via JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Data teachers dari PHP ke JavaScript
const teachersData = {!! json_encode($teachers->map(function($teacher) {
    return [
        'id' => $teacher->id,
        'name' => $teacher->name,
        'photo' => $teacher->photo,
        'educational_qualification' => $teacher->educational_qualification,
        'gender' => $teacher->gender,
        'birth_place' => $teacher->birth_place,
        'birth_date' => $teacher->birth_date,
        'employment_status' => $teacher->employment_status,
        'employment_start_date' => $teacher->employment_start_date,
        'email' => $teacher->email,
        'phone' => $teacher->phone,
    ];
})) !!};

// Format tanggal sederhana (tanpa Carbon)
function formatDate(dateString) {
    if (!dateString) return 'Unknown';
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return 'Invalid Date';
    
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

// Fungsi buka modal
function openTeacherModal(teacherId) {
    const teacher = teachersData.find(t => t.id === teacherId);
    if (!teacher) return;

    // Tampilkan loading
    document.getElementById('modalLoading').style.display = 'block';
    document.getElementById('modalContent').style.display = 'none';

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('teacherModal'));
    modal.show();

    // Set data setelah modal ditampilkan (memberi waktu untuk animasi)
    setTimeout(() => {
        setModalData(teacher);
    }, 300);
}

// Set data ke modal
function setModalData(teacher) {
    // Set basic info
    document.getElementById('modalTeacherName').textContent = teacher.name;
    document.getElementById('modalTeacherTitle').textContent = teacher.educational_qualification || 'Professional Teacher';
    
    // Set image
    const teacherImage = document.getElementById('modalTeacherImage');
    if (teacher.photo) {
        teacherImage.src = "{{ asset('storage/') }}/" + teacher.photo;
        teacherImage.alt = teacher.name;
    } else {
        teacherImage.src = 'https://via.placeholder.com/400x400/3498db/ffffff?text=Guru';
    }

    // Build info grid dengan 2 kolom
    const infoColumn1 = document.getElementById('infoColumn1');
    const infoColumn2 = document.getElementById('infoColumn2');
    infoColumn1.innerHTML = '';
    infoColumn2.innerHTML = '';

    // Definisikan info items untuk kolom 1 (Informasi Pribadi)
    const column1Items = [
        { icon: 'bi-gender-ambiguous', label: 'Sexu', value: teacher.gender || 'Tidak ditentukan' },
        { icon: 'bi-calendar-event', label: 'Fatin Moris', value: teacher.birth_place || 'Tidak diketahui' },
        { icon: 'bi-calendar-date', label: 'Data Moris', value: formatDate(teacher.birth_date) },
        { icon: 'bi-mortarboard', label: 'Abilitasaun Literaria', value: teacher.educational_qualification || 'Tidak ditentukan' },
    ];

    // Definisikan info items untuk kolom 2 (Informasi Profesional)
    const column2Items = [
        { 
            icon: 'bi-envelope', 
            label: 'Email', 
            value: teacher.email ? `<a href="mailto:${teacher.email}" class="text-decoration-none">${teacher.email}</a>` : 'La Eziste' 
        },
        { 
            icon: 'bi-telephone', 
            label: 'Telepon', 
            value: teacher.phone ? `<a href="tel:${teacher.phone}" class="text-decoration-none">${teacher.phone}</a>` : 'La Eziste' 
        }
    ];

    // Isi kolom 1
    column1Items.forEach(item => {
        const infoItem = createInfoItem(item);
        infoColumn1.appendChild(infoItem);
    });

    // Isi kolom 2
    column2Items.forEach(item => {
        const infoItem = createInfoItem(item);
        infoColumn2.appendChild(infoItem);
    });

    // Sembunyikan loading, tampilkan content
    document.getElementById('modalLoading').style.display = 'none';
    document.getElementById('modalContent').style.display = 'block';
}

// Fungsi helper untuk membuat info item
function createInfoItem(item) {
    const infoItem = document.createElement('div');
    infoItem.className = 'info-item-modal';
    infoItem.innerHTML = `
        <div class="info-icon-modal">
            <i class="bi ${item.icon}"></i>
        </div>
        <div class="info-content-modal">
            <div class="info-label-modal">${item.label}</div>
            <div class="info-value-modal">${item.value}</div>
        </div>
    `;
    return infoItem;
}

// Reset modal ketika ditutup
document.getElementById('teacherModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalLoading').style.display = 'block';
    document.getElementById('modalContent').style.display = 'none';
});
</script>
@endsection