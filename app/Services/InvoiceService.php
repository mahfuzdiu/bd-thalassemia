<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * @param $order
     */
    public function generateOrderInvoice($order)
    {
        $pdf = Pdf::loadView('order.invoice', ['order' => $order]);
        $filename = 'invoice-' . $order->order_number . '.pdf';
        Storage::disk('local')->put('invoices/' . $filename, $pdf->output());
    }
}
