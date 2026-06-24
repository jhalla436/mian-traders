<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Str;

class PdfPriceListParser
{
    /**
     * Parse a PDF file and return a collection of structured product variants with their sizes and prices.
     *
     * @param string $filePath Path to the uploaded PDF file
     * @param int|null $companyId Optional company ID for code mapping
     * @return array Array of parsed products
     */
    public function parse(string $filePath, ?int $companyId = null): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        return $this->parseText($text, $companyId);
    }

    /**
     * Parse a CSV/TXT price list file and return structured product variants.
     *
     * @param string $filePath Path to the uploaded CSV/TXT file
     * @param int|null $companyId Optional company ID for code mapping
     * @return array Array of parsed products
     */
    public function parseCsv(string $filePath, ?int $companyId = null): array
    {
        $fh = fopen($filePath, 'r');
        if (!$fh) {
            return [];
        }

        $firstBytes = fread($fh, 3);
        if ($firstBytes !== "\xEF\xBB\xBF") {
            rewind($fh);
        }

        $header = fgetcsv($fh);
        if (!$header) {
            fclose($fh);
            return [];
        }

        $headerMap = [];
        foreach ($header as $i => $column) {
            $normalized = preg_replace('/[^a-z0-9]+/i', '', strtolower(trim((string) $column)));
            $headerMap[$normalized] = $i;
        }

        $findColumn = function (array $aliases) use ($headerMap): ?int {
            foreach ($aliases as $alias) {
                $normalizedAlias = preg_replace('/[^a-z0-9]+/i', '', strtolower(trim((string) $alias)));
                if (array_key_exists($normalizedAlias, $headerMap)) {
                    return $headerMap[$normalizedAlias];
                }

                foreach ($headerMap as $headerKey => $index) {
                    if ($headerKey === $normalizedAlias || str_contains($headerKey, $normalizedAlias) || str_contains($normalizedAlias, $headerKey)) {
                        return $index;
                    }
                }
            }

            return null;
        };

        $get = function (array $row, array $aliases) use ($findColumn) {
            $index = $findColumn($aliases);
            return $index !== null && isset($row[$index]) ? trim((string) $row[$index]) : null;
        };

        $cleanPrice = function (?string $value): ?float {
            if ($value === null) {
                return null;
            }

            $raw = trim($value);
            if ($raw === '') {
                return null;
            }

            $normalized = preg_replace('/[^
0-9.,]/', '', $raw);
            if ($normalized === '' || $normalized === null) {
                return null;
            }

            if (str_contains($normalized, ',')) {
                if (preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $normalized)) {
                    $normalized = str_replace(',', '', $normalized);
                } else {
                    $normalized = str_replace(',', '.', $normalized);
                }
            }

            if (str_contains($normalized, '.')) {
                $parts = explode('.', $normalized);
                $integerPart = $parts[0] ?? '';
                $fractionPart = $parts[1] ?? '';

                // OCR and locale fallback for values like 0.14500 or 14.500 where the separator is not a decimal point.
                if ($fractionPart !== '' && strlen($fractionPart) >= 3 && preg_match('/^\d+$/', $integerPart)) {
                    $collapsed = preg_replace('/\./', '', $normalized);
                    $collapsed = ltrim($collapsed, '0');
                    if ($collapsed !== '') {
                        return (float) $collapsed;
                    }
                }
            }

            $normalized = preg_replace('/[^0-9.]/', '', $normalized);
            if ($normalized === '') {
                return null;
            }

            $price = (float) $normalized;
            if ($price > 0 && $price < 1 && preg_match('/\d{3,}/', $raw)) {
                $collapsed = preg_replace('/[^0-9]/', '', $raw);
                if ($collapsed !== '') {
                    return (float) $collapsed;
                }
            }

            return $price;
        };

        $parseSizeValue = function (?string $value): array {
            $value = trim((string) $value);
            if ($value === '') {
                return [null, null, null];
            }

            if (preg_match('/(\d+(?:\.\d+)?)\s*[x×\s-]\s*(\d+(?:\.\d+)?)(?:\s*[x×\s-]\s*(\d+(?:\.\d+)?))?/i', $value, $matches)) {
                return [
                    (float) $matches[1],
                    (float) $matches[2],
                    isset($matches[3]) && $matches[3] !== '' ? (float) $matches[3] : null,
                ];
            }

            $parts = preg_split('/\s+/', $value);
            $parts = array_values(array_filter($parts, static fn ($part) => is_numeric($part)));
            if (count($parts) >= 2) {
                return [
                    (float) $parts[0],
                    (float) $parts[1],
                    isset($parts[2]) ? (float) $parts[2] : null,
                ];
            }

            return [null, null, null];
        };

        $parsedItems = [];
        $priceColumnIndex = $findColumn(['mrp', 'rate', 'sell', 'sell_price', 'selling_price', 'price', 'amount']);
        while (($row = fgetcsv($fh)) !== false) {
            if ($row === [null] || count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            if ($priceColumnIndex !== null && count($row) > count($header) && isset($row[$priceColumnIndex])) {
                $mergedPrice = implode(',', array_slice($row, $priceColumnIndex));
                $row = array_slice($row, 0, $priceColumnIndex) + [$priceColumnIndex => $mergedPrice];
                ksort($row);
                $row = array_values($row);
            }

            $name = $get($row, ['name', 'product name', 'product', 'product_key', 'item', 'description']);
            $price = $cleanPrice($get($row, ['mrp', 'rate', 'sell', 'sell_price', 'selling_price', 'price', 'amount']));

            if ($name === null || trim($name) === '') {
                continue;
            }

            $length = $cleanPrice($get($row, ['length', 'l', 'length_in', 'len']));
            $width = $cleanPrice($get($row, ['width', 'w', 'width_in']));
            $height = $cleanPrice($get($row, ['height', 'h', 'height_in', 'thickness', 'thk']));

            if ($length === null && $width === null && $height === null) {
                [$length, $width, $height] = $parseSizeValue($get($row, ['size', 'dimensions', 'dim', 'lwh']));
            }

            if ($price === null) {
                $price = $cleanPrice($get($row, ['mrp', 'rate', 'price']));
            }

            $parsedItems[] = [
                'name' => trim($name),
                'length_in' => $length ?? 0.0,
                'width_in' => $width ?? 0.0,
                'height_in' => $height ?? 0.0,
                'price' => $price ?? 0.0,
                'raw_line' => implode(',', array_map(static fn ($value) => (string) $value, $row)),
            ];
        }

        fclose($fh);

        return $parsedItems;
    }

    /**
     * Parse the raw extracted text from the PDF.
     *
     * @param string $text Raw text from PDF
     * @param int|null $companyId Optional company ID for mapping
     * @return array Structured parsed items
     */
    public function parseText(string $text, ?int $companyId = null): array
    {
        // Normalize line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $text);

        $parsedItems = [];
        $currentHeader = 'Foam Product';

        // Dimension regex matches 3D and 2D sizes, e.g. 72x36x4, 78x72x8, 72 x 36 x 5, 78*72*4, etc.
        // It captures:
        // Group 1: Length (in or ft)
        // Group 2: Width (in or ft)
        // Group 3: Height/Thickness (optional)
        $dimensionRegex = '/\b(\d{1,3}(?:\.\d+)?)\s*[xX*×\s-]\s*(\d{1,3}(?:\.\d+)?)(?:\s*[xX*×\s-]\s*(\d{1,2}(?:\.\d+)?))?\b/';

        // Price regex matches numbers with optional commas and optional Rs prefix/suffix, e.g. Rs 12,500, Rs.12500, 15,000, 8500
        $priceRegex = '/\b(?:Rs\.?\s*)?(\d{1,3}(?:,\d{3})+|\d{3,6})(?:\.\d{2})?\b/i';

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Exclude lines that are clearly page numbers, website names, or typical header metadata
            if (preg_match('/page\s*\d+/i', $line) || preg_match('/www\./i', $line) || preg_match('/price\s*list/i', $line)) {
                continue;
            }

            // Check if this line contains a size dimension
            if (preg_match($dimensionRegex, $line, $dimMatches)) {
                $fullSizeMatch = $dimMatches[0];
                $length = (float)$dimMatches[1];
                $width = (float)$dimMatches[2];
                $height = isset($dimMatches[3]) && $dimMatches[3] !== '' ? (float)$dimMatches[3] : 0.0;

                // Remove size from line so it doesn't interfere with price extraction or product name
                $remainingText = str_replace($fullSizeMatch, ' ', $line);

                // Now find the price in the remaining text
                $price = 0.0;
                if (preg_match($priceRegex, $remainingText, $priceMatches)) {
                    $fullPriceMatch = $priceMatches[0];
                    $priceRaw = $priceMatches[1];
                    $price = (float)str_replace(',', '', $priceRaw);

                    // Remove price from remaining text to isolate the product name
                    $remainingText = str_replace($fullPriceMatch, ' ', $remainingText);
                }

                // Clean up remaining text to get the product name
                // Remove ALL leftover occurrences of dimension patterns (so size repeats like 72 36 6 don't end up in product name)
                $remainingText = preg_replace($dimensionRegex, ' ', $remainingText);

                // Remove hyphens, slashes, extra spaces, Rs prefix
                $remainingText = preg_replace('/[–\-—\/\\:*,]/', ' ', $remainingText);
                $remainingText = preg_replace('/\b(?:Rs|RS)\b/i', ' ', $remainingText);
                $productName = trim(preg_replace('/\s+/', ' ', $remainingText));

                // Deduplicate repeated words (e.g. "MEMORY2IN1 MEMORY2IN1" -> "MEMORY2IN1")
                $productName = self::deduplicateWords($productName);

                // Map standard shortcodes/product codes to friendly names
                $productName = self::mapProductCodeToFriendlyName($productName, $companyId);

                // If product name is empty or extremely short, inherit the last seen header name
                if (empty($productName) || strlen($productName) < 3) {
                    $productName = $currentHeader;
                } else {
                    // Update header since we found a new valid product name on this line
                    // (But only if it's not a generic word like "Mattress" or "Size")
                    $lowerName = strtolower($productName);
                    if ($lowerName !== 'size' && $lowerName !== 'price' && $lowerName !== 'mattress' && $lowerName !== 'rate') {
                        $currentHeader = $productName;
                    }
                }

                // Add to our parsed results if we got a valid price and size
                if ($price > 0 && $length > 0 && $width > 0) {
                    $parsedItems[] = [
                        'name' => $productName,
                        'length_in' => $length,
                        'width_in' => $width,
                        'height_in' => $height,
                        'price' => $price,
                        'raw_line' => $line
                    ];
                }
            } else {
                // No size found. This line might be a product/quality header!
                // If it is long enough, does not contain massive numbers, and contains alphabetic text,
                // we treat it as the current active category/product header.
                $cleanLine = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $line);
                $cleanLine = trim(preg_replace('/\s+/', ' ', $cleanLine));

                // A header shouldn't be too long, shouldn't be empty, and should be mostly letters
                if (strlen($cleanLine) > 4 && strlen($cleanLine) < 50 && !preg_match('/^\d+$/', $cleanLine)) {
                    // Avoid generic lines
                    $lowerLine = strtolower($cleanLine);
                    if (!Str::contains($lowerLine, ['phone', 'tel', 'address', 'retail', 'effective', 'date', 'distributor', 'company', 'shop', 'mian', 'traders'])) {
                        $currentHeader = self::mapProductCodeToFriendlyName(self::deduplicateWords($cleanLine), $companyId);
                    }
                }
            }
        }

        return $parsedItems;
    }

    /**
     * Deduplicate words inside a product name string.
     *
     * @param string $text Original string
     * @return string Word-deduplicated string
     */
    public static function deduplicateWords(string $text): string
    {
        $words = explode(' ', $text);
        $uniqueWords = [];
        foreach ($words as $word) {
            if (is_numeric($word) || !in_array(strtolower($word), array_map('strtolower', $uniqueWords))) {
                $uniqueWords[] = $word;
            }
        }
        return implode(' ', $uniqueWords);
    }

    /**
     * Maps codes/shorthand product names to friendly standard product names.
     *
     * @param string $name Extracted raw product name
     * @param int|null $companyId Selected supplier company context
     * @return string Friendly user product name
     */
    public static function mapProductCodeToFriendlyName(string $name, ?int $companyId = null): string
    {
        $name = trim($name);
        $upper = strtoupper($name);
        
        // Dura Foam specific maps (Company ID 4) or general fallback
        if (
            $upper === 'MEMORY2IN1' || 
            $upper === 'MEM2IN1' ||
            Str::contains($upper, 'MEMORY2IN1') || 
            Str::contains($upper, 'MEMORY 2IN1') || 
            Str::contains($upper, 'MEMORY 2 IN 1') ||
            Str::contains($upper, 'MEM 2IN1') ||
            Str::contains($upper, 'MEM 2 IN 1')
        ) {
            return 'Dura memory 2 in 1';
        }

        // Additional common brands mappings if applicable
        if ($upper === 'MOLTYFOAM' || Str::contains($upper, 'MOLTYFOAM')) {
            return 'Molty Foam';
        }
        
        return $name;
    }
}

