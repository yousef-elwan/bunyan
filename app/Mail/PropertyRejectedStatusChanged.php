<?php

namespace App\Mail;

use App\Models\Property\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class PropertyRejectedStatusChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $property;
    public $oldStatus;
    public $newStatus;

    public function __construct(Property $property, string $oldStatus, string $newStatus)
    {
        $this->property = $property;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function build()
    {
        return $this->subject(__('emails.property_status_changed.subject'))
            ->view('emails.property_status_changed');

        // ->view('emails.property_status_changed');
    }
}
