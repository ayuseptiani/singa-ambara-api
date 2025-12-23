<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address; // <--- PENTING: Import Address
use Illuminate\Queue\SerializesModels;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    // 1. Definisikan properti publik agar bisa dibaca di View
    public $token;
    public $email;

    /**
     * Create a new message instance.
     * Menerima token dan email dari Controller
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the message envelope.
     * Mengatur Subjek dan Pengirim (Hardcode)
     */
    public function envelope()
    {
        return new Envelope(
            // HARDCODE PENGIRIM DISINI AGAR TIDAK KENA ERROR SMTP
            from: new Address('dazaisan0624@gmail.com', 'Singa Ambara Suites'),
            subject: 'Reset Password Request',
        );
    }

    /**
     * Get the message content definition.
     * Mengatur File View yang digunakan
     */
    public function content()
    {
        return new Content(
            // Pastikan file ini ada di resources/views/emails/reset_password.blade.php
            view: 'emails.reset_password', 
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments()
    {
        return [];
    }
}