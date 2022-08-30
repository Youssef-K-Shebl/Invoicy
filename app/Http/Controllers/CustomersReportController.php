<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomersReportController extends Controller
{
    public function index()
    {
        return view('reports.customer_report', ['sections' => Section::all()]);
    }

    public function searchInvoices(Request $request)
    {
//        ddd($request);
        $sections = Section::all();
        if ($request->section && $request->product && $request->start_at == '' && $request->end_at == '') {
            $invoices = Invoice::where('section_id', '=', (int)$request->section)->where('product_id', '=', (int)$request->product)->get();
//            ddd($invoices);
            $section_id = $request->section;
            $product_id = $request->product;
            $product_name = $invoices[0]->product->product_name;
            return view('reports.customer_report', compact('section_id', 'product_id', 'invoices', 'sections','product_name'));
        } else {
            $start_at = date($request->start_at);
            $end_at = date($request->end_at);
            $invoices = Invoice::whereBetween('invoice_Date', [$start_at, $end_at])->where('section_id', '=', (int)$request->section)->where('product_id', '=', (int)$request->product)->get();
            $section_id = $request->section;
            $product_id = $request->product;
            $product_name = $invoices[0]->product->product_name;
            return view('reports.customer_report', compact('section_id', 'product_id','invoices', 'start_at', 'end_at', 'sections','product_name'));
        }
    }
}
