<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\MachineFault;

class MachineFaultReported extends Notification implements ShouldQueue
{
    use Queueable;

    public $fault;

    public function __construct(MachineFault $fault)
    {
        $this->fault = $fault;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'id' => $this->fault->id,
            'type' => $this->fault->type->name ?? '-',
            'status' => $this->fault->status->name ?? '-',
            'incident_date' => $this->fault->incident_date?->format('Y-m-d'),
            'description' => $this->fault->description,
            'evidence_url' => $this->fault->evidence_path ? asset('storage/'.$this->fault->evidence_path) : null,
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
