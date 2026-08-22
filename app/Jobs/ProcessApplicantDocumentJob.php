<?php

namespace App\Jobs;

use App\Models\ApplicantDocument;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessApplicantDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public ApplicantDocument $document) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Queue berhasil memproses pengajuan dokumen ID: {$this->document->id} | Reg No: {$this->document->number_registration}");
    }
}
