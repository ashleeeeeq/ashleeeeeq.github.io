<?php

namespace App\Mail;

use App\Models\Donation;
use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DonationReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Donation $donation, public Receipt $receipt)
    {
    }

    public function build()
    {
        $disk = Storage::disk(config('filesystems.default'));

        $mail = $this->subject('Your FAIRALL donation receipt')
            ->view('emails.donation_receipt')
            ->with([
                'donation' => $this->donation,
                'receipt' => $this->receipt,
                'pdfAttached' => true,
            ]);

        try {
            $pdfContents = $disk->get($this->receipt->path);

            if ($pdfContents) {
                return $mail->attachData(
                    $pdfContents,
                    basename($this->receipt->path),
                    ['mime' => 'application/pdf']
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to read receipt PDF for attachment', [
                'donation_id' => $this->donation->id,
                'receipt_id' => $this->receipt->id,
                'path' => $this->receipt->path,
                'error' => $e->getMessage(),
            ]);
        }

        return $mail->with(['pdfAttached' => false]);
    }
}
