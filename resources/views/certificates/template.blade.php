<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Completion</title>
    <style>
        /* Абсолютно фиксированные размеры */
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            width: 794px;
            height: 1123px;
            margin: 0;
            padding: 0;
            background-color: #0000f0;
            color: white;
            position: relative;
        }

        /* Основной контент с фиксированными отступами */
        .certificate-content {
            position: absolute;
            top: 80px;
            left: 80px;
            right: 80px;
            bottom: 80px;
            text-align: center;
            display: flex;
            flex-direction: column;
        }

        /* Заголовок */
        .header {
            margin-bottom: 40px;
        }

        .institution-name {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .institution-subtitle {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.4;
            margin-bottom: 30px;
        }

        .certificate-badge {
            display: inline-block;
            background-color: #0000f0;
            color: #c0c0c0;
            padding: 12px 30px;
            border-radius: 40px;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* Основной контент */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 5px 0;
        }

        .confirmation-text {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 40px;
        }

        .student-name {
            font-size: 36px;
            font-weight: bold;
            margin: 30px 0;
            padding: 25px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .program-text {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .course-title {
            font-size: 24px;
            font-weight: bold;
            margin: 25px 0;
            padding: 20px;
            background-color: #0000f0;
            color: #c0c0c0;
            border-radius: 10px;
        }

        .volume-text {
            font-size: 16px;
            opacity: 0.9;
            margin: 25px 0;
        }

        /* Подписи */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.3);
        }

        .signature {
            text-align: center;
            flex: 1;
        }

        .signature-line {
            width: 240px;
            border-top: 1px solid #c0c0c0;
            margin: 0 auto 15px;
        }

        .signature-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-title {
            font-size: 14px;
            opacity: 0.8;
        }

        /* Футер */
        .certificate-footer {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }

        .certificate-id {
            font-size: 12px;
            opacity: 0.6;
        }

        .issue-date {
            font-size: 14px;
            opacity: 0.8;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Основной контент -->
    <div class="certificate-content">
        <!-- Заголовок -->
        <div class="header">
            <div class="institution-name">Your Educational Institution</div>
            <div class="institution-subtitle">
                Частное образовательное учреждение дополнительного профессионального образования<br>
                «Образовательные технологии»
            </div>
            <div class="certificate-badge">Сертификат</div>
        </div>

        <!-- Основной контент -->
        <div class="main-content">
            <div class="confirmation-text">
                Подтверждает, что
            </div>

            <div class="student-name">{{ $student->name }}</div>

            <div class="program-text">
                успешно завершил(а) обучение по дополнительной образовательной программе
            </div>

            <div class="course-title">"{{ $currentTitle }}"</div>

            <div class="volume-text">
                в объеме {{ $course->duration ?? '510' }} академических часов
            </div>
        </div>

        <!-- Подписи -->
        <div class="signatures">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-name">Анастасия Долгих</div>
                <div class="signature-title">Директор по учебной работе</div>
            </div>

            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-name">Дата выдачи</div>
                <div class="signature-title">{{ $completion_date->format('d.m.Y') }}</div>
            </div>
        </div>

        <!-- Футер -->
        <div class="certificate-footer">
            <div class="certificate-id">ID: {{ $certificate_id }}</div>
            <div class="issue-date">Выпущен: {{ $completion_date->format('d.m.Y') }}</div>
        </div>
    </div>
</body>
</html>
