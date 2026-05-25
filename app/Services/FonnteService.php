<?php

namespace App\Services;

use App\Models\Penghuni;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $apiUrl = 'https://api.fonnte.com/send';

    /**
     * Sanitize phone number for Fonnte API (62 format, no spaces/dashes)
     */
    protected function sanitizePhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert 08 to 628
        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        }
        
        return $phone;
    }

    /**
     * Send a WhatsApp message via Fonnte API.
     */
    public function sendMessage(string $target, string $message, ?int $id_penghuni = null): array
    {
        $token = Setting::get('fonnte_token');
        $cleanTarget = $this->sanitizePhone($target);

        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Token Fonnte belum dikonfigurasi. Silakan atur di halaman Settings.',
            ];
        }

        \App\Jobs\SendWhatsAppJob::dispatchSync($cleanTarget, $message, $id_penghuni);

        return [
            'success' => true,
            'message' => 'Pesan WhatsApp telah dikirim.',
            'data' => null,
        ];
    }

    /**
     * Send a reminder to a specific penghuni.
     */
    public function sendReminder(Penghuni $penghuni): array
    {
        $penghuni->load('kamar');

        $today = Carbon::today();
        $tempo = Carbon::parse($penghuni->tgl_jatuh_tempo)->startOfDay();
        $daysLeft = (int) $today->diffInDays($tempo, false);

        // Choose template based on overdue or upcoming
        if ($daysLeft < 0) {
            $template = Setting::get('wa_overdue_template',
                'Halo {nama}, pembayaran sewa kamar {kamar} Anda sebesar Rp {tagihan} telah melewati jatuh tempo pada {tanggal}. Mohon segera lakukan pembayaran. Terima kasih 🙏'
            );
        } else {
            $template = Setting::get('wa_reminder_template',
                'Halo {nama}, kami dari pengelola KOSQU mengingatkan bahwa pembayaran sewa kamar {kamar} Anda sebesar Rp {tagihan} akan jatuh tempo pada {tanggal} ({sisa_hari} hari lagi). Mohon segera lakukan pembayaran. Terima kasih 🙏'
            );
        }

        $tagihan = $penghuni->sisaTagihan();

        $message = $this->parseTemplate($template, [
            'nama' => $penghuni->nama,
            'kamar' => $penghuni->kamar->nomor_kamar ?? '?',
            'tanggal' => $tempo->translatedFormat('d F Y'),
            'tagihan' => number_format($tagihan, 0, ',', '.'),
            'sisa_hari' => abs($daysLeft),
        ]);

        return $this->sendMessage($penghuni->no_hp, $message, $penghuni->id);
    }

    /**
     * Send welcome message to new tenant with login credentials.
     */
    public function sendWelcomeMessage(Penghuni $penghuni, string $password): array
    {
        $penghuni->load('kamar');
        $nomorKamar = $penghuni->kamar->nomor_kamar ?? '-';
        $url = url('/login');

        $message = "Halo *{$penghuni->nama}* 👋\n\n"
                 . "Selamat bergabung di *KOSQU*! Anda telah terdaftar sebagai penghuni Kamar *{$nomorKamar}*.\n\n"
                 . "Berikut adalah detail akses untuk masuk ke Portal Penghuni Anda:\n"
                 . "🔗 *URL Login*: {$url}\n"
                 . "👤 *Username (NIK)*: {$penghuni->nik}\n"
                 . "🔑 *Password*: {$password}\n\n"
                 . "Silakan login untuk melihat tagihan dan melaporkan pembayaran Anda. Jika ada pertanyaan, jangan ragu untuk menghubungi admin.\n\n"
                 . "Terima kasih!";

        return $this->sendMessage($penghuni->no_hp, $message, $penghuni->id);
    }

    /**
     * Send booking confirmation with login credentials and DP details.
     */
    public function sendBookingConfirmation(Penghuni $penghuni, string $password, int $dpAmount, int $totalTagihan, string $tglRencanaMasuk): array
    {
        $penghuni->load('kamar');
        $nomorKamar = $penghuni->kamar->nomor_kamar ?? '-';
        $url = url('/login');
        $sisaPelunasan = $totalTagihan - $dpAmount;
        $deadline = Carbon::parse($tglRencanaMasuk)->translatedFormat('d F Y');

        $message = "📋 *BOOKING DIKONFIRMASI*\n\n"
                 . "Halo *{$penghuni->nama}* 👋\n"
                 . "Booking kamar *{$nomorKamar}* telah dikonfirmasi!\n\n"
                 . "💰 *Total Sewa*: Rp " . number_format($totalTagihan, 0, ',', '.') . "\n"
                 . "✅ *DP Dibayar*: Rp " . number_format($dpAmount, 0, ',', '.') . "\n";

        if ($sisaPelunasan > 0) {
            $message .= "📊 *Sisa Pelunasan*: Rp " . number_format($sisaPelunasan, 0, ',', '.') . "\n"
                      . "📅 *Deadline*: {$deadline}\n\n";
        } else {
            $message .= "🎉 *Status*: LUNAS\n\n";
        }

        $message .= "Berikut akses login Portal Penghuni:\n"
                  . "🔗 *URL Login*: {$url}\n"
                  . "👤 *NIK*: {$penghuni->nik}\n"
                  . "🔑 *Password*: {$password}\n\n"
                  . "Silakan login untuk memantau tagihan dan melaporkan pembayaran. Terima kasih!";

        return $this->sendMessage($penghuni->no_hp, $message, $penghuni->id);
    }

    /**
     * Send E-Receipt when a transaction is validated or rejected.
     */
    public function sendTransactionReceipt(\App\Models\Transaksi $transaksi): array
    {
        $transaksi->load(['penghuni.kamar']);
        $penghuni = $transaksi->penghuni;
        
        $nominal = number_format($transaksi->jumlah_bayar, 0, ',', '.');
        $bulan = $transaksi->bulan_tagihan;

        if ($transaksi->status_validasi === 'Valid') {
            $jatuhTempoBaru = \Carbon\Carbon::parse($penghuni->tgl_jatuh_tempo)->translatedFormat('d F Y');
            $message = "✅ *PEMBAYARAN DITERIMA*\n\n"
                     . "Halo *{$penghuni->nama}*,\n"
                     . "Pembayaran sewa kamar untuk bulan *{$bulan}* sebesar *Rp {$nominal}* telah berhasil divalidasi oleh admin.\n\n"
                     . "Batas jatuh tempo Anda berikutnya adalah *{$jatuhTempoBaru}*.\n\n"
                     . "Terima kasih!";
        } else {
            // Ditolak
            $message = "❌ *PEMBAYARAN DITOLAK*\n\n"
                     . "Halo *{$penghuni->nama}*,\n"
                     . "Mohon maaf, bukti pembayaran Anda untuk bulan *{$bulan}* sebesar *Rp {$nominal}* telah *DITOLAK* oleh admin.\n\n"
                     . "Silakan periksa kembali bukti transfer Anda atau hubungi admin untuk informasi lebih lanjut.";
        }

        return $this->sendMessage($penghuni->no_hp, $message, $penghuni->id);
    }

    /**
     * Send password reset to tenant.
     */
    public function sendResetPassword(\App\Models\Penghuni $penghuni, string $newPassword): array
    {
        $url = url('/login');

        $message = "Halo *{$penghuni->nama}* 👋\n\n"
                 . "Kami menerima permintaan reset password untuk akun KOSQU Anda.\n\n"
                 . "Berikut adalah detail akses baru Anda:\n"
                 . "🔗 *URL Login*: {$url}\n"
                 . "👤 *Username (NIK)*: {$penghuni->nik}\n"
                 . "🔑 *Password Baru*: {$newPassword}\n\n"
                 . "Silakan login menggunakan password baru ini. Anda dapat mengubahnya nanti di menu Profil.\n\n"
                 . "Abaikan pesan ini jika Anda tidak merasa meminta reset password.";

        return $this->sendMessage($penghuni->no_hp, $message, $penghuni->id);
    }

    /**
     * Parse a template string replacing {placeholders} with values.
     */
    protected function parseTemplate(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }
}
