@extends('layouts.admin')

@section('title', 'Queue Display Media - NU Lipa Admin')

@section('content')
<style>
    .media-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .media-card .card-header {
        padding: 1.25rem 1.5rem;
        font-size: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .slide-thumb {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        background: #f3f4f6;
    }

    .slide-item {
        position: relative;
    }

    .slide-item .delete-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(220,38,38,0.9);
        border: none;
        color: #fff;
        font-size: 1rem;
        line-height: 1;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .slide-item .delete-btn:hover {
        background: #dc2626;
    }

    .default-badge {
        position: absolute;
        bottom: 8px;
        left: 8px;
        background: rgba(0,51,153,0.85);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .upload-zone {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        background: #f9fafb;
        transition: border-color 0.2s, background 0.2s;
        cursor: pointer;
    }

    .upload-zone:hover, .upload-zone.dragover {
        border-color: #003399;
        background: #eff3ff;
    }

    .upload-zone input[type=file] {
        display: none;
    }

    .upload-zone i {
        font-size: 2.5rem;
        color: #9ca3af;
        display: block;
        margin-bottom: 0.5rem;
    }

    .upload-zone:hover i {
        color: #003399;
    }

    .video-preview {
        width: 100%;
        max-height: 280px;
        border-radius: 10px;
        background: #000;
    }

    .section-divider {
        border: none;
        border-top: 2px solid #e5e7eb;
        margin: 1.5rem 0;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0" style="color:#003399; font-weight:700;">
                <i class="bi bi-display me-2"></i>Queue Display Media
            </h1>
            <p class="text-muted mt-1">Manage the slideshow images and advertisement video shown on the queue display screen.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- ── SLIDESHOW IMAGES ──────────────────────────────────────── --}}
        <div class="col-12">
            <div class="card media-card">
                <div class="card-header" style="background:linear-gradient(135deg,#003399,#001f5f); color:#fff;">
                    <i class="bi bi-images"></i>
                    Slideshow Images
                    <span class="badge ms-auto" style="background:rgba(255,255,255,0.2); font-weight:600;">
                        {{ count($slides) }} slide{{ count($slides) !== 1 ? 's' : '' }}
                    </span>
                </div>
                <div class="card-body">

                    {{-- Current slides --}}
                    @if(count($slides) > 0)
                        <h6 class="fw-semibold mb-3 text-muted text-uppercase" style="font-size:.78rem; letter-spacing:.8px;">Current Slides</h6>
                        <div class="row g-3 mb-4">
                            @foreach($slides as $slide)
                                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                                    <div class="slide-item">
                                        <img src="{{ $slide['url'] }}" alt="{{ $slide['filename'] }}" class="slide-thumb">

                                        @if($slide['custom'])
                                            {{-- Delete button only for uploaded slides --}}
                                            <form method="POST"
                                                  action="{{ route('admin.display-media.slide.delete', $slide['id']) }}"
                                                  onsubmit="return confirm('Delete this slide?')"
                                                  style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-btn" title="Delete slide">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="default-badge">Default</span>
                                        @endif
                                    </div>
                                    <p class="text-muted mt-1 text-truncate" style="font-size:.75rem;">{{ $slide['filename'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <hr class="section-divider">
                    @endif

                    {{-- Upload new slides --}}
                    <h6 class="fw-semibold mb-3 text-muted text-uppercase" style="font-size:.78rem; letter-spacing:.8px;">
                        <i class="bi bi-upload me-1"></i>Upload New Slide
                    </h6>
                    <form method="POST" action="{{ route('admin.display-media.slide.upload') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row align-items-end g-3">
                            <div class="col-md-8">
                                <label class="upload-zone w-100" for="slideInput" id="slideDropZone">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <span id="slideLabel" class="fw-semibold" style="color:#374151;">Click or drag & drop an image here</span>
                                    <small class="d-block text-muted mt-1">JPEG, PNG, GIF, WEBP — max 5 MB</small>
                                    <input type="file" id="slideInput" name="slide" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                                </label>
                                @error('slide') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100" style="background:#003399; border:#003399;">
                                    <i class="bi bi-upload me-1"></i>Upload Slide
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(collect($slides)->where('custom', true)->isEmpty() && count($slides) > 0)
                        <div class="alert alert-info mt-3 mb-0 py-2" style="font-size:.85rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Currently showing <strong>default slides</strong>. Upload your own images to replace them. Once at least one custom slide is uploaded, the defaults will no longer be shown.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── VIDEO ADVERTISEMENT ──────────────────────────────────── --}}
        <div class="col-12">
            <div class="card media-card">
                <div class="card-header" style="background:linear-gradient(135deg,#1d4ed8,#1e3a8a); color:#fff;">
                    <i class="bi bi-play-circle"></i>
                    Advertisement Video
                </div>
                <div class="card-body">
                    <div class="row g-4 align-items-start">
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-3 text-muted text-uppercase" style="font-size:.78rem; letter-spacing:.8px;">Current Video</h6>
                            <video class="video-preview" controls muted>
                                <source src="{{ $videoUrl }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            @if($videoRecord)
                                <p class="mt-2 mb-0" style="font-size:.8rem;">
                                    <i class="bi bi-file-earmark-play me-1 text-primary"></i>
                                    <strong>{{ $videoRecord->original_name }}</strong>
                                    <span class="text-muted ms-2">— uploaded {{ $videoRecord->created_at->diffForHumans() }}</span>
                                </p>
                            @else
                                <p class="text-muted mt-2" style="font-size:.8rem;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Using default advertisement video.
                                </p>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-3 text-muted text-uppercase" style="font-size:.78rem; letter-spacing:.8px;">
                                <i class="bi bi-upload me-1"></i>Replace Video
                            </h6>
                            <form method="POST" action="{{ route('admin.display-media.video.upload') }}" enctype="multipart/form-data">
                                @csrf
                                <label class="upload-zone w-100" for="videoInput" id="videoDropZone">
                                    <i class="bi bi-film"></i>
                                    <span id="videoLabel" class="fw-semibold" style="color:#374151;">Click or drag & drop a video here</span>
                                    <small class="d-block text-muted mt-1">MP4, WebM, OGG — max 200 MB</small>
                                    <input type="file" id="videoInput" name="video" accept="video/mp4,video/webm,video/ogg">
                                </label>
                                @error('video') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                                <button type="submit" class="btn btn-primary w-100 mt-3" style="background:#1d4ed8; border:#1d4ed8;">
                                    <i class="bi bi-upload me-1"></i>Upload & Replace Video
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Show selected filename in upload zones
    document.getElementById('slideInput').addEventListener('change', function () {
        const label = document.getElementById('slideLabel');
        label.textContent = this.files.length ? this.files[0].name : 'Click or drag & drop an image here';
    });

    document.getElementById('videoInput').addEventListener('change', function () {
        const label = document.getElementById('videoLabel');
        label.textContent = this.files.length ? this.files[0].name : 'Click or drag & drop a video here';
    });

    // Drag & drop highlight
    ['slideDropZone', 'videoDropZone'].forEach(function(id) {
        const zone = document.getElementById(id);
        zone.addEventListener('dragover', function(e) { e.preventDefault(); zone.classList.add('dragover'); });
        zone.addEventListener('dragleave', function()  { zone.classList.remove('dragover'); });
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            zone.classList.remove('dragover');
            const input = zone.querySelector('input[type=file]');
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    });
</script>
@endsection
