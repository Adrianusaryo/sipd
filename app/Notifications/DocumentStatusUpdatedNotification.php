<?php

namespace App\Notifications;

use App\Models\ApplicantDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ApplicantDocument $document,
        public string $type // 'submitted' (ke verifikator) atau 'verified' (ke pemohon)
    ) {}

    public function via(object $notifiable): array
    {
        // Menyimpan notifikasi ke Database dan mengirim Mail
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = is_object($this->document->status)
            ? strtoupper($this->document->status->value)
            : strtoupper($this->document->status);

        if ($this->type === 'submitted') {
            return (new MailMessage)
                ->subject("Dokumen Baru Perlu Diverifikasi: {$this->document->title}")
                ->greeting("Halo {$notifiable->name},")
                ->line("Dokumen dengan judul '{$this->document->title}' telah dikirim/direvisi oleh pemohon.")
                ->action('Periksa Dokumen', url("/admin/documents/{$this->document->id}"));
        }

        return (new MailMessage)
            ->subject("Status Dokumen Diperbarui: {$status}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Status pengajuan dokumen '{$this->document->title}' telah diperbarui menjadi: {$status}.")
            ->lineIf(! empty($this->document->verificator_notes), "Catatan: {$this->document->verificator_notes}")
            ->action('Lihat Detail Dokumen', url("/applicant/documents/{$this->document->id}"));
    }

    public function toArray(object $notifiable): array
    {
        $statusValue = is_object($this->document->status)
            ? $this->document->status->value
            : $this->document->status;

        return [
            'document_id' => $this->document->id,
            'title' => $this->document->title,
            'status' => $statusValue,
            'notes' => $this->document->verificator_notes,
            'type' => $this->type,
        ];
    }
}
