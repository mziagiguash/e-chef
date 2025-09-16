@extends('frontend.layouts.app')
@section('title', ($translation->name ?? $instructor->name) . ' - ' . __('Instructor Profile'))

@section('content')
<div class="container py-5">
    <!-- Language Switcher -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-center align-items-center">
                <span class="me-3">{{ __('Choose language:') }}</span>
                <div class="btn-group" role="group">
                   @foreach(['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'] as $code => $name)
    <a href="{{ route('frontend.instructor.show', ['locale' => $code, 'id' => $instructor->id]) }}"
       class="btn btn-outline-primary {{ app()->getLocale() == $code ? 'active' : '' }}">
        {{ $name }}
    </a>
@endforeach
                </div>
            </div>
        </div>
    </div>

    @php
        // ПЕРВОЕ: получаем перевод ДО всего остального
        $locale = app()->getLocale();
        $translation = $instructor->translations->firstWhere('locale', $locale);

        // ВТОРОЕ: находим изображение
        $imagePath = public_path('uploads/users');
        $extensions = ['jpg','jpeg','png','gif','webp'];
        $foundImage = null;

        if ($instructor->image && file_exists($imagePath.'/'.$instructor->image)) {
            $foundImage = asset('uploads/users/'.$instructor->image);
        } else {
            foreach ($extensions as $ext) {
                $file = $imagePath.'/instructor_'.$instructor->id.'.'.$ext;
                if (file_exists($file)) {
                    $foundImage = asset('uploads/users/instructor_'.$instructor->id.'.'.$ext);
                    break;
                }
            }
        }
        if (!$foundImage) $foundImage = asset('uploads/users/default-instructor.jpg');
    @endphp

    <div class="row">
        <div class="col-md-4">
            <div class="text-center">
                <img src="{{ $foundImage }}"
                     alt="{{ $translation->name ?? $instructor->name }}"
                     class="img-fluid rounded-circle mb-3"
                     style="width: 250px; height: 250px; object-fit: cover;">
                <h2>{{ $translation->name ?? $instructor->name }}</h2>
<p class="text-primary h5">
    {{ $translation->designation ?? $instructor->translations->first()->designation ?? '' }}
</p>
<p class="text-muted">
    {{ $translation->title ?? $instructor->translations->first()->title ?? '' }}
</p>
</div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('About Me') }}</h4>
                </div>
                <div class="card-body">
                    <p>{{ $translation->bio ?? $instructor->translations->first()->bio ?? __('No biography available.') }}</p>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>{{ __('Contact Information') }}</h5>
                            <p><strong>{{ __('Email') }}:</strong> {{ $instructor->email }}</p>
                            @if($instructor->contact)
                                <p><strong>{{ __('Phone') }}:</strong> {{ $instructor->contact }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5>{{ __('Details') }}</h5>
                            <p><strong>{{ __('Status') }}:</strong>
                                <span class="badge badge-success">
                                    {{ $instructor->status == 1 ? __('Active') : __('Inactive') }}
                                </span>
                            </p>
                            <p><strong>{{ __('Language') }}:</strong>
                                @switch($instructor->language)
                                    @case('en') {{ __('English') }} @break
                                    @case('ru') {{ __('Russian') }} @break
                                    @case('ka') {{ __('Georgian') }} @break
                                    @default {{ __('Unknown') }}
                                @endswitch
                            </p>
                            <p><strong>{{ __('Member Since') }}:</strong>
                                {{ $instructor->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>{{ __('Social Media') }}</h5>
                            <div class="social-links">
                                <a href="#" class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="btn btn-outline-info btn-sm me-2">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="btn btn-outline-danger btn-sm">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses Section -->
            @if($instructor->courses && $instructor->courses->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h4>{{ __('Courses by this Instructor') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($instructor->courses->take(3) as $course)
                        <div class="col-md-4 mb-3">
                            <div class="card course-card">
                                <img src="{{ asset('uploads/courses/' . $course->image) }}"
                                     class="card-img-top"
                                     alt="{{ $course->title }}"
                                     style="height: 150px; object-fit: cover;">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $course->title }}</h6>
                                    <p class="card-text text-muted small">
                                        {{ Str::limit($course->description, 60) }}
                                    </p>
                                    <<a href="{{ route('course.show', ['locale' => app()->getLocale(), 'id' => $course->id]) }}"
   class="btn btn-primary btn-sm">
    {{ __('View Course') }}
</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($instructor->courses->count() > 3)
                    <div class="text-center mt-3">
                        <a href="{{ route('instructor.courses', ['locale' => app()->getLocale(), 'id' => $instructor->id]) }}"
   class="btn btn-outline-primary">
    {{ __('View All Courses') }} ({{ $instructor->courses->count() }})
</a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('frontend.instructors', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left me-2"></i>
    {{ __('Back to Instructors') }}
</a>

                @if($instructor->contact)
                <a href="mailto:{{ $instructor->email }}" class="btn btn-primary ms-2">
                    <i class="fas fa-envelope me-2"></i>
                    {{ __('Contact Instructor') }}
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.course-card {
    transition: transform 0.3s ease;
}
.course-card:hover {
    transform: translateY(-5px);
}
.social-links .btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}
</style>
@endsection
