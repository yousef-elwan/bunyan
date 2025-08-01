<?php

namespace App\Mail;

use App\Models\Property\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class NewPropertyNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    public Property $property;
    public array $images;

    public function __construct(Property $property)
    {
        $this->property = $property;
        $this->images = $property->images->pluck('image_url')->toArray();
    }

    public function build()
    {
        return $this->subject(__('emails.new_property.subject'))
            ->view('emails.new_property')
            ->with([
                'property' => $this->property,
                'images' => $this->images,
            ]);
    }
}
