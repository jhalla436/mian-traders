<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyContactController;
use App\Http\Controllers\CompanyGroupController;
use App\Http\Controllers\DiscountTypeController;
use App\Http\Controllers\DiscountRuleController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CompanyLedgerController;
use App\Http\Controllers\CompanyOrderController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\RecurringExpenseController;
use App\Http\Controllers\UdharController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\WhatsAppListController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard (sticky sidebar on all pages)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/mt/dashboard', [DashboardController::class, 'index'])->name('mt.dashboard');

    // POS
    Route::get('/pos', [PosController::class, 'index'])->name('mt.pos.index');
    Route::get('/pos/suggest', [PosController::class, 'suggest'])->name('mt.pos.suggest');

    Route::post('/pos/add', [PosController::class, 'add'])->name('mt.pos.add');
    Route::post('/pos/add-cut', [PosController::class, 'addCut'])->name('mt.pos.add_cut');

    // Leftover tab actions
    Route::post('/pos/leftover/add-cut', [PosController::class, 'addLeftoverCut'])->name('mt.pos.leftover_add_cut');
    Route::post('/pos/leftover/add-full', [PosController::class, 'addLeftoverFull'])->name('mt.pos.leftover_add_full');

    // Live cart update
    Route::post('/pos/update-item', [PosController::class, 'updateItem'])->name('mt.pos.update_item');

    Route::post('/pos/remove', [PosController::class, 'remove'])->name('mt.pos.remove');
    Route::post('/pos/clear', [PosController::class, 'clear'])->name('mt.pos.clear');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('mt.pos.checkout');

    // Products (management pages)
    Route::get('/products/import', [ProductController::class, 'importForm'])->name('mt.products.import_form');
    Route::post('/products/import', [ProductController::class, 'importProcess'])->name('mt.products.import_process');
    Route::get('/products/import/template', [ProductController::class, 'downloadImportTemplate'])->name('mt.products.import_template');
    Route::resource('products', ProductController::class)->names('mt.products');

    // Categories
    Route::resource('categories', CategoryController::class)->names('mt.categories');

    // Companies
    Route::resource('companies', CompanyController::class)->names('mt.companies');

    // Company Contacts (multiple contacts per company)
    Route::get('/companies/{company}/contacts', [CompanyContactController::class, 'index'])->name('mt.company_contacts.index');
    Route::get('/companies/{company}/contacts/create', [CompanyContactController::class, 'create'])->name('mt.company_contacts.create');
    Route::post('/companies/{company}/contacts', [CompanyContactController::class, 'store'])->name('mt.company_contacts.store');
    Route::get('/companies/{company}/contacts/{contact}/edit', [CompanyContactController::class, 'edit'])->name('mt.company_contacts.edit');
    Route::put('/companies/{company}/contacts/{contact}', [CompanyContactController::class, 'update'])->name('mt.company_contacts.update');
    Route::delete('/companies/{company}/contacts/{contact}', [CompanyContactController::class, 'destroy'])->name('mt.company_contacts.destroy');

    // Company Ledger (payable/credit with purchases, payments, transport etc.)
    Route::get('/companies/{company}/ledger', [CompanyLedgerController::class, 'index'])->name('mt.company_ledger.index');
    Route::post('/companies/{company}/ledger', [CompanyLedgerController::class, 'store'])->name('mt.company_ledger.store');
    Route::delete('/companies/{company}/ledger/{entry}', [CompanyLedgerController::class, 'destroy'])->name('mt.company_ledger.destroy');

    // Purchases = Stock In + Company Ledger
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('mt.purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('mt.purchases.create');
    Route::get('/purchases/calc-cost', [PurchaseController::class, 'calcCost'])->name('mt.purchases.calc_cost');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('mt.purchases.store');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('mt.purchases.show');

    // Company Orders (estimated payable, later receive to stock-in)
    Route::get('/company-orders', [CompanyOrderController::class, 'index'])->name('mt.company_orders.index');
    Route::get('/company-orders/create', [CompanyOrderController::class, 'create'])->name('mt.company_orders.create');
    Route::post('/company-orders', [CompanyOrderController::class, 'store'])->name('mt.company_orders.store');
    Route::get('/company-orders/{order}', [CompanyOrderController::class, 'show'])->name('mt.company_orders.show');
    Route::post('/company-orders/{order}/receive', [CompanyOrderController::class, 'receive'])->name('mt.company_orders.receive');
    Route::get('/company-orders/calc-cost', [CompanyOrderController::class, 'calcCost'])->name('mt.company_orders.calc_cost');

    // Expenses
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('mt.expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('mt.expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('mt.expenses.store');

    // Recurring Expenses (e.g. Shop Rent on 21st)
    Route::get('/recurring-expenses', [RecurringExpenseController::class, 'index'])->name('mt.recurring_expenses.index');
    Route::post('/recurring-expenses', [RecurringExpenseController::class, 'store'])->name('mt.recurring_expenses.store');
    Route::put('/recurring-expenses/{recurringExpense}', [RecurringExpenseController::class, 'update'])->name('mt.recurring_expenses.update');
    Route::delete('/recurring-expenses/{recurringExpense}', [RecurringExpenseController::class, 'destroy'])->name('mt.recurring_expenses.destroy');

    // Company Groups
    Route::resource('company-groups', CompanyGroupController::class)->names('mt.company_groups');

    // Discount Types / Rules
    Route::resource('discount-types', DiscountTypeController::class)->names('mt.discount_types');
    Route::resource('discount-rules', DiscountRuleController::class)->names('mt.discount_rules');

    // Sales
    Route::get('/sales', [SalesController::class, 'index'])->name('mt.sales.index');
    Route::get('/sales/{sale}', [SalesController::class, 'show'])->name('mt.sales.show');

    // Payments
    Route::post('/payments', [PaymentController::class, 'store'])->name('mt.payments.store');

    // Stock Movements
    Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('mt.stock_movements.index');
    Route::get('/stock-movements/create', [StockMovementController::class, 'create'])->name('mt.stock_movements.create');
    Route::post('/stock-movements', [StockMovementController::class, 'store'])->name('mt.stock_movements.store');

    // Udhar
    Route::get('/udhar', [UdharController::class, 'index'])->name('mt.udhar.index');
    Route::get('/udhar/{phone}', [UdharController::class, 'show'])->name('mt.udhar.show');
    Route::post('/udhar/pay-total/{phone}', [UdharController::class, 'payTotal'])->name('mt.udhar.pay_total');
    Route::post('/udhar/pay/{sale}', [UdharController::class, 'payBill'])->name('mt.udhar.pay_bill');

    // Customer Profile
    Route::get('/customers/profile/{phone}', [CustomerProfileController::class, 'show'])->name('mt.customers.profile');

    // WhatsApp number lists (udhar / walk-in / all)
    Route::get('/whatsapp', [WhatsAppListController::class, 'index'])->name('mt.whatsapp.index');
    Route::get('/whatsapp/export', [WhatsAppListController::class, 'export'])->name('mt.whatsapp.export');
});

require __DIR__.'/auth.php';
