<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoicesReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function searchInvoices(Request $request)
    {
        if ($request->rdio == 1) {
            if ($request->type && $request->start_at == '' && $request->end_at == '') {
                $invoices = Invoice::where('value_status', '=', (int)$request->type)->get();
                $type = $request->type;

                return view('reports.index', compact('type', 'invoices'));
            } else {
                $start_at = date($request->start_at);
                $end_at = date($request->end_at);
                $invoices = Invoice::whereBetween('invoice_Date', [$start_at, $end_at])->where('value_status', '=', (int)$request->type)->get();
                $type = $request->type;
                return view('reports.index', compact('type', 'invoices', 'start_at', 'end_at'));
            }


        } else {
            $invoices = Invoice::where('invoice_number', $request->invoice_number)->get();
            return view('reports.index', ['invoices' => $invoices]);
        }
    }
}
