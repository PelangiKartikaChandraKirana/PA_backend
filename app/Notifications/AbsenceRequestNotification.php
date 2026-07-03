<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceRequestNotification extends Notification
{
    use Queueable;

    protected $document;
    protected $employee;

    /**
     * Create a new notification instance.
     */
    public function __construct($document, $employee)
    {
        $this->document = $document;
        $this->employee = $employee;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Tentukan URL detail berdasarkan role admin/superadmin
        $url = $notifiable->role === 'superadmin' 
            ? route('superadmin.pegawai.ketidakhadiran')
            : route('admin.verifikasi.izin');

        return [
            'document_id' => $this->document->id,
            'employee_name' => $this->employee->name,
            'type' => $this->document->document_type,
            'title' => $this->document->title,
            'message' => "{$this->employee->name} mengajukan izin: {$this->document->title}",
            'action_url' => $url,
            'icon' => 'fas fa-file-alt',
            'color' => 'primary'
        ];
    }
}
