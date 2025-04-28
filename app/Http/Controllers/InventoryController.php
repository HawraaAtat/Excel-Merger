<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController extends Controller
{
    public function showForm()
    {
        return view('inventory.form');
    }

    public function process(Request $request)
    {
        $request->validate([
            'inventory_file' => 'required|mimes:csv,txt',
            'stock_file' => 'required|mimes:xlsx,xls',
        ]);

        $inventoryFile = $request->file('inventory_file');
        $stockFile = $request->file('stock_file');

        $errors = [
            'missing_skus' => [],
            'duplicate_skus' => [],
            'invalid_quantities' => [],
        ];

        // Load inventory CSV
        $inventory = collect(array_map('str_getcsv', file($inventoryFile->getRealPath())));
        $headers = array_map('trim', $inventory->shift());
        $inventory = $inventory->map(fn($row) => array_combine($headers, array_map('trim', $row)));

        // Load stock Excel
        $spreadsheet = IOFactory::load($stockFile->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $stock = collect($sheet->toArray(null, true, true, true));
        $stockHeaders = array_map('trim', $stock->shift());
        $stock = $stock->map(fn($row) => array_combine($stockHeaders, array_map('trim', $row)));

        $stockLookup = $stock->keyBy(fn($row) => strtolower($row['StockNumber']));

        foreach ($inventory as &$row) {
            $sku = strtolower($row['SKU'] ?? '');

            if (!$sku) {
                $errors['missing_skus'][] = $row;
                continue;
            }

            if (isset($stockLookup[$sku])) {
                $row['Available'] = $this->sanitizeQuantity($stockLookup[$sku]['Quantity']);
            }

            $row['Committed'] = $this->sanitizeQuantity($row['Committed'] ?? 0);
            $row['On Hand'] = $row['Available'] + $row['Committed'];

            if ($row['On Hand'] < -1000000000 || $row['On Hand'] > 1000000000) {
                $errors['invalid_quantities'][] = ['SKU' => $sku, 'value' => $row['On Hand']];
            }
        }

        return response()->streamDownload(function () use ($inventory, $headers) {
            echo $this->generateCsv($inventory, $headers);
        }, 'updated_inventory.csv');

    }

    private function sanitizeQuantity($value)
    {
        // Ensure quantity is a whole number within valid range
        if (!is_numeric($value)) {
            return 0;
        }
    
        $value = (int) $value;
    
        if ($value < -1000000000) {
            return -1000000000;
        }
    
        if ($value > 1000000000) {
            return 1000000000;
        }
    
        return $value;
    }
    
    private function generateCsv($rows, $headers)
    {
        $stream = fopen('php://temp', 'r+');
    
        // Set UTF-8 BOM to ensure proper encoding in CSV
        fwrite($stream, "\xEF\xBB\xBF");

        fputcsv($stream, $headers);
    
        foreach ($rows as $row) {
            $data = [];
            foreach ($headers as $header) {
                $data[] = $row[$header] ?? '';
            }
            fputcsv($stream, $data);
        }
    
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);
    
        return $csv;
    }
}
