<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SystemResetController extends Controller
{
    private const CONFIRM_TEXT = 'RESET';

    private array $groups = [
        'sales' => [
            'label' => 'Sales, invoices, payments and udhar',
            'description' => 'Deletes sales history, sale items, sale payments, customer payments and udhar balances.',
            'tables' => ['sale_payments', 'payments', 'sale_items', 'sales'],
        ],
        'customers' => [
            'label' => 'Customers',
            'description' => 'Deletes customer profiles and phone ledger records.',
            'tables' => ['customers'],
        ],
        'stock' => [
            'label' => 'Stock movements, leftovers and shop stock',
            'description' => 'Deletes movement history and leftovers, then clears shop/product stock quantities.',
            'tables' => ['stock_movements', 'leftover_pieces'],
            'clear_stock' => true,
        ],
        'purchases' => [
            'label' => 'Purchases, company orders and company ledger',
            'description' => 'Deletes purchase records, pending company orders and supplier ledger entries.',
            'tables' => ['company_order_items', 'company_orders', 'purchase_items', 'purchases', 'company_ledger_entries'],
        ],
        'expenses' => [
            'label' => 'Expenses and recurring expenses',
            'description' => 'Deletes expense history and recurring expense rules.',
            'tables' => ['expenses', 'recurring_expenses'],
        ],
        'catalog' => [
            'label' => 'Product catalog, categories and discount setup',
            'description' => 'Deletes products, categories, sizes, shop prices, variants and discount rules/types. Use this only when replacing dummy catalog data.',
            'tables' => [
                'sale_payments',
                'payments',
                'sale_items',
                'sales',
                'stock_movements',
                'leftover_pieces',
                'company_order_items',
                'company_orders',
                'purchase_items',
                'purchases',
                'company_ledger_entries',
                'shop_products',
                'product_variants',
                'products',
                'category_sizes',
                'discount_rules',
                'discount_types',
                'categories',
            ],
        ],
        'companies' => [
            'label' => 'Companies, contacts and company groups',
            'description' => 'Deletes supplier/company setup. Product catalog and purchase/order data should also be reset if they depend on these companies.',
            'tables' => ['company_contacts', 'company_groups', 'companies'],
        ],
    ];

    private array $deleteOrder = [
        'sale_payments',
        'payments',
        'sale_items',
        'sales',
        'stock_movements',
        'leftover_pieces',
        'company_order_items',
        'company_orders',
        'purchase_items',
        'purchases',
        'company_ledger_entries',
        'expenses',
        'recurring_expenses',
        'shop_products',
        'product_variants',
        'products',
        'category_sizes',
        'discount_rules',
        'discount_types',
        'company_contacts',
        'company_groups',
        'categories',
        'companies',
        'customers',
    ];

    private array $preservedBackupTables = [
        'shops',
        'users',
        'user_shops',
    ];

    public function index()
    {
        $groups = $this->groupsWithCounts();
        $backups = $this->backupFiles();

        return view('mt.system_reset.index', compact('groups', 'backups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'groups' => ['required', 'array', 'min:1'],
            'groups.*' => ['string'],
            'confirm_text' => ['required', 'string'],
        ]);

        if (trim($data['confirm_text']) !== self::CONFIRM_TEXT) {
            return back()->with('error', 'Type RESET exactly to confirm.')->withInput();
        }

        $selectedGroups = array_values(array_intersect($data['groups'], array_keys($this->groups)));
        if (empty($selectedGroups)) {
            return back()->with('error', 'Select at least one reset section.')->withInput();
        }

        $tables = $this->tablesForGroups($selectedGroups);
        $backup = $this->writeBackup($selectedGroups, $tables);

        DB::transaction(function () use ($selectedGroups, $tables) {
            $this->withoutForeignKeyChecks(function () use ($tables) {
                foreach ($this->orderedTables($tables) as $table) {
                    if (Schema::hasTable($table)) {
                        DB::table($table)->delete();
                        $this->resetAutoIncrement($table);
                    }
                }
            });

            if (in_array('stock', $selectedGroups, true)) {
                $this->clearStockQuantities();
            }
        });

        session()->forget(['mt_cart', 'mt_cart_overall_discount']);

        $sectionCount = count($selectedGroups);
        return redirect()
            ->route('mt.system_reset.index')
            ->with('success', "Backup saved, then {$sectionCount} reset section(s) were cleared. Backup: {$backup['filename']}");
    }

    public function download(string $filename)
    {
        $safeName = basename($filename);
        $path = $this->backupDirectory() . DIRECTORY_SEPARATOR . $safeName;

        abort_unless(File::exists($path), 404);

        return response()->download($path);
    }

    private function groupsWithCounts(): array
    {
        $groups = [];
        foreach ($this->groups as $key => $group) {
            $count = 0;
            foreach ($group['tables'] as $table) {
                if (Schema::hasTable($table)) {
                    $count += DB::table($table)->count();
                }
            }
            if (!empty($group['clear_stock'])) {
                if (Schema::hasTable('shop_products')) {
                    $count += DB::table('shop_products')->where(function ($query) {
                        $query->where('stock_qty', '!=', 0)->orWhere('avg_cost', '!=', 0);
                    })->count();
                }
                if (Schema::hasTable('products')) {
                    $count += DB::table('products')->where('stock_qty', '!=', 0)->count();
                }
            }

            $groups[$key] = $group + ['key' => $key, 'count' => $count];
        }

        return $groups;
    }

    private function tablesForGroups(array $groupKeys): array
    {
        $tables = [];
        foreach ($groupKeys as $key) {
            foreach ($this->groups[$key]['tables'] ?? [] as $table) {
                $tables[] = $table;
            }
        }

        return array_values(array_unique($tables));
    }

    private function orderedTables(array $tables): array
    {
        $known = array_values(array_intersect($this->deleteOrder, $tables));
        $extra = array_values(array_diff($tables, $known));

        return array_merge($known, $extra);
    }

    private function writeBackup(array $selectedGroups, array $tables): array
    {
        $directory = $this->backupDirectory();
        File::ensureDirectoryExists($directory);

        $payload = [
            'created_at' => now()->toDateTimeString(),
            'selected_groups' => $selectedGroups,
            'database_connection' => config('database.default'),
            'tables' => [],
        ];

        $backupTables = array_values(array_unique(array_merge($this->deleteOrder, $this->preservedBackupTables, $tables)));

        foreach ($this->orderedTables($backupTables) as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $payload['tables'][$table] = [
                'columns' => Schema::getColumnListing($table),
                'rows' => DB::table($table)->get()->map(fn ($row) => (array) $row)->all(),
            ];
        }

        if (in_array('stock', $selectedGroups, true)) {
            foreach (['shop_products', 'products'] as $table) {
                if (Schema::hasTable($table) && !isset($payload['tables'][$table])) {
                    $payload['tables'][$table] = [
                        'columns' => Schema::getColumnListing($table),
                        'rows' => DB::table($table)->get()->map(fn ($row) => (array) $row)->all(),
                    ];
                }
            }
        }

        $filename = 'reset_backup_' . now()->format('Ymd_His') . '_' . Str::random(6) . '.json';
        $path = $directory . DIRECTORY_SEPARATOR . $filename;

        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return ['filename' => $filename, 'path' => $path];
    }

    private function backupFiles(): array
    {
        $directory = $this->backupDirectory();
        if (!File::isDirectory($directory)) {
            return [];
        }

        return collect(File::files($directory))
            ->filter(fn ($file) => Str::endsWith($file->getFilename(), '.json'))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->take(12)
            ->map(fn ($file) => [
                'filename' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'modified' => date('Y-m-d H:i:s', $file->getMTime()),
            ])
            ->values()
            ->all();
    }

    private function clearStockQuantities(): void
    {
        if (Schema::hasTable('shop_products')) {
            $updates = [];
            foreach (['stock_qty', 'avg_cost', 'low_stock_alert_qty'] as $column) {
                if (Schema::hasColumn('shop_products', $column)) {
                    $updates[$column] = 0;
                }
            }
            if (!empty($updates)) {
                DB::table('shop_products')->update($updates);
            }
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'stock_qty')) {
            DB::table('products')->update(['stock_qty' => 0]);
        }
    }

    private function withoutForeignKeyChecks(callable $callback): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            try {
                $callback();
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
            return;
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            try {
                $callback();
            } finally {
                DB::statement('PRAGMA foreign_keys = ON');
            }
            return;
        }

        $callback();
    }

    private function resetAutoIncrement(string $table): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
            return;
        }

        if ($driver === 'sqlite' && Schema::hasTable('sqlite_sequence')) {
            DB::table('sqlite_sequence')->where('name', $table)->delete();
        }
    }

    private function backupDirectory(): string
    {
        return storage_path('app/backups/resets');
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
