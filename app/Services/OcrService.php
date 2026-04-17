<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrService
{
    /**
     * Run OCR on an image file and return extracted text
     */
    public function extractText(string $imagePath): string
    {
        try {
            $ocr = new TesseractOCR($imagePath);
            $ocr->lang('eng');        // English language
            $ocr->psm(6);             // Assume a single uniform block of text
            return $ocr->run();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Extract Transaction ID from raw OCR text
     * Telebirr and CBE Birr transaction IDs follow specific patterns
     */
    public function extractTransactionId(string $text): ?string
    {
        // Telebirr transaction IDs — look for patterns like "TXN123456789" or long numbers
        $patterns = [
            '/(?:Transaction\s*(?:ID|Id|id|No|Number)[:\s#]*)([\w\d]{6,20})/i',
            '/(?:Ref(?:erence)?\s*(?:No|Number|ID)?[:\s#]*)([\w\d]{6,20})/i',
            '/(?:Receipt\s*(?:No|Number)?[:\s#]*)([\w\d]{6,20})/i',
            '/\b([A-Z]{2,4}\d{8,15})\b/',  // Pattern like TXN12345678
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    /**
     * Extract Amount from raw OCR text
     */
    public function extractAmount(string $text): ?float
    {
        // Look for patterns like "Amount: 500.00" or "ETB 500" or "Birr 500.00"
        $patterns = [
            '/(?:Amount|Total|Paid|ETB|Birr)[:\s]*([0-9,]+\.?[0-9]{0,2})/i',
            '/([0-9,]+\.[0-9]{2})\s*(?:ETB|Birr|birr)/i',
            '/(?:Amount)[:\s=]*([0-9]+(?:\.[0-9]{1,2})?)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                // Remove commas and convert to float
                return (float) str_replace(',', '', $matches[1]);
            }
        }

        return null;
    }

    /**
     * Extract Date from raw OCR text
     */
    public function extractDate(string $text): ?string
    {
        // Look for common date formats
        $patterns = [
            '/(\d{4}[-\/]\d{2}[-\/]\d{2})/',          // 2024-01-15 or 2024/01/15
            '/(\d{2}[-\/]\d{2}[-\/]\d{4})/',           // 15-01-2024 or 15/01/2024
            '/(\d{1,2}\s+(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+\d{4})/i', // 15 Jan 2024
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                try {
                    return date('Y-m-d', strtotime($matches[1]));
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return null;
    }

    /**
     * Run full extraction — returns all data at once
     */
    public function processPaymentScreenshot(string $imagePath): array
    {
        $rawText = $this->extractText($imagePath);

        return [
            'raw_text'       => $rawText,
            'transaction_id' => $this->extractTransactionId($rawText),
            'amount'         => $this->extractAmount($rawText),
            'date'           => $this->extractDate($rawText),
        ];
    }
}