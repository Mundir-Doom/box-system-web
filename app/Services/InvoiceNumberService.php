<?php

namespace App\Services;

use App\Models\Backend\Parcel;
use Carbon\Carbon;

class InvoiceNumberService
{
    /**
     * Generate a unique invoice number
     * Format: INV-YYYY-MM-DD-XXXXXX (where XXXXXX is a 6-digit sequential number)
     */
    public function generateInvoiceNumber()
    {
        $today = Carbon::now();
        $datePrefix = $today->format('Y-m-d');
        
        // Get the last invoice number for today
        $lastInvoice = Parcel::whereDate('created_at', $today->toDateString())
            ->whereNotNull('invoice_no')
            ->where('invoice_no', 'like', 'INV-' . $datePrefix . '-%')
            ->orderBy('invoice_no', 'desc')
            ->first();
        
        if ($lastInvoice) {
            // Extract the sequential number from the last invoice
            $lastNumber = (int) substr($lastInvoice->invoice_no, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            // First invoice of the day
            $nextNumber = 1;
        }
        
        // Format the sequential number with leading zeros
        $sequentialNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        
        return "INV-{$datePrefix}-{$sequentialNumber}";
    }
    
    /**
     * Generate a simple sequential invoice number
     * Format: INV-XXXXXX (where XXXXXX is a 6-digit sequential number)
     */
    public function generateSimpleInvoiceNumber()
    {
        // Get the last invoice number
        $lastInvoice = Parcel::whereNotNull('invoice_no')
            ->where('invoice_no', 'like', 'INV-%')
            ->orderBy('invoice_no', 'desc')
            ->first();
        
        if ($lastInvoice) {
            // Extract the sequential number from the last invoice
            $lastNumber = (int) substr($lastInvoice->invoice_no, 4); // Remove 'INV-' prefix
            $nextNumber = $lastNumber + 1;
        } else {
            // First invoice
            $nextNumber = 1;
        }
        
        // Format the sequential number with leading zeros
        $sequentialNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        
        return "INV-{$sequentialNumber}";
    }
    
    /**
     * Generate a merchant-specific invoice number
     * Format: INV-{MERCHANT_ID}-YYYYMMDD-XXXX
     */
    public function generateMerchantInvoiceNumber($merchantId)
    {
        $today = Carbon::now();
        $datePrefix = $today->format('Ymd');
        
        // Get the last invoice number for this merchant today
        $lastInvoice = Parcel::where('merchant_id', $merchantId)
            ->whereDate('created_at', $today->toDateString())
            ->whereNotNull('invoice_no')
            ->where('invoice_no', 'like', "INV-{$merchantId}-{$datePrefix}-%")
            ->orderBy('invoice_no', 'desc')
            ->first();
        
        if ($lastInvoice) {
            // Extract the sequential number from the last invoice
            $lastNumber = (int) substr($lastInvoice->invoice_no, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            // First invoice for this merchant today
            $nextNumber = 1;
        }
        
        // Format the sequential number with leading zeros
        $sequentialNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return "INV-{$merchantId}-{$datePrefix}-{$sequentialNumber}";
    }
    
    /**
     * Check if an invoice number is unique
     */
    public function isInvoiceNumberUnique($invoiceNumber)
    {
        return !Parcel::where('invoice_no', $invoiceNumber)->exists();
    }
    
    /**
     * Generate a unique invoice number with retry logic
     */
    public function generateUniqueInvoiceNumber($method = 'date_based')
    {
        $maxRetries = 10;
        $retryCount = 0;
        
        do {
            switch ($method) {
                case 'simple':
                    $invoiceNumber = $this->generateSimpleInvoiceNumber();
                    break;
                case 'merchant_based':
                    // This would need merchant_id parameter
                    $invoiceNumber = $this->generateInvoiceNumber();
                    break;
                case 'date_based':
                default:
                    $invoiceNumber = $this->generateInvoiceNumber();
                    break;
            }
            
            if ($this->isInvoiceNumberUnique($invoiceNumber)) {
                return $invoiceNumber;
            }
            
            $retryCount++;
        } while ($retryCount < $maxRetries);
        
        // Fallback: add timestamp to ensure uniqueness
        return $invoiceNumber . '-' . time();
    }
}
