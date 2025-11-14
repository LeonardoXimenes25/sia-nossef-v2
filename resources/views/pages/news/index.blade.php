@extends('layouts.app')

@section('content')
<div class="container-fluid px-0 news-page">
    <!-- Header Section -->
    <div class="news-header bg-primary text-white py-5 mb-5 text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Berita Sekolah</h1>
            <p class="lead mb-3">Informasi terbaru seputar kegiatan dan perkembangan sekolah</p>
            <i class="fas fa-newspaper display-1 opacity-75"></i>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Search and Filter -->
        <div class="row mb-4 g-3">
            <div class="col-md-8">
                <form method="GET" action="{{ route('news.index') }}" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari berita..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </form>
            </div>
            <div class="col-md-4">
                <form method="GET" action="{{ route('news.index') }}">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- Featured News (Carousel) -->
        @if($featuredNews->count() > 0)
        <div class="row mb-5">
            <div class="col-12">
                <h3 class="mb-3 border-bottom pb-2">Berita Utama</h3>
                <div id="featuredCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($featuredNews as $index => $item)
                            <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner rounded-3 overflow-hidden shadow">
                        @foreach($featuredNews as $index => $item)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row g-0 bg-dark text-white">
                                <div class="col-md-6 position-relative">
                                    <img src="{{ $item->image ? asset('storage/'.$item->image) : asset('images/default-news.jpg') }}"
                                         class="img-fluid w-100" alt="{{ $item->title }}" style="height: 350px; object-fit: cover;">
                                    <div class="position-absolute bottom-0 start-0 p-3 bg-dark bg-opacity-50 w-100">
                                        <span class="badge bg-primary mb-2">{{ $item->category->name ?? 'Tanpa Kategori' }}</span>
                                        <h4 class="mb-0">{{ $item->title }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                                    <p class="text-muted mb-2"><i class="far fa-calendar me-2"></i>{{ \Carbon\Carbon::parse($item->published_at)->format('d M Y') }}</p>
                                    <p class="mb-3">{{ Str::limit(strip_tags($item->content), 150) }}</p>
                                    <a href="{{ route('news.show', $item->slug) }}" class="btn btn-outline-light align-self-start">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- News Grid -->
        <div class="row g-4" id="newsGrid">
            @forelse($news as $item)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 news-card shadow-sm border-0 overflow-hidden">
                    <div class="position-relative">
                        <img src="{{ $item->image ? asset('storage/'.$item->image) : asset('images/default-news.jpg') }}" 
                             class="card-img-top" alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
                        <span class="badge bg-primary position-absolute top-0 end-0 m-3">{{ $item->category->name ?? 'Tanpa Kategori' }}</span>
                        <small class="text-white bg-dark bg-opacity-75 px-2 py-1 rounded position-absolute bottom-0 start-0 m-2">
                            <i class="far fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($item->published_at)->format('d M Y') }}
                        </small>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $item->title }}</h5>
                        <p class="card-text text-muted flex-grow-1">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <a href="{{ route('news.show', $item->slug) }}" class="btn btn-sm btn-outline-primary stretched-link">Baca Selengkapnya</a>
                            <small class="text-muted"><i class="far fa-eye me-1"></i> 245</small>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-newspaper display-1 text-muted mb-3"></i>
                <h4 class="text-muted">Belum ada berita</h4>
                <p class="text-muted">Silakan kembali lagi nanti untuk informasi terbaru</p>
            </div>
            @endforelse
        </div>

        <!-- Newsletter Subscription -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="bg-primary text-white rounded-3 p-4 p-md-5 shadow">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-2">Berlangganan Newsletter</h4>
                            <p class="mb-0">Dapatkan update berita terbaru langsung ke email Anda</p>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Alamat email">
                                <button class="btn btn-light text-primary">Berlangganan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                {{ $news->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    /* Jarak dari navbar */
    .news-page {
        padding-top: 100px; /* sesuaikan dengan tinggi navbar */
    }

    .news-header {
        background: linear-gradient(135deg, #3498db, #2c3e50);
    }

    .news-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .carousel-item {
        transition: transform 0.6s ease-in-out;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gridViewBtn = document.querySelector('[data-view="grid"]');
        const listViewBtn = document.querySelector('[data-view="list"]');
        const newsGrid = document.getElementById('newsGrid');

        if(gridViewBtn && listViewBtn) {
            gridViewBtn.addEventListener('click', function() {
                newsGrid.className = 'row g-4';
                gridViewBtn.classList.add('active');
                listViewBtn.classList.remove('active');

                const cards = newsGrid.querySelectorAll('.col-12');
                cards.forEach(card => card.className = 'col-lg-4 col-md-6');
            });

            listViewBtn.addEventListener('click', function() {
                newsGrid.className = 'row g-3';
                const cards = newsGrid.querySelectorAll('.col-lg-4, .col-md-6');
                cards.forEach(card => card.className = 'col-12');
                gridViewBtn.classList.remove('active');
                listViewBtn.classList.add('active');
            });
        }
    });
</script>
@endsection
