<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SendUrlMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Send Url Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Genera una URL firmada con una ruta con nombre
        $urlSigned = URL::temporarySignedRoute(
            'validationView', now()->addMinutes(30), ['user_id' => $this->user->id]
        );
        Log::info('URL firmada: '.$urlSigned);
        
         return new Content(
            view: 'send.mail',

            with:[
                'name'=>$this->puser->name,
                'urlSigned'=>$urlSigned,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
