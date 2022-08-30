<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use function PHPUnit\Framework\isEmpty;

class InvoiceAttachmentController extends Controller
{

    function __construct()
    {

        $this->middleware('permission:اضافة مرفق', ['only' => ['store']]);
        $this->middleware('permission:حذف المرفق', ['only' => ['destroy']]);
        $this->middleware('permission:تعديل صلاحية', ['only' => ['edit','update']]);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'file_name' => ['required', 'mimes:pdf,jpeg,jpg,png', 'max:10000'],
            'invoice_number' => ['required', Rule::exists('invoices', 'invoice_number')],
            'invoice_id' => ['required', Rule::exists('invoices', 'id')]
        ]);

        $attributes['file_name'] = $request->file('file_name')->store('attachments/'.$request->invoice_number);
        $attributes['created_by'] = auth()->user()->email;
//        ddd($attributes);
        InvoiceAttachment::create($attributes);
        return redirect()->back()->with('Add', 'تم اضافة المرفق بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InvoiceAttachment  $invoiceAttachment
     * @return \Illuminate\Http\Response
     */
    public function show(InvoiceAttachment $invoiceAttachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InvoiceAttachment  $invoiceAttachment
     * @return \Illuminate\Http\Response
     */
    public function edit(InvoiceAttachment $invoiceAttachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InvoiceAttachment  $invoiceAttachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InvoiceAttachment $invoiceAttachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param InvoiceAttachment $invoiceAttachment
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(InvoiceAttachment $invoiceAttachment)
    {

//        ddd($invoiceAttachment);

        $delete = Storage::disk('public')->delete($invoiceAttachment->file_name);


        $invoiceAttachment->delete();

        return redirect()->back()->with('Delete' ,'Attachment Deleted Successfully');
    }

    public function openDocument(Invoice $invoice, int $docId)
    {

        $document = Storage::disk('documents')->getDriver()->getAdapter()->applyPathPrefix($invoice->invoiceAttachments->where('id', $docId)[0]->file_name);
        return response()->file($document);
    }

    public function downloadDocument(Invoice $invoice, int $docId)
    {

        $document = Storage::disk('documents')->getDriver()->getAdapter()->applyPathPrefix($invoice->invoiceAttachments->where('id', $docId)[0]->file_name);
        return response()->download($document);
    }
}
