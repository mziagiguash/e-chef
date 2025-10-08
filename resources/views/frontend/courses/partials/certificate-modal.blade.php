{{-- Модальное окно сертификата --}}
<div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 overflow-hidden">
            {{-- Градиентный хедер --}}
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4681f4 0%, #5d3be3 100%);">
                <h5 class="modal-title" id="certificateModalLabel">
                    <i class="fas fa-award me-2"></i>
                    {{ __('Certificate of Completion') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Основной контент с градиентным фоном --}}
            <div class="modal-body text-center p-0">
                <div class="certificate-preview p-4" style="background: linear-gradient(135deg, #4681f4 0%, #5d3be3 100%);">
                    {{-- Предпросмотр сертификата --}}
                    <div class="certificate-content position-relative mx-auto text-white" style="max-width: 500px;">
                        {{-- Заголовок --}}
                        <div class="text-center mb-4">
                            <h6 class="fw-bold mb-2">Your Educational Institution</h6>
                            <small class="opacity-80">Образовательные технологии</small>
                        </div>

                        {{-- Бейдж сертификата --}}
                        <div class="badge bg-white text-primary mb-4" style="font-size: 0.9rem; padding: 8px 20px;">
                            Сертификат
                        </div>

                        {{-- Основной текст --}}
                        <p class="opacity-80 mb-3">Подтверждает, что</p>
                        <h4 class="fw-bold mb-4">{{ $student->name ?? __('Student Name') }}</h4>
                        <p class="opacity-80 mb-3">успешно завершил(а) обучение по программе</p>
                        <h5 class="fw-bold bg-white text-primary p-3 rounded mb-4">
                            {{ $currentTitle }}
                        </h5>
                        <p class="opacity-80 mb-4">в объеме {{ $course->duration ?? '510' }} академических часов</p>

                        {{-- Подписи --}}
                        <div class="row small opacity-80 mt-5">
                            <div class="col-6 text-start">
                                <div class="border-top border-white pt-2">
                                    <small class="d-block">Директор по учебной работе</small>
                                    <small class="fw-bold">Анастасия Долгих</small>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="border-top border-white pt-2">
                                    <small class="d-block">Дата выдачи</small>
                                    <small class="fw-bold">{{ now()->format('d.m.Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Опции загрузки --}}
                <div class="download-options p-4 bg-light">
                    <p class="text-muted mb-3">{{ __('Download your certificate:') }}</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <button class="btn btn-primary btn-lg download-certificate" data-format="pdf">
                            <i class="fas fa-file-pdf me-2"></i> PDF Format
                        </button>
                        <button class="btn btn-outline-primary btn-lg download-certificate" data-format="png" disabled title="{{ __('Available soon') }}">
                            <i class="fas fa-image me-2"></i> PNG (Soon)
                        </button>
                    </div>
                    <p class="small text-muted mt-3">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ __('Professional certificate with gradient design') }}
                    </p>
                </div>

                {{-- Дебаг информация --}}
                <div class="debug-info mt-3 p-3 bg-white border-top small">
                    <strong>Debug Info:</strong><br>
                    Course ID: {{ $course->id }}<br>
                    Student ID: {{ $studentId ?? 'Not set' }}<br>
                    Locale: {{ $locale }}<br>
                    CSRF Token: {{ csrf_token() ? 'Set' : 'Not set' }}
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
