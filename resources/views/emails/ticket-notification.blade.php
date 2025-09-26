<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subjectText }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; margin:0; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#ffffff; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <tr>
            <td style="background:#2563eb; color:#fff; padding:20px; border-top-left-radius:8px; border-top-right-radius:8px; text-align:center;">
                <h2 style="margin:0;">📩 Tiket Baru</h2>
            </td>
        </tr>
        <tr>
            <td style="padding:20px; color:#333;">
                <p>Halo <strong>{{ $teknisiName ?? 'Teknisi' }}</strong>,</p>
                <p>Anda telah ditugaskan untuk menangani tiket <strong>#{{ $ticketId }}</strong>.</p>
                <p><strong>Kategori:</strong> {{ $ticketCategory }}<br>
                   <strong>Deskripsi:</strong> {{ $ticketDescription }}</p>
                <p>Harap segera ditindaklanjuti. 👍</p>
                <div style="text-align:center; margin-top:20px;">
                    <a href="{{ config('app.static_url') }}tabel-keluhan/lihat/{{ $ticketId }}" style="display:inline-block; background:#2563eb; color:#fff; padding:12px 20px; text-decoration:none; border-radius:5px;">Lihat Tiket</a>
                </div>
            </td>
        </tr>
        <tr>
            <td style="background:#f3f4f6; padding:15px; text-align:center; font-size:12px; color:#666; border-bottom-left-radius:8px; border-bottom-right-radius:8px;">
                © {{ date('Y') }} Provider Internet. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>
