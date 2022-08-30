<?php

use App\Http\Controllers\AdminController;

use App\Http\Controllers\CustomersReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceAttachmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\InvoicesReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
})->name('home');



Route::get('/home', [HomeController::class, 'index']);

require __DIR__.'/auth.php';
Route::middleware('auth')->group(function () {
    Route::resource('invoices', InvoiceController::class);
    Route::resource('sections', SectionController::class);
    Route::resource('products', ProductController::class);
    Route::resource('invoiceAttachment', InvoiceAttachmentController::class);
    Route::get('/section/{id}', [InvoiceController::class, 'getProduct']);
    Route::get('/open/{invoice}/{docId}', [InvoiceAttachmentController::class, 'openDocument']);
    Route::get('/download/{invoice}/{docId}', [InvoiceAttachmentController::class, 'downloadDocument']);
    Route::get('/status/{invoice}', [InvoiceController::class, 'changeStatus']);
    Route::post('/status/{invoice}', [InvoiceController::class, 'createStatus']);
    Route::get('/invoice_paid', [InvoiceController::class, 'getPaidInvoices']);
    Route::get('/invoice_unpaid', [InvoiceController::class, 'getUnpaidInvoices']);
    Route::get('/invoice_partial', [InvoiceController::class, 'getPartialInvoices']);
    Route::delete('/archive/{invoice}', [InvoiceController::class, 'archive']);
    Route::get('/archive', [InvoiceController::class, 'getArchive']);
    Route::get('/print_invoice/{invoice}', [InvoiceController::class, 'print']);
    Route::delete('/restore/{id}', [InvoiceController::class, 'restoreInvoice']);
    Route::delete('/delete', [InvoiceController::class, 'deleteArchive']);
    Route::get('/export/invoices', [InvoiceController::class, 'export']);
    Route::get('/invoices_report', [InvoicesReportController::class, 'index']);
    Route::post('/Search_invoices', [InvoicesReportController::class, 'searchInvoices']);
    Route::get('/customers_report', [CustomersReportController::class, 'index']);
    Route::post('/Search_customers', [CustomersReportController::class, 'searchInvoices']);
    Route::get('/markAllRead', [InvoiceController::class, 'markAllRead']);
    Route::get('/markRead/{id}', [InvoiceController::class, 'markRead']);
});

Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles',RoleController::class);
    Route::resource('users',UserController::class);
});




Route::get('/{page}', [AdminController::class ,'index'])->middleware('auth');


