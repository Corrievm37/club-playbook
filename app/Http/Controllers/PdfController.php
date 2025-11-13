<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function invoice(int $id)
    {
        $invoice = Invoice::with(['club','player','fee','payments'])->findOrFail($id);
        $club = $invoice->club;

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice','club'))
            ->setPaper('a4');

        $filename = $invoice->number . '.pdf';
        $path = 'invoices/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        if (!$invoice->pdf_path) {
            $invoice->pdf_path = $path;
            $invoice->saveQuietly();
        }

        return response()->download(Storage::disk('public')->path($path), $filename);
    }
}
