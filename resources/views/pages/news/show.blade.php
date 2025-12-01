@extends('layouts.app')

@section('content')
<div class="container mt-5" style="padding-top: 50px">
    <!-- Header Section -->
    <div class="news-header text-white py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white text-decoration-none"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('news.index') }}" class="text-white text-decoration-none">Notisia</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ Str::limit($news->title, 30) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <article class="news-article">
                    <!-- Article Header -->
                    <header class="mb-4">
                        <span class="badge bg-primary mb-2">{{ $news->category->name ?? 'Tanpa Kategori' }}</span>
                        <h1 class="display-5 fw-bold mb-3">{{ $news->title }}</h1>
                        <div class="d-flex flex-wrap align-items-center text-muted mb-4">
                            <div class="d-flex align-items-center me-4 mb-2">
                                <i class="far fa-calendar me-2"></i>
                                <span>{{ \Carbon\Carbon::parse($news->published_at)->format('d F Y') }}</span>
                            </div>
                            <div class="d-flex align-items-center me-4 mb-2">
                                <i class="far fa-clock me-2"></i>
                                <span>{{ \Carbon\Carbon::parse($news->published_at)->format('H:i') }} WIB</span>
                            </div>
                            <div class="d-flex align-items-center me-4 mb-2">
                                <i class="far fa-eye me-2"></i>
                                <span>{{ $news->views ?? 0 }} dilihat</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="far fa-user me-2"></i>
                                <span>{{ $news->author->name ?? 'Admin Sekolah' }}</span>
                            </div>
                        </div>
                    </header>

                    <!-- Featured Image -->
                    @if($news->image)
                    <div class="featured-image mb-4">
                        <img src="{{ asset('storage/'.$news->image) }}" alt="{{ $news->title }}" 
                             class="img-fluid rounded-3 w-100 shadow" style="max-height: 500px; object-fit: cover;">
                        @if($news->image_caption)
                        <figcaption class="text-center text-muted mt-2 small">{{ $news->image_caption }}</figcaption>
                        @endif
                    </div>
                    @endif

                    <!-- Article Content -->
                    <div class="article-content mb-5">
                        <div class="content-body">
                            {!! $news->content !!}
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($news->tags && $news->tags->count() > 0)
                    <div class="tags-section mb-5">
                        <h6 class="mb-3"><i class="fas fa-tags me-2"></i>Tags:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($news->tags as $tag)
                            <a href="{{ route('news.tag', $tag->slug) }}" class="badge bg-light text-dark text-decoration-none border">
                                #{{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Share Section -->
                    <div class="share-section mb-5 p-4 bg-light rounded-3">
                        <h6 class="mb-3">Bagikan berita ini:</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="" class="btn btn-sm btn-primary d-flex align-items-center">
                                <i class="fab fa-facebook-f me-2"></i> Facebook
                            </a>
                            <a href="#" class="btn btn-sm btn-info d-flex align-items-center text-white">
                                <i class="fab fa-twitter me-2"></i> Twitter
                            </a>
                            <a href="#" class="btn btn-sm btn-danger d-flex align-items-center">
                                <i class="fab fa-whatsapp me-2"></i> WhatsApp
                            </a>
                            <a href="#" class="btn btn-sm btn-secondary d-flex align-items-center">
                                <i class="fas fa-link me-2"></i> Salin Link
                            </a>
                        </div>
                    </div>

                    <!-- Author Section -->
                    <div class="author-section mb-5 p-4 bg-light rounded-3 d-flex align-items-center gap-3">
                        <div class="author-avatar rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-user text-white fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $news->author->name ?? 'Admin Sekolah' }}</h6>
                            <p class="text-muted small mb-0">Penulis Berita</p>
                            @if($news->author && $news->author->bio)
                            <p class="small text-muted mt-1 mb-0">{{ $news->author->bio }}</p>
                            @endif
                        </div>
                    </div>
                </article>

                <!-- Navigation Between Posts -->
                <div class="post-navigation d-flex justify-content-between mb-5 flex-wrap gap-2">
                    @if($previousNews)
                    <a href="{{ route('news.show', $previousNews->id) }}" class="btn btn-outline-primary d-flex align-items-center">
                        <i class="fas fa-chevron-left me-2"></i>
                        <div class="text-start">
                            <small class="d-block text-muted">Sebelumnya</small>
                            <span class="fw-medium">{{ Str::limit($previousNews->title, 30) }}</span>
                        </div>
                    </a>
                    @endif

                    @if($nextNews)
                    <a href="{{ route('news.show', $nextNews->id) }}" class="btn btn-outline-primary d-flex align-items-center">
                        <div class="text-end">
                            <small class="d-block text-muted">Selanjutnya</small>
                            <span class="fw-medium">{{ Str::limit($nextNews->title, 30) }}</span>
                        </div>
                        <i class="fas fa-chevron-right ms-2"></i>
                    </a>
                    @endif
                </div>

                <!-- Related News -->
                @if($relatedNews->count() > 0)
                <section class="related-news mb-5">
                    <h4 class="mb-4 border-bottom pb-2">Berita Terkait</h4>
                    <div class="row g-3">
                        @foreach($relatedNews as $related)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="row g-0 h-100">
                                    <div class="col-4">
                                        @if($related->image)
                                        <img src="{{ asset('storage/'.$related->image) }}" class="img-fluid rounded-start h-100" alt="{{ $related->title }}" style="object-fit: cover;">
                                        @else
                                        <img src="{{ asset('images/default-news.jpg') }}" class="img-fluid rounded-start h-100" alt="Default Image" style="object-fit: cover;">
                                        @endif
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body d-flex flex-column h-100">
                                            <h6 class="card-title">{{ Str::limit($related->title, 50) }}</h6>
                                            <small class="text-muted mt-auto">
                                                {{ \Carbon\Carbon::parse($related->published_at)->format('d M Y') }}
                                            </small>
                                            <a href="{{ route('news.show', $related->id) }}" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Popular News -->
                <div class="sidebar-widget mb-5">
                    <h5 class="mb-3 border-bottom pb-2">Berita Populer</h5>
                    <div class="list-group list-group-flush">
                        @foreach($popularNews as $popular)
                        <a href="{{ route('news.show', $popular->id) }}" class="list-group-item list-group-item-action border-0 px-0 py-3">
                            <div class="d-flex align-items-start">
                                @if($popular->image)
                                <img src="{{ asset('storage/'.$popular->image) }}" alt="{{ $popular->title }}" 
                                    class="flex-shrink-0 me-3 rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                <img src="{{ asset('images/default-news.jpg') }}" alt="Default Image" 
                                    class="flex-shrink-0 me-3 rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($popular->title, 50) }}</h6>
                                    <small class="text-muted">
                                        <i class="far fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($popular->published_at)->format('d M Y') }}
                                    </small>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Categories -->
                <div class="sidebar-widget mb-5">
                    <h5 class="mb-3 border-bottom pb-2">Kategori</h5>
                    <div class="list-group list-group-flush">
                        @foreach($categories as $category)
                        <a href="{{ route('news.category', $category->slug) }}" 
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 px-0">
                            {{ $category->name }}
                            <span class="badge bg-primary rounded-pill">{{ $category->news_count }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Newsletter Subscription -->
                <div class="sidebar-widget mb-5">
                    <div class="bg-primary text-white rounded-3 p-4">
                        <h6 class="mb-2">Berlangganan Newsletter</h6>
                        <p class="small mb-3">Dapatkan update berita terbaru langsung ke email Anda</p>
                        <div class="input-group input-group-sm">
                            <input type="email" class="form-control" placeholder="Alamat email">
                            <button class="btn btn-light text-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent News -->
                <div class="sidebar-widget">
                    <h5 class="mb-3 border-bottom pb-2">Berita Terbaru</h5>
                    <div class="list-group list-group-flush">
                        @foreach($recentNews as $recent)
                        <a href="{{ route('news.show', $recent->id) }}" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ Str::limit($recent->title, 60) }}</h6>
                            </div>
                            <small class="text-muted">
                                <i class="far fa-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($recent->published_at)->format('d M Y') }}
                            </small>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/*  */

.news-header {
    background: linear-gradient(135deg, #3498db, #2c3e50);
    margin-top: 1rem; 
    margin-bottom: 1rem;
    border-radius: 0.5rem; 
    padding: 1rem 0; 
}


/* Article */
.article-content {
    font-size: 1.1rem;
    line-height: 1.8;
}
.article-content img {
    max-width: 100%;
    border-radius: 8px;
    margin: 1rem 0;
}
.article-content h2, h3, h4 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #2c3e50;
}
.article-content blockquote {
    border-left: 4px solid #3498db;
    padding-left: 1rem;
    font-style: italic;
    color: #6c757d;
    margin: 1.5rem 0;
}

/* Sidebar */
.sidebar-widget .list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
    transition: all 0.3s ease;
}

/* Post Navigation */
.post-navigation .btn {
    max-width: 45%;
}
@media (max-width: 768px) {
    .post-navigation .btn { max-width: 100%; margin-bottom: 1rem; }
    .post-navigation { flex-direction: column; }
}

/* Author */
.author-avatar {
    background: linear-gradient(135deg, #3498db, #2c3e50);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyLinkBtn = document.querySelector('.btn-secondary');
    if(copyLinkBtn){
        copyLinkBtn.addEventListener('click', function(e){
            e.preventDefault();
            navigator.clipboard.writeText(window.location.href).then(()=>{
                const original = copyLinkBtn.innerHTML;
                copyLinkBtn.innerHTML = '<i class="fas fa-check me-2"></i> Tersalin!';
                setTimeout(()=>{ copyLinkBtn.innerHTML = original; }, 2000);
            });
        });
    }
});
</script>
@endsection
