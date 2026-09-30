<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuratCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $surat;

    /**
     * Create a new message instance.
     *
     * @param $surat
     * @return void
     */
    public function __construct($surat)
    {
        $this->surat = $surat;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $namaPenerima = is_object($this->surat->penerima)
            ? $this->surat->penerima->name  // relasi User
            : $this->surat->penerima;       // kalau sudah string

        $subject = 'Surat ' . $this->surat->jenis_surat . ' untuk ' . $namaPenerima;

        return $this->subject($subject)
                    ->view('emails.surat_created');
    }
}

