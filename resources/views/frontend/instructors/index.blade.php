@extends('frontend.layouts.app')
@section('title', __('Our Instructors'))

@push('styles')
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')

<div class="instructors-page">
    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-4 fw-bold mb-3">{{ __('Our Expert Instructors') }}</h1>
                    <p class="lead mb-4">{{ __('Learn from the best professionals in the industry') }}</p>
                    <div class="hero-stats">
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <div class="stat-item">
                                    <h3 class="mb-0">{{ $instructors->count() }}+</h3>
                                    <small>{{ __('Qualified Instructors') }}</small>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="stat-item">
                                    <h3 class="mb-0">100+</h3>
                                    <small>{{ __('Years of Experience') }}</small>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="stat-item">
                                    <h3 class="mb-0">500+</h3>
                                    <small>{{ __('Happy Students') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Language Switcher -->
    <section class="language-section py-3 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-center align-items-center">
                        <span class="me-3">{{ __('Choose language:') }}</span>
                        <div class="btn-group" role="group">
                            @foreach(['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'] as $code => $name)
                                @if(Route::currentRouteName() == 'frontend.instructors')
                                    <a href="{{ route('frontend.instructors', ['locale' => $code]) }}"
                                       class="btn btn-outline-primary {{ app()->getLocale() == $code ? 'active' : '' }}">
                                        {{ $name }}
                                    </a>
                                @elseif(Route::currentRouteName() == 'frontend.instructor.show')
                                    <a href="{{ route('frontend.instructor.show', ['locale' => $code, 'id' => $instructor->id]) }}"
                                       class="btn btn-outline-primary {{ app()->getLocale() == $code ? 'active' : '' }}">
                                        {{ $name }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section py-4 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="search-box">
                        <form id="search-form">
                            <div class="input-group">
                                <input type="text" id="instructor-search" class="form-control form-control-lg"
                                       placeholder="{{ __('Search instructors by name, specialty or bio...') }}"
                                       aria-label="{{ __('Search instructors') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-outline-secondary" type="button" id="clear-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Instructors Grid -->
    <section class="instructors-grid-section py-5">
        <div class="container">
            @if($instructors->count() > 0)
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <h2 class="section-title">{{ __('Meet Our Team') }}</h2>
                        <p class="section-subtitle">{{ __('Professional instructors ready to guide you') }}</p>
                    </div>
                </div>

                <div id="search-results-info" class="alert alert-info d-none mb-4">
                    <span id="results-count">0</span> {{ __('instructors found') }}
                </div>

                <div class="row" id="instructors-container">
                    @foreach($instructors as $instructor)
@php
    $locale = app()->getLocale();
    $translation = $instructor->translations->firstWhere('locale', $locale);

    // Упрощенный и надежный поиск изображения
    $foundImage = null;

    // Сначала проверяем основное изображение из базы данных
    if ($instructor->image) {
        $imagePath = public_path('uploads/users/' . $instructor->image);
        if (file_exists($imagePath)) {
            $foundImage = asset('uploads/users/' . $instructor->image);
        }
    }

    // Если не найдено, пробуем найти по ID
    if (!$foundImage) {
        $possibleFilenames = [
            'instructor_' . $instructor->id . '.jpg',
            'instructor_' . $instructor->id . '.jpeg',
            'instructor_' . $instructor->id . '.png',
            'instructor_' . $instructor->id . '.webp',
            'instructor_' . $instructor->id . '.gif',
        ];

        foreach ($possibleFilenames as $filename) {
            $imagePath = public_path('uploads/users/' . $filename);
            if (file_exists($imagePath)) {
                $foundImage = asset('uploads/users/' . $filename);
                break;
            }
        }
    }

    // Если всё еще не найдено, используем дефолтное изображение
    if (!$foundImage) {
        $foundImage = asset('uploads/users/default-instructor.jpg');
    }
@endphp
<div class="col-lg-4 col-md-6 mb-4 instructor-item"
     data-name="{{ strtolower($translation->name ?? $instructor->name) }}"
     data-designation="{{ strtolower($translation->designation ?? $instructor->translations->first()->designation ?? '') }}"
     data-bio="{{ strtolower($translation->bio ?? $instructor->translations->first()->bio ?? '') }}">

    {{-- Временная отладочная информация --}}
    <div style="display:none;">
        @php
            $debugPath = public_path('uploads/instructors/instructor_' . $instructor->id . '.jpg');
            $debugExists = file_exists($debugPath) ? 'Файл найден: instructor_' . $instructor->id . '.jpg' : 'Файл не найден: instructor_' . $instructor->id . '.jpg';
        @endphp
        {{ $debugExists }}<br>
        Image: {{ $foundImage }}
    </div>

    <div class="card instructor-card h-100">
        <div class="instructor-image-container">
            <img src="{{ $foundImage }}"
                 class="instructor-image"
                 alt="{{ $translation->name ?? $instructor->name }}"
                 onerror="this.src='https://via.placeholder.com/300x300/667eea/ffffff?text=Image+Not+Found'">
            <div class="instructor-overlay">
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

                                <div class="card-body text-center">
                                    <h5 class="instructor-name">{{ $translation->name ?? $instructor->name }}</h5>
                                    <p class="instructor-designation text-primary">
                                        {{ $translation->designation ?? $instructor->translations->first()->designation ?? '' }}
                                    </p>
                                    <p class="instructor-bio">
                                        {{ Str::limit($translation->bio ?? $instructor->translations->first()->bio ?? '', 120) }}
                                    </p>

                                    <div class="instructor-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-graduation-cap"></i>
                                            <span>10+ {{ __('Years') }}</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-users"></i>
                                            <span>200+ {{ __('Students') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-transparent text-center">
                                    <a href="{{ route('frontend.instructor.show', ['locale' => app()->getLocale(), 'id' => $instructor->id]) }}"
                                       class="btn btn-primary btn-view-profile">
                                        <i class="fas fa-user me-2"></i>
                                        {{ __('View Profile') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- No Results Message -->
                <div id="no-results-message" class="row d-none">
                    <div class="col-12 text-center">
                        <div class="empty-state">
                            <i class="fas fa-search fa-4x text-muted mb-4"></i>
                            <h3>{{ __('No instructors found') }}</h3>
                            <p class="text-muted">{{ __('Try different search terms or browse all instructors') }}</p>
                            <button id="reset-search" class="btn btn-primary mt-3">
                                <i class="fas fa-redo me-2"></i>
                                {{ __('Show All Instructors') }}
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="empty-state">
                            <i class="fas fa-users fa-4x text-muted mb-4"></i>
                            <h3>{{ __('No Instructors Available') }}</h3>
                            <p class="text-muted">{{ __('We are currently updating our instructor team. Please check back later.') }}</p>
                            <a href="{{ url('/') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-home me-2"></i>
                                {{ __('Return Home') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
                        </div>

                <!-- Пагинация -->
                @if(method_exists($instructors, 'hasPages') && $instructors->hasPages())

                <div class="row mt-5">
                    <div class="col-12">
                        <nav aria-label="Instructors pagination">
                            <ul class="pagination justify-content-center">
                                {{-- Previous Page Link --}}
                                @if($instructors->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">{{ __('Previous') }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $instructors->previousPageUrl() }}" rel="prev">{{ __('Previous') }}</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach($instructors->getUrlRange(1, $instructors->lastPage()) as $page => $url)
                                    @if($page == $instructors->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if($instructors->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $instructors->nextPageUrl() }}" rel="next">{{ __('Next') }}</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">{{ __('Next') }}</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
                @endif
    </section>
</div>
@endsection

@push('styles')
<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hero-stats .stat-item {
    padding: 0 2rem;
    border-right: 1px solid rgba(255,255,255,0.3);
}

.hero-stats .stat-item:last-child {
    border-right: none;
}
.language-section .btn{
    background-color: #f4f4f7 !important;
    border-color: #667eea !important;
}
.search-box {
    position: relative;
}

.search-box .input-group {
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    border-radius: 50px;
    overflow: hidden;
}

.search-box .form-control {
    border: none;
    padding-left: 1.5rem;
}

.search-box .btn {
    border-radius: 0;
    border: none;

}

.search-box .btn:first-of-type {
    border-top-right-radius: 50px;
    border-bottom-right-radius: 50px;
}

.search-box .btn:last-of-type {
    border-right: 1px solid #dee2e6;
}

.instructor-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border-radius: 15px;
    overflow: hidden;
}

.instructor-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.instructor-image-container {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.instructor-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.instructor-card:hover .instructor-image {
    transform: scale(1.1);
}

.instructor-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.instructor-card:hover .instructor-overlay {
    opacity: 1;
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    width: 40px;
    height: 40px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: #667eea;
    color: #fff;
    transform: translateY(-3px);
}

.instructor-name {
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.instructor-designation {
    font-weight: 600;
    margin-bottom: 1rem;
}

.instructor-bio {
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.instructor-meta {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6c757d;
}
/* Исправляем цвет всех кнопок */
.btn-primary {
    background-color: #667eea !important;
    border-color: #667eea !important;
}

.btn-primary:hover, .btn-primary:focus {
    background-color: #5a6fd8 !important;
    border-color: #5a6fd8 !important;
}

.btn-outline-primary {
    color: #667eea !important;
    border-color: #667eea !important;
}

.btn-outline-primary:hover, .btn-outline-primary:focus {
    background-color: #667eea !important;
    border-color: #667eea !important;
    color: white !important;
}

/* Для языковых кнопок */
.language-btn.active {
    background-color: #667eea !important;
    border-color: #667eea !important;
    color: white !important;
}

.btn-view-profile {
    border-radius: 25px;
    padding: 0.5rem 2rem;
    font-weight: 600;
}

.empty-state {
    padding: 4rem 2rem;
}

.section-title {
    font-weight: 700;
    color: #2c3e50;
}

.section-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
}

.language-btn.active {
    background-color: #667eea;
    border-color: #667eea;
    color: white;
}

.cta-section {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
}

@media (max-width: 768px) {
    .hero-stats .stat-item {
        padding: 0 1rem;
        margin-bottom: 1rem;
        border-right: none;
        border-bottom: 1px solid rgba(255,255,255,0.3);
    }

    .hero-stats .stat-item:last-child {
        border-bottom: none;
    }

    .instructor-meta {
        flex-direction: column;
        gap: 1rem;
    }

    .search-box .input-group {
        flex-direction: column;
        border-radius: 12px;
    }

    .search-box .form-control {
        border-radius: 12px 12px 0 0;
        margin-bottom: 1px;
    }

    .search-box .btn {
        border-radius: 0;
        flex: 1;
    }

    .search-box .btn:first-of-type {
        border-radius: 0 0 0 12px;
    }

    .search-box .btn:last-of-type {
        border-radius: 0 0 12px 0;
        border-right: none;
        border-top: 1px solid #dee2e6;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('instructor-search');
    const clearSearchBtn = document.getElementById('clear-search');
    const resetSearchBtn = document.getElementById('reset-search');
    const instructorsContainer = document.getElementById('instructors-container');
    const noResultsMessage = document.getElementById('no-results-message');
    const searchResultsInfo = document.getElementById('search-results-info');
    const resultsCount = document.getElementById('results-count');
    const instructorItems = document.querySelectorAll('.instructor-item');

    // Функция для поиска инструкторов
    function searchInstructors(query) {
        const searchTerm = query.toLowerCase().trim();
        let visibleCount = 0;

        instructorItems.forEach(item => {
            const name = item.dataset.name;
            const designation = item.dataset.designation;
            const bio = item.dataset.bio;

            const matches = searchTerm === '' ||
                           name.includes(searchTerm) ||
                           designation.includes(searchTerm) ||
                           bio.includes(searchTerm);

            if (matches) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Показываем/скрываем сообщения
        if (searchTerm !== '') {
            resultsCount.textContent = visibleCount;

            if (visibleCount === 0) {
                noResultsMessage.classList.remove('d-none');
                searchResultsInfo.classList.add('d-none');
                instructorsContainer.classList.add('d-none');
            } else {
                noResultsMessage.classList.add('d-none');
                searchResultsInfo.classList.remove('d-none');
                instructorsContainer.classList.remove('d-none');
            }
        } else {
            noResultsMessage.classList.add('d-none');
            searchResultsInfo.classList.add('d-none');
            instructorsContainer.classList.remove('d-none');
        }
    }

    // Обработчик отправки формы поиска
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        searchInstructors(searchInput.value);
    });

    // Обработчик очистки поиска
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        searchInstructors('');
        searchInput.focus();
    });

    // Обработчик сброса поиска
    if (resetSearchBtn) {
        resetSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInstructors('');
        });
    }

    // Поиск при вводе (можно добавить debounce для оптимизации)
    searchInput.addEventListener('input', function() {
        searchInstructors(this.value);
    });

    // Language switching functionality
    const languageButtons = document.querySelectorAll('.language-btn');

    languageButtons.forEach(button => {
        button.addEventListener('click', function() {
            const locale = this.dataset.locale;

            // Update active state
            languageButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Here you would typically reload the page with the selected language
            // or make an AJAX request to change the language
            window.location.href = `{{ url()->current() }}?locale=${locale}`;
        });
    });
});
</script>
@endpush
