<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicantRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $candidateName,
        public string $jobTitle,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Update on Your Application for {$this->jobTitle} | HSB Investasi");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.applicant-rejected',
            text: 'emails.applicant-rejected-text',
        );
    }
}
