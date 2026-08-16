<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Project;
use Carbon\Carbon;

class ReceiptScannerService
{
    /**
     * Sanitize string to ensure 100% valid UTF-8 without malformed bytes or control characters.
     */
    public function sanitizeUtf8(string $text): string
    {
        // 1. Convert to UTF-8
        $clean = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        // 2. Strip invalid sequences
        $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $clean);
        if ($clean === false) {
            $clean = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        // 3. Remove non-printable control characters (allow newline, carriage return, tab)
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string)$clean);

        return $clean ?? '';
    }

    /**
     * Clean messy OCR noise, barcode artifacts, and random symbols from scanned text.
     */
    public function cleanScannedText(string $rawText): string
    {
        $safeText = $this->sanitizeUtf8($rawText);
        $rawLines = explode("\n", $safeText);
        $cleanLines = [];

        foreach ($rawLines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                continue;
            }

            // 1. Strip leading/trailing dashes, equal signs, tildes, underscores
            $stripped = trim($trimmed, "-—_=~*#<>:; \t\n\r\0\x0B");

            // 2. Discard lines with very few alphanumeric characters or mostly symbols
            $alphaNumCount = preg_match_all('/[a-zA-Z0-9]/', $stripped);
            $symbolCount = preg_match_all('/[^a-zA-Z0-9\s]/', $stripped);
            $totalLen = strlen($stripped);

            if ($alphaNumCount < 3 || $totalLen < 3) {
                continue;
            }

            if ($symbolCount > $alphaNumCount && !str_contains($stripped, 'Rp') && !str_contains($stripped, 'Total')) {
                continue;
            }

            // 3. Filter out common OCR hallucination noise lines
            if (preg_match('/^(?:ee\s*\d+|a\s*br|elixlese|ram!|er\s*ways|mira\\\\|eset\s*eset|blossompinket)/i', $stripped)) {
                continue;
            }

            // 4. Normalize spacing inside line
            $normalized = preg_replace('/\s+/', ' ', $stripped);
            $normalized = preg_replace('/(\d)\s*,\s*(\d)/', '$1,$2', $normalized);
            $normalized = preg_replace('/(\d)\s*\.\s*(\d)/', '$1.$2', $normalized);

            // 5. Clean up OCR merchant typos
            if (preg_match('/(?:hil|k0pi|kopl)\s*kenangan/i', $normalized)) {
                $normalized = preg_replace('/(?:hil|k0pi|kopl)\s*kenangan/i', 'Kopi Kenangan', $normalized);
            }

            $cleanLines[] = $normalized;
        }

        return implode("\n", $cleanLines);
    }

    /**
     * Convert Indonesian spoken amount phrases into numerical float.
     * Supports:
     * - "42 ribu", "42rb", "42k", "42.000", "42000"
     * - "5 juta", "5jt", "5 jt"
     * - "1,5 juta", "1.5 juta", "1 koma 5 juta", "satu setengah juta"
     * - "2 juta 500 ribu", "2jt 500rb", "2 juta lima ratus ribu"
     * - "tiga puluh lima ribu", "seratus lima puluh ribu"
     */
    public function parseSpokenAmount(string $text): float
    {
        $lower = mb_strtolower($text);

        // Normalize decimal comma and spoken words
        $lower = str_replace(',', '.', $lower);
        $lower = str_replace(['koma', 'titik'], '.', $lower);
        $lower = str_replace('setengah', '0.5', $lower);
        $lower = str_replace('sejuta', '1 juta', $lower);
        $lower = str_replace('seribu', '1 ribu', $lower);

        // Pattern 1: X juta Y ribu (e.g. 2 juta 500 ribu / 2.5 juta)
        if (preg_match('/([0-9]+(?:\.[0-9]+)?)\s*(?:juta|jt)\s*(?:dan\s*)?([0-9]+(?:\.[0-9]+)?)\s*(?:ribu|rb|k)?/i', $lower, $m)) {
            $juta = (float)$m[1] * 1000000;
            $ribuVal = (float)$m[2];
            // If user says "2 juta 500" (omits ribu), treat 500 as 500.000 if < 1000
            $ribu = $ribuVal < 1000 ? $ribuVal * 1000 : $ribuVal;
            return $juta + $ribu;
        }

        // Pattern 2: Single Juta (e.g. 5 juta / 1.5jt / 1,5 juta)
        if (preg_match('/([0-9]+(?:\.[0-9]+)?)\s*(?:juta|jt)\b/i', $lower, $m)) {
            return (float)$m[1] * 1000000;
        }

        // Pattern 3: Single Ribu / RB / K (e.g. 42 ribu / 42rb / 42k / 150rb)
        if (preg_match('/([0-9]+(?:\.[0-9]+)?)\s*(?:ribu|rb|k)\b/i', $lower, $m)) {
            return (float)$m[1] * 1000;
        }

        // Pattern 4: Indonesian Word Numbers Converter (e.g. "tiga puluh lima ribu")
        $wordAmount = $this->convertIndonesianWordsToNumber($lower);
        if ($wordAmount > 0) {
            return $wordAmount;
        }

        return 0;
    }

    /**
     * Convert Indonesian spelled numbers to integer.
     */
    private function convertIndonesianWordsToNumber(string $text): float
    {
        $wordMap = [
            'satu' => 1, 'dua' => 2, 'tiga' => 3, 'empat' => 4, 'lima' => 5,
            'enam' => 6, 'tujuh' => 7, 'delapan' => 8, 'sembilan' => 9,
            'sepuluh' => 10, 'sebelas' => 11, 'dua belas' => 12, 'tiga belas' => 13,
            'empat belas' => 14, 'lima belas' => 15, 'enam belas' => 16,
            'tujuh belas' => 17, 'delapan belas' => 18, 'sembilan belas' => 19,
        ];

        // Check if string contains any number words
        $hasNumWords = false;
        foreach (array_keys($wordMap) as $w) {
            if (str_contains($text, $w)) {
                $hasNumWords = true;
                break;
            }
        }
        if (!$hasNumWords && !str_contains($text, 'ratus') && !str_contains($text, 'ribu') && !str_contains($text, 'juta')) {
            return 0;
        }

        $tokens = preg_split('/[\s\-_,]+/', $text);
        $total = 0;
        $section = 0;
        $current = 0;

        foreach ($tokens as $token) {
            if (isset($wordMap[$token])) {
                $current = $wordMap[$token];
            } elseif ($token === 'puluh') {
                $section += ($current === 0 ? 1 : $current) * 10;
                $current = 0;
            } elseif ($token === 'seratus') {
                $section += 100;
                $current = 0;
            } elseif ($token === 'ratus') {
                $section += ($current === 0 ? 1 : $current) * 100;
                $current = 0;
            } elseif ($token === 'seribu') {
                $total += 1000;
                $section = 0;
                $current = 0;
            } elseif ($token === 'ribu') {
                $section += $current;
                $total += ($section === 0 ? 1 : $section) * 1000;
                $section = 0;
                $current = 0;
            } elseif ($token === 'sejuta') {
                $total += 1000000;
                $section = 0;
                $current = 0;
            } elseif ($token === 'juta') {
                $section += $current;
                $total += ($section === 0 ? 1 : $section) * 1000000;
                $section = 0;
                $current = 0;
            } elseif (is_numeric($token)) {
                $current = (float)$token;
            }
        }

        $total += ($section + $current);
        return (float)$total;
    }

    /**
     * Parse natural spoken voice input (Voice-to-Transaction).
     */
    public function parseVoiceText(string $spokenText): array
    {
        $safeText = $this->sanitizeUtf8($spokenText);
        $lowerText = mb_strtolower($safeText);

        // 1. Detect Type
        $type = 'expense';
        if (preg_match('/(?:pemasukan|terima|dapat|gaji|income|masuk|dp|pelunasan|dibayar|cair)/i', $lowerText)) {
            $type = 'income';
        } elseif (preg_match('/(?:transfer|pindah|kirim\s*uang)/i', $lowerText)) {
            $type = 'transfer';
        }

        // 2. Extract Amount (Spoken converter + Regex fallback)
        $amount = $this->parseSpokenAmount($safeText);
        if ($amount <= 0) {
            $amount = $this->extractAmount($safeText);
        }

        // 3. Extract Date (support "hari ini", "kemarin", "lusa")
        $date = now()->format('Y-m-d');
        if (str_contains($lowerText, 'kemarin')) {
            $date = now()->subDay()->format('Y-m-d');
        } elseif (str_contains($lowerText, 'lusa')) {
            $date = now()->addDays(2)->format('Y-m-d');
        } elseif ($extractedDate = $this->extractDate($safeText)) {
            $date = $extractedDate;
        }

        // 4. Extract Description by cleaning command & filler words
        $cleanDesc = $safeText;
        $stripPatterns = [
            // Command preambles & conversational intros
            '/\b(?:tolong\s*)?(?:catat(?:kan)?|masukkan|tambah(?:kan)?|input|buat(?:kan)?|tulis(?:kan)?)\s*(?:transaksi|pengeluaran|pemasukan)?\b/ui',
            '/\b(?:aku|saya|gue|gw)\s*(?:baru\s*saja|baru|lagi|tadi|habis|udah|telah|dapat|terima)?\b/ui',
            '/\b(?:baru\s*saja|tadi\s*pagi|tadi\s*siang|tadi\s*malam|tadi|barusan|kemarin|hari\s*ini|lusa)\b/ui',
            '/\b(?:dapat|terima|cair|pemasukan|pengeluaran|transfer|income|expense)\b/ui',
            
            // Payment / Transfer destination method phrases: "bayar pakai cash", "masuk bca", "transfer ke mandiri", "via ovo", etc.
            '/\b(?:bayar|dibayar|pembayaran|bayarnya|masuk|ditransfer|kirim|diterima)\s*(?:pake|pakai|lewat|via|menggunakan|dengan|dari|ke)?\s*(?:rekening|akun|bank|dompet|uang)?\s*(?:bca|mandiri|bri|bni|jago|jenius|gopay|ovo|dana|shopeepay|cash|tunai|kontan|qris)\b/ui',
            '/\b(?:pake|pakai|lewat|via|menggunakan|dengan|dari|ke)\s*(?:rekening|akun|bank|dompet|uang)?\s*(?:bca|mandiri|bri|bni|jago|jenius|gopay|ovo|dana|shopeepay|cash|tunai|kontan|qris)\b/ui',
            '/\b(?:bayar\s*cash|bayar\s*tunai|bayar\s*qris|bayar\s*gopay|bayar\s*ovo|bayar\s*bca|masuk\s*bca|masuk\s*mandiri)\b/ui',
            '/\b(?:cash|tunai|kontan|qris)\b/ui',

            // Amount preambles and currency expressions: "habis Rp20.000", "seharga 20rb", "sebesar 50rb", "total 100k", "Rp20.000", "20 ribu"
            '/\b(?:sebesar|sejumlah|seharga|habis|total|nominal|sebanyak|biaya|harganya|tarif)\s*(?:rp\.?|idr)?\s*[0-9]+(?:\.[0-9]+)?\s*(?:juta|jt|ribu|rb|k)?\b/ui',
            '/\b(?:sebesar|sejumlah|seharga|habis|total|nominal|sebanyak|biaya|harganya|tarif)\b/ui',
            '/\b(?:rp\.?|idr)\s*[0-9.,]+\b/ui',
            '/\b[0-9]+(?:\.[0-9]+)?\s*(?:juta|jt|ribu|rb|k)\b/ui',
            '/\b[0-9.,]+\b/u',
            '/\b(?:rp\.?|rupiah|idr)\b/ui',

            // Spelled number phrases
            '/\b(?:satu|dua|tiga|empat|lima|enam|tujuh|delapan|sembilan|sepuluh|sebelas|belas|puluh|seratus|ratus|seribu|ribu|sejuta|juta)\b/ui',

            // Conversational fillers
            '/\b(?:dong|ya|deh|nih|sih|loh|lah|tolong|plis|please|dong\s*ya)\b/ui',
        ];

        foreach ($stripPatterns as $p) {
            $cleanDesc = preg_replace($p, ' ', $cleanDesc);
        }

        $cleanDesc = trim(preg_replace('/\s+/', ' ', $cleanDesc));
        $cleanDesc = trim($cleanDesc, "-—:=,. \t\n\r\0\x0B");

        if (empty($cleanDesc) || strlen($cleanDesc) < 3) {
            $cleanDesc = $this->extractDescription([$spokenText], $lowerText, $type);
        } else {
            // Check known merchants
            $knownMerchant = $this->matchKnownMerchant($lowerText);
            if ($knownMerchant) {
                $cleanDesc = $knownMerchant;
            } else {
                $cleanDesc = mb_convert_case($cleanDesc, MB_CASE_TITLE, "UTF-8");
                $cleanDesc = preg_replace('/\bDp\b/', 'DP', $cleanDesc);
                $cleanDesc = preg_replace('/\bPln\b/', 'PLN', $cleanDesc);
                $cleanDesc = preg_replace('/\bPdam\b/', 'PDAM', $cleanDesc);
            }
        }

        // 5. Match Category
        $categoryId = $this->suggestCategory($lowerText, $type);

        // 6. Match Account
        $accountId = $this->suggestAccount($lowerText);

        // 7. Match Project
        $projectId = $this->suggestProject($lowerText);

        return [
            'type' => $type,
            'amount' => $amount > 0 ? $amount : null,
            'date' => $date,
            'description' => $this->sanitizeUtf8($cleanDesc),
            'category_id' => $categoryId,
            'account_id' => $accountId,
            'project_id' => $projectId,
            'raw_text' => $safeText,
            'cleaned_text' => $this->sanitizeUtf8($cleanDesc),
        ];
    }

    /**
     * Parse extracted raw text from receipt / invoice / transfer slip.
     */
    public function parseReceiptText(string $rawText): array
    {
        $safeRawText = $this->sanitizeUtf8($rawText);
        $cleanedText = $this->cleanScannedText($safeRawText);
        $textToParse = !empty($cleanedText) ? $cleanedText : trim($safeRawText);
        $lines = array_values(array_filter(array_map('trim', explode("\n", $textToParse))));
        $lowerText = mb_strtolower($textToParse . "\n" . $safeRawText);

        // 1. Detect Transaction Type
        $type = $this->detectType($lowerText);

        // 2. Clean noise (phone numbers, NPWP, barcode numbers) and extract Amount
        $amount = $this->extractAmount($textToParse);
        if ($amount <= 0) {
            $amount = $this->extractAmount($safeRawText);
        }
        if ($amount <= 0) {
            // Try voice / spoken parser if single sentence
            $amount = $this->parseSpokenAmount($safeRawText);
        }

        // 3. Extract Date
        $date = $this->extractDate($textToParse) ?? $this->extractDate($safeRawText) ?? now()->format('Y-m-d');

        // 4. Extract Merchant / Description
        $description = $this->extractDescription($lines, $lowerText, $type);

        // 5. Suggest Category
        $categoryId = $this->suggestCategory($lowerText, $type);

        // 6. Suggest Account
        $accountId = $this->suggestAccount($lowerText);

        // 7. Check if project keyword exists
        $projectId = $this->suggestProject($lowerText);

        return [
            'type' => $type,
            'amount' => $amount > 0 ? $amount : null,
            'date' => $date,
            'description' => $this->sanitizeUtf8($description),
            'category_id' => $categoryId,
            'account_id' => $accountId,
            'project_id' => $projectId,
            'raw_text' => $safeRawText,
            'cleaned_text' => $this->sanitizeUtf8($cleanedText),
        ];
    }

    /**
     * Detect if transaction is Income, Expense, or Transfer.
     */
    private function detectType(string $text): string
    {
        $incomeKeywords = [
            'transfer masuk', 'dana masuk', 'penerimaan', 'kredit', 'cr', 'income',
            'terima dari', 'invoice paid', 'lunas dibayar klien', 'gaji', 'fee project',
            'pembayaran masuk', 'top up berhasil ke rekening', 'terima uang', 'dp project', 'dp', 'pemasukan'
        ];

        $transferKeywords = [
            'transfer antar rekening', 'pemindahan dana', 'pindah buku', 'overbooking', 'antar bank sendiri'
        ];

        foreach ($transferKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return 'transfer';
            }
        }

        foreach ($incomeKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return 'income';
            }
        }

        return 'expense';
    }

    /**
     * Extract nominal amount from text using high-precision priority stages.
     */
    private function extractAmount(string $text): float
    {
        // 1. Pre-filter known noise (phone numbers, NPWP, long barcode strings)
        $cleanText = $text;

        // Remove phone numbers e.g. +62-81-7073-9110 or 0817-0756-865
        $cleanText = preg_replace('/(?:\+62|08|628)[0-9\-\s]{7,18}/', ' ', $cleanText);

        // Remove NPWP e.g. 82.877.376.2-029.000
        $cleanText = preg_replace('/\b\d{2}\.\d{3}\.\d{3}\.\d{1,2}\-\d{3}\.\d{3}\b/', ' ', $cleanText);

        // Remove barcode/order ID numbers (8 to 25 contiguous digits)
        $cleanText = preg_replace('/\b(?:take\s*away|order|trx|transaksi|ref|inv|barcode|resi|terminal|server)[\s#:]*([0-9]{7,25})\b/i', ' ', $cleanText);
        $cleanText = preg_replace('/\b\d{10,30}\b/', ' ', $cleanText);

        // Normalize space between decimal digits e.g. 42 , 000 or 42 . 000
        $cleanText = preg_replace('/(\d)\s*,\s*(\d)/', '$1,$2', $cleanText);
        $cleanText = preg_replace('/(\d)\s*\.\s*(\d)/', '$1.$2', $cleanText);

        $lines = array_values(array_filter(array_map('trim', explode("\n", $cleanText))));

        // STAGE 1 (HIGHEST PRIORITY): Look for explicit TOTAL / GRAND TOTAL / BAYAR / JUMLAH line
        $totalPatterns = [
            '/(?:grand\s*total|total\s*bayar|total\s*tagihan|total\s*belanja|total|jumlah\s*bayar|jumlah\s*transfer|jumlah\s*tagihan|jumlah\s*dana|jumlah|gopay\s*qr|gopay|qris|tunai|cash|debit|tagihan)[\s.:=–-]*([rRpPiIdD\s]{0,4}[0-9.,]+)/i',
        ];

        foreach ($lines as $line) {
            foreach ($totalPatterns as $pattern) {
                if (preg_match($pattern, $line, $m)) {
                    $amount = $this->cleanAmountString($m[1]);
                    if ($amount >= 500 && !in_array((int)$amount, [2022, 2023, 2024, 2025, 2026, 2027])) {
                        return $amount;
                    }
                }
            }
        }

        // STAGE 2: Explicit Currency Identifier (Rp 42.000 / IDR 42,000)
        $currencyMatches = [];
        if (preg_match_all('/(?:rp\.?|idr)\s*([0-9]{1,3}(?:[.,][0-9]{3})*(?:[.,][0-9]{2})?)/i', $cleanText, $matches)) {
            foreach ($matches[1] as $match) {
                $amount = $this->cleanAmountString($match);
                if ($amount >= 500 && !in_array((int)$amount, [2022, 2023, 2024, 2025, 2026, 2027])) {
                    $currencyMatches[] = $amount;
                }
            }
        }
        if (!empty($currencyMatches)) {
            return (float) max($currencyMatches);
        }

        // STAGE 3: Formatted Price Numbers with Thousands Separator (e.g. 42.000 or 42,000)
        $priceMatches = [];
        if (preg_match_all('/(?:\b)([0-9]{1,3}(?:[.,][0-9]{3})+(?:[.,][0-9]{2})?)(?:\b)/', $cleanText, $matches)) {
            foreach ($matches[1] as $match) {
                $amount = $this->cleanAmountString($match);
                if ($amount >= 500 && !in_array((int)$amount, [2022, 2023, 2024, 2025, 2026, 2027])) {
                    $priceMatches[] = $amount;
                }
            }
        }
        if (!empty($priceMatches)) {
            return (float) max($priceMatches);
        }

        return 0;
    }

    /**
     * Clean amount string to standard float.
     */
    private function cleanAmountString(string $val): float
    {
        $cleaned = trim(preg_replace('/[^0-9.,]/', '', $val));
        if (empty($cleaned)) {
            return 0;
        }

        // Indonesian format: 42.000 or 42.000,00 or 42,000
        if (str_contains($cleaned, '.') && str_contains($cleaned, ',')) {
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
        } elseif (str_contains($cleaned, '.')) {
            if (preg_match('/\.\d{3}$/', $cleaned) || substr_count($cleaned, '.') > 1) {
                $cleaned = str_replace('.', '', $cleaned);
            }
        } elseif (str_contains($cleaned, ',')) {
            if (preg_match('/,\d{3}$/', $cleaned) || substr_count($cleaned, ',') > 1) {
                $cleaned = str_replace(',', '', $cleaned);
            } else {
                $cleaned = str_replace(',', '.', $cleaned);
            }
        }

        return (float) $cleaned;
    }

    /**
     * Extract date from text.
     */
    private function extractDate(string $text): ?string
    {
        $months = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'mei' => 5, 'may' => 5,
            'jun' => 6, 'jul' => 7, 'agu' => 8, 'aug' => 8, 'sep' => 9, 'okt' => 10,
            'oct' => 10, 'nov' => 11, 'des' => 12, 'dec' => 12
        ];

        // Format 1: Month Day Year (e.g. Jan 20 2024 or Jan 20, 2024)
        foreach ($months as $monthKey => $monthNum) {
            if (preg_match('/\b' . $monthKey . '[a-z]*\s+(\d{1,2})[,\s]+(\d{4})\b/i', $text, $m)) {
                try {
                    return Carbon::create($m[2], $monthNum, $m[1])->format('Y-m-d');
                } catch (\Exception $e) {
                    // Fallback
                }
            }
        }

        // Format 2: Day Month Year (e.g. 20 Jan 2024 or 20 Januari 2024)
        foreach ($months as $monthKey => $monthNum) {
            if (preg_match('/\b(\d{1,2})\s+' . $monthKey . '[a-z]*\s+(\d{4})\b/i', $text, $m)) {
                try {
                    return Carbon::create($m[2], $monthNum, $m[1])->format('Y-m-d');
                } catch (\Exception $e) {
                    // Fallback
                }
            }
        }

        // Format 3: DD/MM/YYYY or DD-MM-YYYY
        if (preg_match('/\b(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})\b/', $text, $m)) {
            try {
                return Carbon::createFromFormat('d/m/Y', "{$m[1]}/{$m[2]}/{$m[3]}")->format('Y-m-d');
            } catch (\Exception $e) {
                // Fallback
            }
        }

        // Format 4: YYYY-MM-DD
        if (preg_match('/\b(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})\b/', $text, $m)) {
            try {
                return Carbon::createFromFormat('Y-m-d', "{$m[1]}-{$m[2]}-{$m[3]}")->format('Y-m-d');
            } catch (\Exception $e) {
                // Fallback
            }
        }

        return null;
    }

    /**
     * Match Known Merchant.
     */
    private function matchKnownMerchant(string $lowerText): ?string
    {
        $merchants = [
            'kopi kenangan' => 'Kopi Kenangan',
            'kenangan' => 'Kopi Kenangan',
            'starbucks' => 'Starbucks Coffee',
            'fore coffee' => 'Fore Coffee',
            'fore' => 'Fore Coffee',
            'janji jiwa' => 'Kopi Janji Jiwa',
            'point coffee' => 'Point Coffee',
            'indomaret' => 'Belanja Indomaret',
            'alfamart' => 'Belanja Alfamart',
            'alfamidi' => 'Belanja Alfamidi',
            'superindo' => 'Belanja Superindo',
            'mcdonald' => 'McDonald\'s',
            'mcd' => 'McDonald\'s',
            'kfc' => 'KFC Restaurant',
            'gofood' => 'Pesanan GoFood',
            'grabfood' => 'Pesanan GrabFood',
            'shopeefood' => 'Pesanan ShopeeFood',
            'tokopedia' => 'Pembelian Tokopedia',
            'shopee' => 'Pembelian Shopee',
            'pertamina' => 'Isi Bensin Pertamina',
            'shell' => 'Isi Bensin Shell',
            'pln' => 'Pembayaran Listrik PLN',
            'pdam' => 'Pembayaran Air PDAM',
            'indihome' => 'Langganan IndiHome',
            'biznet' => 'Langganan Biznet',
            'telkomsel' => 'Beli Pulsa/Paket Telkomsel',
            'myorbit' => 'Beli Paket Orbit',
            'adobe' => 'Langganan Adobe Creative Cloud',
            'figma' => 'Langganan Figma Professional',
            'chatgpt' => 'Langganan ChatGPT Plus',
            'openai' => 'Langganan ChatGPT Plus / OpenAI',
            'canva' => 'Langganan Canva Pro',
            'github' => 'Langganan GitHub Copilot',
            'digitalocean' => 'Server DigitalOcean',
            'hostinger' => 'Hosting Web Hostinger',
            'sewa kamera' => 'Sewa Kamera & Lensa',
            'warteg' => 'Makan Warteg',
        ];

        foreach ($merchants as $needle => $name) {
            if (str_contains($lowerText, $needle)) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Extract Description / Merchant Name.
     */
    private function extractDescription(array $lines, string $lowerText, string $type): string
    {
        $known = $this->matchKnownMerchant($lowerText);
        if ($known) {
            return $known;
        }

        if ($type === 'income') {
            if (str_contains($lowerText, 'dp') || str_contains($lowerText, 'down payment')) {
                return 'DP Pembayaran Project';
            }
            if (str_contains($lowerText, 'pelunasan')) {
                return 'Pelunasan Fee Project';
            }
            return 'Penerimaan Dana / Fee Klien';
        }

        // Grab first meaningful non-empty line
        foreach ($lines as $line) {
            $cleaned = trim($line);
            if (strlen($cleaned) >= 3 && !preg_match('/^[0-9\/\-\.:\s#=—_]+$/', $cleaned) && !preg_match('/(struk|nota|invoice|receipt|selamat datang|take away)/i', $cleaned)) {
                return mb_convert_case($cleaned, MB_CASE_TITLE, "UTF-8");
            }
        }

        return 'Transaksi Pembelian / Belanja';
    }

    /**
     * Suggest Category based on semantic keywords and category names.
     */
    private function suggestCategory(string $lowerText, string $type): ?int
    {
        $categories = Category::where('type', $type)->get();
        if ($categories->isEmpty()) {
            return null;
        }

        // Semantic Category Mappings
        $categoryMappings = [
            [
                'targets' => ['makan', 'minum', 'kuliner', 'food', 'f&b', 'kopi', 'nongkrong', 'konsumsi'],
                'triggers' => [
                    'kopi', 'coffee', 'latte', 'kenangan', 'starbucks', 'fore', 'janji jiwa', 'point coffee',
                    'cafe', 'resto', 'restoran', 'warung', 'makan', 'minum', 'bakso', 'mie', 'nasi', 'ayam',
                    'burger', 'pizza', 'roti', 'snack', 'gofood', 'grabfood', 'shopeefood', 'indomaret', 'alfamart',
                    'superindo', 'alfamidi', 'beverage', 'tea', 'boba', 'espresso', 'cappuccino', 'ice cream', 'warteg'
                ],
            ],
            [
                'targets' => ['transport', 'operasional', 'kendaraan', 'bensin'],
                'triggers' => [
                    'bensin', 'pertamina', 'shell', 'spbu', 'gojek', 'goride', 'gocar', 'grab', 'toll', 'parkir',
                    'service motor', 'service mobil', 'tambal ban', 'ojek', 'pertalite', 'pertamax'
                ],
            ],
            [
                'targets' => ['software', 'server', 'hosting', 'tools', 'equipment', 'langganan', 'digital', 'perangkat'],
                'triggers' => [
                    'adobe', 'figma', 'canva', 'openai', 'chatgpt', 'github', 'hosting', 'hostinger', 'domain',
                    'digitalocean', 'google one', 'apple', 'aws', 'vps', 'cloudflare', 'midjourney', 'cursor',
                    'claude', 'kamera', 'lensa', 'tripod', 'lighting', 'mic', 'macbook', 'ipad', 'keyboard'
                ],
            ],
            [
                'targets' => ['listrik', 'wifi', 'kos', 'utilitas', 'air', 'internet', 'tagihan'],
                'triggers' => [
                    'pln', 'listrik', 'token pln', 'pdam', 'air', 'wifi', 'internet', 'indihome', 'biznet',
                    'myorbit', 'telkomsel', 'xl', 'indosat', 'pulsa', 'kuota', 'kos', 'sewa kos', 'kontrakan'
                ],
            ],
            [
                'targets' => ['lifestyle', 'hiburan', 'belanja', 'pribadi', 'hobi', 'entertainment'],
                'triggers' => [
                    'bioskop', 'cinema', 'xxi', 'cgv', 'netflix', 'spotify', 'youtube premium', 'steam', 'game',
                    'baju', 'kaos', 'sepatu', 'tokopedia', 'shopee', 'lazada', 'mall', 'gym', 'fitness'
                ],
            ],
            [
                'targets' => ['project', 'proyek', 'freelance', 'fee', 'jasa', 'klien', 'pendapatan'],
                'triggers' => [
                    'project', 'proyek', 'klien', 'client', 'dp project', 'dp', 'pelunasan', 'fee project',
                    'jasa desain', 'jasa website', 'video shooting', 'animasi', 'fee'
                ],
            ],
        ];

        foreach ($categoryMappings as $mapping) {
            foreach ($mapping['triggers'] as $trigger) {
                if (str_contains($lowerText, $trigger)) {
                    foreach ($categories as $cat) {
                        $catName = mb_strtolower($cat->name);
                        foreach ($mapping['targets'] as $target) {
                            if (str_contains($catName, $target)) {
                                return $cat->id;
                            }
                        }
                    }
                }
            }
        }

        // Fallback: check direct match between text and category name
        foreach ($categories as $cat) {
            $catName = mb_strtolower($cat->name);
            $words = array_filter(explode(' ', preg_replace('/[^a-z0-9]/', ' ', $catName)));
            foreach ($words as $w) {
                if (strlen($w) >= 4 && str_contains($lowerText, $w)) {
                    return $cat->id;
                }
            }
        }

        return null;
    }

    /**
     * Suggest Account based on bank / e-wallet keywords.
     */
    private function suggestAccount(string $lowerText): ?int
    {
        $accounts = Account::where('is_active', true)->get();
        if ($accounts->isEmpty()) {
            return null;
        }

        // 1. Cash / Tunai keywords (e.g. "bayar pakai cash", "tunai", "uang fisik", "dompet")
        if (preg_match('/\b(?:cash|tunai|kontan|dompet|uang\s*tunai|uang\s*cash|uang\s*fisik)\b/i', $lowerText)) {
            $cashAcc = $accounts->first(fn($a) => $a->type === 'cash' || str_contains(mb_strtolower($a->name), 'cash') || str_contains(mb_strtolower($a->name), 'tunai') || str_contains(mb_strtolower($a->name), 'dompet'));
            if ($cashAcc) {
                return $cashAcc->id;
            }
        }

        // 2. GoPay / OVO / E-Wallets (e.g. "pake gopay", "ovo", "dana", "shopeepay", "qris")
        if (preg_match('/\b(?:gopay|ovo|dana|shopeepay|spay|qris|linkaja|ewallet|e-wallet|gojek|grab)\b/i', $lowerText)) {
            $ewalletAcc = $accounts->first(fn($a) => $a->type === 'ewallet' || str_contains(mb_strtolower($a->name), 'gopay') || str_contains(mb_strtolower($a->name), 'ovo') || str_contains(mb_strtolower($a->name), 'dana') || str_contains(mb_strtolower($a->name), 'shopee'));
            if ($ewalletAcc) {
                return $ewalletAcc->id;
            }
        }

        // 3. Bank BCA
        if (preg_match('/\b(?:bca|klikbca|m-bca|mbca|bank\s*bca)\b/i', $lowerText)) {
            $bcaAcc = $accounts->first(fn($a) => str_contains(mb_strtolower($a->name), 'bca'));
            if ($bcaAcc) {
                return $bcaAcc->id;
            }
        }

        // 4. Bank Mandiri
        if (preg_match('/\b(?:mandiri|livin|bank\s*mandiri)\b/i', $lowerText)) {
            $mandiriAcc = $accounts->first(fn($a) => str_contains(mb_strtolower($a->name), 'mandiri'));
            if ($mandiriAcc) {
                return $mandiriAcc->id;
            }
        }

        // 5. Bank BRI
        if (preg_match('/\b(?:bri|brimo|bank\s*bri)\b/i', $lowerText)) {
            $briAcc = $accounts->first(fn($a) => str_contains(mb_strtolower($a->name), 'bri'));
            if ($briAcc) {
                return $briAcc->id;
            }
        }

        // 6. Bank BNI
        if (preg_match('/\b(?:bni|wondr|bank\s*bni)\b/i', $lowerText)) {
            $bniAcc = $accounts->first(fn($a) => str_contains(mb_strtolower($a->name), 'bni'));
            if ($bniAcc) {
                return $bniAcc->id;
            }
        }

        // 7. Bank Jago
        if (preg_match('/\b(?:jago|bank\s*jago)\b/i', $lowerText)) {
            $jagoAcc = $accounts->first(fn($a) => str_contains(mb_strtolower($a->name), 'jago'));
            if ($jagoAcc) {
                return $jagoAcc->id;
            }
        }

        // 8. Jenius / BTPN
        if (preg_match('/\b(?:jenius|btpn)\b/i', $lowerText)) {
            $jeniusAcc = $accounts->first(fn($a) => str_contains(mb_strtolower($a->name), 'jenius') || str_contains(mb_strtolower($a->name), 'btpn'));
            if ($jeniusAcc) {
                return $jeniusAcc->id;
            }
        }

        return $accounts->first()?->id;
    }

    /**
     * Suggest Project if project title mentioned.
     */
    private function suggestProject(string $lowerText): ?int
    {
        $projects = Project::whereIn('status', ['prospect', 'in_progress', 'completed'])->get();

        foreach ($projects as $project) {
            $projName = mb_strtolower($project->name);
            if (strlen($projName) >= 3 && str_contains($lowerText, $projName)) {
                return $project->id;
            }
        }

        return null;
    }
}
