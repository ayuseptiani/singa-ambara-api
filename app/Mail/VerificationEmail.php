<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp; // Variabel untuk menampung OTP

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('dazaisan0624@gmail.com', 'Singa Ambara Suites') // <-- HARDCODE DISINI AGAR 100% AMAN
                    ->subject('Kode Verifikasi Akun')
                    ->view('emails.verification'); // Kita akan buat tampilan HTML-nya
    }
}