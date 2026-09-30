<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Informasi Surat Baru</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;">
  <!-- Preheader (teks pendek yang muncul di preview inbox) -->
  <span style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;overflow:hidden;mso-hide:all;">
    Anda menerima surat baru pada sistem SITEMAN-SURAT.
  </span>

  <!-- Wrapper -->
  <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="background:#f4f6f8;">
    <tr>
      <td align="center" style="padding:24px;">
        <!-- Card -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
          <!-- Header -->
          <tr>
            <td style="background:#0d6efd;padding:24px 28px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td>
                    <div style="font:700 18px/1.2 'Segoe UI',Arial,sans-serif;color:#bcd7ff;letter-spacing:.08em;">SITEMAN-SURAT</div>
                    <div style="font:700 22px/1.3 'Segoe UI',Arial,sans-serif;color:#ffffff;margin-top:4px;">
                      Informasi Surat Baru
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Greeting -->
          @php
            $namaPenerima = $surat->penerima_display
              ?? optional($surat->penerima)->name
              ?? 'Bapak/Ibu';
          @endphp
          <tr>
            <td style="padding:20px 28px 0;">
              <div style="font:400 14px 'Segoe UI',Arial,sans-serif;color:#111827;margin:0 0 4px;">
                Yth. {{ $namaPenerima }},
              </div>
              <div style="font:400 14px 'Segoe UI',Arial,sans-serif;color:#4b5563;margin:0;">
                Anda menerima surat baru pada sistem SITEMAN-SURAT.
              </div>
            </td>
          </tr>

          <!-- Title -->
          <tr>
            <td style="padding:16px 28px 0;">
              <div style="font:700 20px 'Segoe UI',Arial,sans-serif;color:#111827;margin:0 0 6px;">
                {{ $surat->judul_surat ?? 'Surat Baru' }}
              </div>
              <div style="font:400 14px 'Segoe UI',Arial,sans-serif;color:#6b7280;margin:0;">
                Diterima pada
                {{ optional($surat->created_at)->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
              </div>
            </td>
          </tr>

          <!-- Details -->
          <tr>
            <td style="padding:16px 28px 4px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:separate;border-spacing:0 10px;">
                <tr>
                  <td width="180" style="font:600 14px 'Segoe UI',Arial,sans-serif;color:#374151;">Nomor Surat</td>
                  <td style="font:400 14px 'Segoe UI',Arial,sans-serif;color:#111827;">{{ $surat->nomor_surat }}</td>
                </tr>
                <tr>
                  <td width="180" style="font:600 14px 'Segoe UI',Arial,sans-serif;color:#374151;">Jenis Surat</td>
                  <td style="font:400 14px 'Segoe UI',Arial,sans-serif;color:#111827;text-transform:lowercase;">
                    {{ $surat->jenis_surat }}
                  </td>
                </tr>
                <tr>
                  <td width="180" style="font:600 14px 'Segoe UI',Arial,sans-serif;color:#374151;">Kategori</td>
                  <td style="font:400 14px 'Segoe UI',Arial,sans-serif;color:#111827;">
                    {{ $surat->kategori }}
                  </td>
                </tr>
                <tr>
                  <td width="180" style="font:600 14px 'Segoe UI',Arial,sans-serif;color:#374151;">Perihal</td>
                  <td style="font:400 14px 'Segoe UI',Arial,sans-serif;color:#111827;">
                    {{ $surat->perihal }}
                  </td>
                </tr>
                <tr>
                  <td width="180" style="font:600 14px 'Segoe UI',Arial,sans-serif;color:#374151;">Pengirim</td>
                  <td style="font:400 14px 'Segoe UI',Arial,sans-serif;color:#111827;">
                    {{ $surat->pengirim_display }}
                  </td>
                </tr>
                <tr>
                  <td width="180" style="font:600 14px 'Segoe UI',Arial,sans-serif;color:#374151;">Penerima</td>
                  <td style="font:400 14px 'Segoe UI',Arial,sans-serif;color:#111827;">
                    {{ $surat->penerima_display }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- CTA -->
          @php
            // Opsional: kirim $urlLihat dari Mailable untuk tombol di bawah
            $lihatUrl = $urlLihat ?? null;
          @endphp
          @if($lihatUrl)
          <tr>
            <td align="left" style="padding:12px 28px 24px;">
              <a href="{{ $lihatUrl }}" target="_blank"
                 style="display:inline-block;background:#0d6efd;color:#ffffff;text-decoration:none;font:600 14px 'Segoe UI',Arial,sans-serif;padding:12px 18px;border-radius:8px;">
                Lihat Surat
              </a>
            </td>
          </tr>
          @endif
          <!-- Divider -->
          <tr>
            <td style="padding:0 28px;">
              <hr style="border:none;border-top:1px solid #e5e7eb;margin:0;">
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding:16px 28px 24px;">
              <div style="font:400 12px/1.6 'Segoe UI',Arial,sans-serif;color:#6b7280;margin-bottom:10px;">
                Ini adalah pesan otomatis dari sistem SITEMAN-SURAT. Mohon jangan membalas email ini.
              </div>
              <div style="font:400 12px/1.6 'Segoe UI',Arial,sans-serif;color:#6b7280;">
                Butuh bantuan? Hubungi Bagian Persuratan:
                <a href="mailto:persuratan@setneg.go.id" style="color:#0d6efd;text-decoration:none;">persuratan@setneg.go.id</a>
              </div>
            </td>
          </tr>
        </table>
        <!-- /Card -->
      </td>
    </tr>
  </table>
</body>
</html>
