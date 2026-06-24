<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategorySizeController;
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
use App\Http\Controllers\ShopSwitchController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SheetDesignController;
use App\Http\Controllers\CompanyBrochureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemResetController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/r/{sale}', [SalesController::class, 'publicReceipt'])
    ->middleware('signed')
    ->name('mt.sales.receipt');

Route::middleware(['auth','activeShop'])->group(function () {

    // Switch active shop
    Route::post('/shops/switch', [ShopSwitchController::class, 'switch'])->name('mt.shops.switch');

    // Dashboard (sticky sidebar on all pages)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/mt/dashboard', [DashboardController::class, 'index'])->name('mt.dashboard');

    // Analytics
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('mt.analytics.index');

    // POS
    Route::get('/pos', [PosController::class, 'index'])->name('mt.pos.index');
    Route::get('/pos/suggest', [PosController::class, 'suggest'])->name('mt.pos.suggest');
    Route::get('/pos/products', [PosController::class, 'products'])->name('mt.pos.products');


    Route::post('/pos/add', [PosController::class, 'add'])->name('mt.pos.add');
    Route::post('/pos/add-cut', [PosController::class, 'addCut'])->name('mt.pos.add_cut');

    // Leftover tab actions
    Route::post('/pos/leftover/add-cut', [PosController::class, 'addLeftoverCut'])->name('mt.pos.leftover_add_cut');
    Route::post('/pos/leftover/add-full', [PosController::class, 'addLeftoverFull'])->name('mt.pos.leftover_add_full');

    // Live cart update
    Route::post('/pos/update-item', [PosController::class, 'updateItem'])->name('mt.pos.update_item');
    Route::post('/pos/update-overall-discount', [PosController::class, 'updateOverallDiscount'])->name('mt.pos.update_overall_discount');

    Route::post('/pos/remove', [PosController::class, 'remove'])->name('mt.pos.remove');
    Route::post('/pos/clear', [PosController::class, 'clear'])->name('mt.pos.clear');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('mt.pos.checkout');

    // Products (management pages - all non-resource routes MUST come before resource definition)
    Route::get('/products/import', [ProductController::class, 'importForm'])->name('mt.products.import_form');
    Route::post('/products/import', [ProductController::class, 'importProcess'])->name('mt.products.import_process');
    Route::get('/products/import/template', [ProductController::class, 'downloadImportTemplate'])->name('mt.products.import_template');
    // Price import (supports supplier CSVs with product_key,length,width,height,type,price)
    Route::get('/products/price-import', [\App\Http\Controllers\PriceImportController::class, 'showForm'])->name('mt.products.price_import_form');
    Route::post('/products/price-import', [\App\Http\Controllers\PriceImportController::class, 'import'])->name('mt.products.price_import_process');
    // PDF price import
    Route::get('/products/price-import-pdf', [\App\Http\Controllers\PriceImportController::class, 'showPdfForm'])->name('mt.products.price_import_pdf_form');
    Route::post('/products/price-import-pdf', [\App\Http\Controllers\PriceImportController::class, 'importPdf'])->name('mt.products.price_import_pdf_process');
    Route::post('/products/price-import-pdf-confirm', [\App\Http\Controllers\PriceImportController::class, 'confirmPdfImport'])->name('mt.products.price_import_pdf_confirm');
    // Bulk pricing
    Route::get('/products/bulk-price', [ProductController::class, 'bulkPrice'])->name('mt.products.bulk_price');
    Route::post('/products/bulk-price-save', [ProductController::class, 'bulkPriceSave'])->name('mt.products.bulk_price_save');
    // Bulk operations
    Route::post('/products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('mt.products.bulk_destroy');
    Route::post('/products/bulk-delete-by-keyword', [ProductController::class, 'bulkDestroyByKeyword'])->name('mt.products.bulk_destroy_by_keyword');
    Route::get('/products/count-by-keyword', [ProductController::class, 'countByKeyword'])->name('mt.products.count_by_keyword');
    Route::post('/products/bulk-clear-sizes', [ProductController::class, 'bulkClearSizes'])->name('mt.products.bulk_clear_sizes');
    Route::post('/products/bulk-restore', [ProductController::class, 'bulkRestore'])->name('mt.products.bulk_restore');
    Route::post('/products/bulk-force-delete', [ProductController::class, 'bulkForceDestroy'])->name('mt.products.bulk_force_destroy');
    // Recycle bin / trashed products
    Route::get('/products/trash', [ProductController::class, 'trash'])->name('mt.products.trash');
    // Resource route (MUST come last)
    Route::resource('products', ProductController::class)->names('mt.products');
    // Per-product operations
    Route::post('/products/{product}/restore', [ProductController::class, 'restore'])->name('mt.products.restore');
    Route::post('/products/{product}/force-delete', [ProductController::class, 'forceDestroy'])->name('mt.products.force_destroy');
    Route::patch('/products/{product}/quick-update', [ProductController::class, 'quickUpdate'])->name('mt.products.quick_update');

    // Categories
    Route::resource('categories', CategoryController::class)->names('mt.categories');
    Route::get('/categories/{category}/confirm-delete', [CategoryController::class, 'confirmDelete'])->name('mt.categories.confirm_delete');
    Route::post('/categories/{category}/reassign-and-delete', [CategoryController::class, 'reassignAndDelete'])->name('mt.categories.reassign_and_delete');
    
    // Category Sizes
    Route::get('/categories/{category}/sizes', [CategorySizeController::class, 'index'])->name('mt.categories.sizes');
    Route::post('/categories/{category}/sizes', [CategorySizeController::class, 'store'])->name('mt.categories.sizes.store');
    Route::delete('/categories/{category}/sizes/selected', [CategorySizeController::class, 'destroySelected'])->name('mt.categories.sizes.destroy_selected');
    Route::delete('/categories/{category}/sizes', [CategorySizeController::class, 'destroyAll'])->name('mt.categories.sizes.destroy_all');
    Route::delete('/categories/{category}/sizes/{size}', [CategorySizeController::class, 'destroy'])->name('mt.categories.sizes.destroy');

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
    Route::get('/company-orders/products', [CompanyOrderController::class, 'getCompanyProducts'])->name('mt.company_orders.products');
    Route::get('/company-orders/{order}', [CompanyOrderController::class, 'show'])->name('mt.company_orders.show');
    Route::get('/company-orders/{order}/pdf', [CompanyOrderController::class, 'pdf'])->name('mt.company_orders.pdf');
    Route::post('/company-orders/{order}/receive', [CompanyOrderController::class, 'receive'])->name('mt.company_orders.receive');
    Route::post('/company-orders/{order}/payments', [CompanyOrderController::class, 'recordPayment'])->name('mt.company_orders.payments');
    Route::delete('/company-orders/{order}', [CompanyOrderController::class, 'destroy'])->name('mt.company_orders.destroy');
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

    // Sheet Designs
    Route::get('/sheet-designs/bulk', [SheetDesignController::class, 'bulkForm'])->name('mt.sheet_designs.bulk_form');
    Route::post('/sheet-designs/bulk', [SheetDesignController::class, 'bulkProcess'])->name('mt.sheet_designs.bulk_process');
    Route::resource('sheet-designs', SheetDesignController::class)->names('mt.sheet_designs');

    // Company Brochures
    Route::get('/company-brochures', [CompanyBrochureController::class, 'index'])->name('mt.company_brochures.index');
    Route::post('/company-brochures', [CompanyBrochureController::class, 'store'])->name('mt.company_brochures.store');
    Route::get('/company-brochures/file/{brochure}', [CompanyBrochureController::class, 'showFile'])->name('mt.company_brochures.file');
    Route::delete('/company-brochures/{brochure}', [CompanyBrochureController::class, 'destroy'])->name('mt.company_brochures.destroy');

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

    // Shops (Admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/shops', [ShopController::class, 'index'])->name('mt.shops.index');
        Route::get('/shops/create', [ShopController::class, 'create'])->name('mt.shops.create');
        Route::post('/shops', [ShopController::class, 'store'])->name('mt.shops.store');
        Route::get('/shops/{shop}/edit', [ShopController::class, 'edit'])->name('mt.shops.edit');
        Route::put('/shops/{shop}', [ShopController::class, 'update'])->name('mt.shops.update');
        // Users (Admin)
        Route::get('/users', [UserController::class, 'index'])->name('mt.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('mt.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('mt.users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('mt.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('mt.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('mt.users.destroy');
        Route::get('/system/reset', [SystemResetController::class, 'index'])->name('mt.system_reset.index');
        Route::post('/system/reset', [SystemResetController::class, 'store'])->name('mt.system_reset.store');
        Route::get('/system/reset/backups/{filename}', [SystemResetController::class, 'download'])->name('mt.system_reset.download');
        });

    // sms/whatsapp/pdf routes are available to all authenticated shop users, not just admins
    Route::post('/sales/{sale}/sms', [\App\Http\Controllers\SaleSmsController::class, 'send'])
        ->name('mt.sales.sms')
        ->middleware(['auth','activeShop']);
    Route::get('/sales/{sale}/whatsapp', [\App\Http\Controllers\SaleWhatsappController::class, 'send'])
        ->name('mt.sales.whatsapp')
        ->middleware(['auth','activeShop']);
    Route::get('/sales/{sale}/pdf', [\App\Http\Controllers\SalePdfController::class, 'download'])
        ->name('mt.sales.pdf')
        ->middleware(['auth','activeShop']);

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
