<?php

namespace App\Jobs;

use App\Models\Certificado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateCertificatePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The certificate instance.
     *
     * @var \App\Models\Certificado
     */
    public $certificado;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Certificado  $certificado
     * @return void
     */
    public function __construct(Certificado $certificado)
    {
        $this->certificado = $certificado;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $certificado = $this->certificado;
            $nomeArquivo = 'certificado-' . $certificado->id . '.pdf';
            $path = 'certificados/' . $nomeArquivo;

            $pdf = Pdf::loadView('certificados.pdf', compact('certificado'))
                      ->setPaper('a4', 'landscape');

            $conteudoPdf = $pdf->output();

            Storage::disk('public')->put($path, $conteudoPdf);

            $certificado->path = $path;
            $certificado->save();
        } catch (\Exception $e) {
            Log::error("Erro ao gerar PDF para o certificado ID: {$this->certificado->id} - " . $e->getMessage());
            $this->fail($e);
        }
    }
}
