<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Support\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WhatsAppListController extends Controller
{
    /**
     * Build base query for WhatsApp list.
     *
     * $type: udhar|walkin|all
     */
    private function buildQuery(string $type, string $q)
    {
        $type = trim($type);
        if (!in_array($type, ['udhar', 'walkin', 'all'], true)) {
            $type = 'udhar';
        }

        $q = trim($q);

        $query = Sale::query()
            ->selectRaw('
                customer_phone,
                MAX(customer_name) as customer_name,
                MAX(customer_phone2) as customer_phone2,
                MAX(customer_phone3) as customer_phone3,
                SUM(COALESCE(total_amount,0)) as total_sales,
                SUM(COALESCE(balance_amount,0)) as total_udhar,
                MAX(created_at) as last_purchase
            ')
            ->whereNotNull('customer_phone')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('customer_phone', 'like', "%{$q}%")
                        ->orWhere('customer_phone2', 'like', "%{$q}%")
                        ->orWhere('customer_phone3', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%");
                });
            })
            ->groupBy('customer_phone');

        if ($type === 'udhar') {
            $query->havingRaw('SUM(COALESCE(balance_amount,0)) > 0');
        } elseif ($type === 'walkin') {
            $query->havingRaw('SUM(COALESCE(balance_amount,0)) <= 0');
        }

        return $query;
    }

    public function index(Request $request)
    {
        $type = (string)$request->get('type', 'udhar');
        $q = (string)$request->get('q', '');

        $base = $this->buildQuery($type, $q);

        // Display table (paginated)
        $rows = (clone $base)
            ->orderByDesc(DB::raw('MAX(created_at)'))
            ->paginate(50)
            ->withQueryString();

        // Lists (all matching, capped to keep page fast)
        $listRows = (clone $base)
            ->orderByDesc(DB::raw('MAX(created_at)'))
            ->limit(5000)
            ->get();

        $phones = [];
        foreach ($listRows as $r) {
            foreach ([$r->customer_phone, $r->customer_phone2, $r->customer_phone3] as $p) {
                $p = trim((string)$p);
                if ($p !== '') $phones[] = $p;
            }
        }

        $phones = array_values(array_unique($phones));

        $waPhones = [];
        foreach ($phones as $p) {
            $n = WhatsApp::toNumber($p);
            if ($n !== '') $waPhones[] = $n;
        }
        $waPhones = array_values(array_unique($waPhones));

        $numbersOriginal = implode("\n", $phones);
        $numbersWa = implode("\n", $waPhones);

        $typeSafe = in_array($type, ['udhar', 'walkin', 'all'], true) ? $type : 'udhar';

        return view('mt.whatsapp.index', [
            'rows' => $rows,
            'q' => $q,
            'type' => $typeSafe,
            'numbersOriginal' => $numbersOriginal,
            'numbersWa' => $numbersWa,
            'totalCount' => $listRows->count(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $type = (string)$request->get('type', 'udhar');
        $q = (string)$request->get('q', '');

        $rows = $this->buildQuery($type, $q)
            ->orderByDesc(DB::raw('MAX(created_at)'))
            ->limit(10000)
            ->get();

        $filename = 'whatsapp_numbers_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Name',
                'Phone 1',
                'Phone 2',
                'Phone 3',
                'WhatsApp 1',
                'WhatsApp 2',
                'WhatsApp 3',
                'Total Sales',
                'Total Udhar',
                'Last Purchase',
            ]);

            foreach ($rows as $r) {
                $p1 = trim((string)$r->customer_phone);
                $p2 = trim((string)$r->customer_phone2);
                $p3 = trim((string)$r->customer_phone3);

                fputcsv($out, [
                    $r->customer_name ?? '',
                    $p1,
                    $p2,
                    $p3,
                    WhatsApp::toNumber($p1),
                    WhatsApp::toNumber($p2),
                    WhatsApp::toNumber($p3),
                    (float)($r->total_sales ?? 0),
                    (float)($r->total_udhar ?? 0),
                    (string)($r->last_purchase ?? ''),
                ]);
            }

            fclose($out);
        }, 200, $headers);
    }
}
