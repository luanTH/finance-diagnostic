<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Attachment;
use Illuminate\Queue\SerializesModels;

class DiagnosticReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public $pdfBinary
    ) {}

    public function build()
    {
        return $this->subject('📊 Seu Diagnóstico Financeiro está pronto!')
                    ->view('emails.diagnostic_report')
                    ->attachData($this->pdfBinary, 'diagnostico_financeiro.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
