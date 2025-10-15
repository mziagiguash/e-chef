{{-- resources/views/emails/contact-response.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Response to Your Contact Message</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; border-radius: 5px; }
        .response { background: white; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Response to Your Contact Message</h1>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $studentName }}</strong>,</p>

            <p>Thank you for contacting us. Here is our response to your inquiry:</p>

            <div class="response">
                {!! nl2br(e($adminMessage)) !!}
            </div>

            <p>If you have any further questions, please don't hesitate to contact us again.</p>

            <p>Best regards,<br>Support Team</p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
