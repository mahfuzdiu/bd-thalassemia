<?php

namespace App\Services;

use App\Jobs\GenerateInvoiceOnOrderConfirmation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * @param $order
     */
    public function generateOrderInvoice($order)
    {
        GenerateInvoiceOnOrderConfirmation::dispatch($order);
    }
}
