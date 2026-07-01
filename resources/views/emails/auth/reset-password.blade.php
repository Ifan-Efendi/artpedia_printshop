<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atur Ulang Password Akun Artpedia Printshop</title>
</head>
<body style="margin:0; padding:0; background:#fff5fa; font-family: Inter, Arial, Helvetica, sans-serif; color:#334155;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#fff5fa; margin:0; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px; width:100%;">
                    <tr>
                        <td align="center" style="padding:0 0 18px;">
                            <div style="font-family: Montserrat, Inter, Arial, Helvetica, sans-serif; font-size:22px; font-weight:800; color:#9d005e;">
                                Artpedia Printshop
                            </div>
                            <div style="font-size:13px; color:#64748b; margin-top:4px;">
                                Sistem Pemesanan Percetakan
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; padding:32px; box-shadow:0 4px 12px rgba(15,23,42,0.08);">
                            <h1 style="margin:0 0 12px; font-size:22px; line-height:1.35; color:#1e293b; font-weight:700;">
                                Atur Ulang Password
                            </h1>

                            <p style="margin:0 0 16px; font-size:15px; line-height:1.7; color:#475569;">
                                Halo{{ !empty($notifiable->name) ? ', ' . $notifiable->name : '' }}.
                            </p>

                            <p style="margin:0 0 20px; font-size:15px; line-height:1.7; color:#475569;">
                                Kami menerima permintaan untuk mengatur ulang password akun Artpedia Printshop Anda.
                                Klik tombol di bawah ini untuk membuat password baru.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin:28px auto;">
                                <tr>
                                    <td align="center" bgcolor="#9d005e" style="border-radius:8px;">
                                        <a href="{{ $url }}" style="display:inline-block; padding:13px 24px; font-size:15px; line-height:1; color:#ffffff; text-decoration:none; font-weight:700; background:#9d005e; border-radius:8px;">
                                            Atur Ulang Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 14px; font-size:14px; line-height:1.7; color:#64748b;">
                                Link reset password ini hanya berlaku selama {{ $expire }} menit.
                            </p>

                            <p style="margin:0 0 24px; font-size:14px; line-height:1.7; color:#64748b;">
                                Jika Anda tidak merasa meminta reset password, abaikan email ini. Password akun Anda tidak akan berubah.
                            </p>

                            <p style="margin:0; font-size:14px; line-height:1.7; color:#475569;">
                                Salam,<br>
                                <strong style="color:#1e293b;">Artpedia Printshop</strong>
                            </p>

                            <div style="border-top:1px solid #e2e8f0; margin-top:28px; padding-top:20px;">
                                <p style="margin:0 0 8px; font-size:12px; line-height:1.6; color:#64748b;">
                                    Jika tombol tidak dapat diklik, salin dan buka tautan berikut di browser Anda:
                                </p>
                                <p style="margin:0; font-size:12px; line-height:1.6; word-break:break-all;">
                                    <a href="{{ $url }}" style="color:#9d005e; text-decoration:underline;">{{ $url }}</a>
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:18px 12px 0; font-size:12px; line-height:1.6; color:#94a3b8;">
                            Email ini dikirim otomatis oleh Artpedia Printshop. Mohon tidak membalas email ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
