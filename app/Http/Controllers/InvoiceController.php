<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Models\invoice;
use App\Models\InvoiceAttachment;
use App\Models\InvoicesDetails;
use App\Models\Section;
use App\Models\User;
use App\Notifications\AddInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;


class InvoiceController extends Controller
{

    function __construct()
    {

        $this->middleware('permission:الفواتير|قائمة الفواتير', ['only' => ['index']]);
        $this->middleware('permission:اضافة فاتورة', ['only' => ['create','store']]);
        $this->middleware('permission:تعديل الفاتورة', ['only' => ['edit','update']]);
        $this->middleware('permission:حذف الفاتورة', ['only' => ['destroy']]);
        $this->middleware('permission:تغير حالة الدفع', ['only' => ['changeStatus','createStatus']]);
        $this->middleware('permission:الفواتير المدفوعة', ['only' => ['getPaidInvoices']]);
        $this->middleware('permission:الفواتير الغير مدفوعة', ['only' => ['getUnpaidInvoices']]);
        $this->middleware('permission:الفواتير المدفوعة جزئيا', ['only' => ['getPartialInvoices']]);
        $this->middleware('permission:ارشيف الفواتير', ['only' => ['getArchive']]);
        $this->middleware('permission:ارشفة الفاتورة', ['only' => ['deleteArchive']]);
        $this->middleware('permission:طباعةالفاتورة', ['only' => ['print']]);
        $this->middleware('permission:تصدير EXCEL', ['only' => ['export']]);
        $this->middleware('permission:تغير حالة الدفع', ['only' => ['restoreInvoice']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        return view('invoices.invoices', ['invoices' => Invoice::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('invoices.create', ['sections' => Section::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

//        ddd($request->all());

        $attributes = $request->validate([
            'invoice_number' => ['required'] ,
            'invoice_date' => 'required',
            'section_id' => ['required', Rule::exists('sections', 'id')],
            'due_date' => 'required',
            'product_id' => ['required', Rule::exists('products', 'id')],
            'amount_collection' => ['required', 'max:6'],
            'amount_commission' => ['required', 'max:6'],
            'discount' => 'required',
            'rate_VAT' => 'required',
            'value_VAT' => 'required',
            'total' => 'required',
        ]);
        $attributes['value_status'] = 1;

        $invoice = Invoice::create($attributes);
        InvoicesDetails::create([
            'invoice_number' => $invoice->invoice_number,
            'invoice_id' => $invoice->id,
            'product' => $invoice->product_id,
            'section' => $invoice->section_id,
            'value_status' => $invoice->value_status,
            'user' => Auth::user()->email,
        ]);

        if ($request->hasFile('pic')) {
            $this->validate($request, ['pic' => ['required', 'mimes:pdf,jpeg,jpg,png', 'max:10000'] ]);
            InvoiceAttachment::create([
                'file_name' => $request->file('pic')->store('attachments/'.$invoice->invoice_number),
                'invoice_number' => $invoice->invoice_number,
                'created_by' => Auth::user()->email,
                'invoice_id' => $invoice->id,
            ]);

        }

        $user = User::all()->except(Auth::user()->id);
        Notification::send($user, new AddInvoice($invoice));


        return redirect('/invoices')->with('Add', 'تم اضافة الفاتورة بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(invoice $invoice)
    {
        return view('invoices.show', ['invoice' => $invoice, 'details' => $invoice->invoiceDetails, 'attachments' => $invoice->invoiceAttachments]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(invoice $invoice)
    {
        return view('invoices.edit', ['invoice' => $invoice, 'sections' => Section::all()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, invoice $invoice)
    {
        $attributes = $request->validate([
            'invoice_number'=>['required', Rule::exists('invoices', 'invoice_number')],
            'invoice_date'=>'required',
            'due_date'=>'required',
            'section_id'=>['required', Rule::exists('sections', 'id')],
            'product_id'=>['required', Rule::exists('products', 'id')],
            'amount_collection' => ['required', 'max:6'],
            'amount_commission' => ['required', 'max:6'],
            'discount' => 'required',
            'rate_VAT' => 'required',
            'value_VAT' => 'required',
            'total' => 'required',
        ]);

        $invoice->update($attributes);

        return redirect('/invoices')->with('Edit' ,'تم تعديل الفاتورة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(invoice $invoice)
    {
        if (\request()->id_page == 2) {
            $invoice->delete();
            return redirect('/invoices')->with('Archive', 'تم ارشفه الفاتورة بنجاح');
        } else {
            Storage::disk('documents')->deleteDirectory('attachments/' . $invoice->invoice_number);
            $invoice->forceDelete();
            return redirect('/invoices')->with('Delete', 'تم حذف الفاتورة بنجاح');
        }

    }

    public function getProduct($id)
    {
        $status = DB::table('products')->where('section_id', $id)->pluck("product_name", "id");
        return json_encode($status);
    }

    public function changeStatus(Invoice $invoice)
    {
        return view('invoices.status_update', ['invoice' => $invoice]);
    }

    public function createStatus(Invoice $invoice)
    {
        $attributes = \request()->validate([
            'invoice_number' => ['required', Rule::exists('invoices', 'invoice_number')],
            'invoice_id' => ['required', Rule::exists('invoices', 'id')],
            'product' => ['required', Rule::exists('products', 'id')],
            'section' => ['required', Rule::exists('sections', 'id')],
            'value_status' => ['required', 'integer' ,'between:2,3'],
            'payment_date' => 'required'
        ]);
        $attributes['note'] = \request()->note ?? null;
        $attributes['user'] = Auth::user()->email;
        InvoicesDetails::create($attributes);
        $invoice->update([
            'value_status' => $attributes['value_status'],
            'payment_date' => $attributes['payment_date']
        ]);
        return redirect('/invoices')->with('Status', 'تم تغيير حالة الدفع بنجاح');
    }

    public function getPaidInvoices()
    {
        return view('invoices.paid_invoices', ['invoices' => Invoice::where('value_status', '=', 3)->get()]);
    }

    public function getUnpaidInvoices()
    {
        return view('invoices.unpaid_invoices', ['invoices' => Invoice::where('value_status', '=', 1)->get()]);
    }

    public function getPartialInvoices()
    {
        return view('invoices.paid_invoices', ['invoices' => Invoice::where('value_status', '=', 2)->get()]);
    }

    public function getArchive()
    {
        return view('invoices.invoices_archive', ['invoices' => Invoice::onlyTrashed()->get()]);
    }

    public function restoreInvoice($id)
    {
        Invoice::onlyTrashed()->find($id)->restore();
        return redirect('/invoices')->with('Archive', 'تم استعادة الفاتورة بنجاح');
    }

    public function deleteArchive()
    {
        $invoice = Invoice::onlyTrashed()->find(request()->invoice_id);
        Storage::disk('documents')->deleteDirectory('attachments/' . $invoice->invoice_number);
        $invoice->forceDelete();
        return redirect('/invoices')->with('Delete', 'تم حذف الفاتورة بنجاح');
    }

    public function print(Invoice $invoice)
    {
        return view('invoices.print_invoice', ['invoice' => $invoice]);
    }

    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }

    public function markAllRead()
    {
        $notifications = \auth()->user()->unreadNotifications;
//        ddd($notifications);
        if ($notifications) {
            $notifications->markAsRead();
        }
            return back();
    }

    public function markRead($id)
    {
        $notification = Auth::user()->unreadNotifications()->find($id);
        $notification->markAsRead();
        $id = $notification->data['id'];

        return redirect('/invoices/' . $id);
    }


}
