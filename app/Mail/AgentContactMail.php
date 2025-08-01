<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $agentName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $details, string $agentName)
    {
        $this->details = $details;
        $this->agentName = $agentName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = trans('property.contact.email_subject', ['propertyName' => $this->details['propertyName']]);
        return $this->subject($subject)
            ->markdown('emails.agent.contact'); // Path to your Markdown email view
    }
}
