<html>
<head>
    <meta charset="UTF-8">
    <title>Tiket #{{ $ticketId }} Selesai</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; margin:0; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#ffffff; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <tr>
            <td style="background:#16a34a; color:#fff; padding:20px; border-top-left-radius:8px; border-top-right-radius:8px; text-align:center;">
                <h2 style="margin:0;">✅ Tiket Diselesaikan</h2>
            </td>
        </tr>
        <tr>
            <td style="padding:20px; color:#333;">
                <p>Halo <strong>{{ $customerName }}</strong>,</p>

                <p>Kami informasikan bahwa tiket <strong>#{{ $ticketId }}</strong> telah berhasil diselesaikan.</p>

                <p><strong>Kategori:</strong> {{ $ticketCategory }}<br>
                   <strong>Deskripsi:</strong> {{ $ticketDescription }}</p>

                <p>Terima kasih telah menggunakan layanan kami. 🙏</p>

                <div style="text-align:center; margin-top:20px;">
                    <a href="{{ config('app.static_url') }}{{ $ticketId }}"
                       style="display:inline-block; background:#16a34a; color:#fff; padding:12px 20px; text-decoration:none; border-radius:5px;">
                       Lihat Detail Tiket
                    </a>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
