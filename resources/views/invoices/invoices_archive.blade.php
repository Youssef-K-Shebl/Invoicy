@extends('layouts.master')
@section('title')
    قائمة الفواتير
@endsection
@section('css')
    <!-- Internal Data table css -->
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/notify/css/notifIt.css')}}" rel="stylesheet"/>

@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">الفواتير</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة الفواتير</span>
						</div>
					</div>
				</div>
				<!-- breadcrumb -->
@endsection
@section('content')
				<!-- row -->

                @if (session()->has('Delete'))
                    <script>
                        window.onload = function () {
                            notif({
                                msg: "تم حذف الفاتورة بنجاح",
                                type: "success"
                            })
                        }
                    </script>
                @endif

                @if (session()->has('Status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session()->get('Status') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session()->has('Add'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session()->get('Add') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session()->has('Edit'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session()->get('Editي') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

				<div class="row">
                        <div class="col-xl-12">
                            <div class="card mg-b-20">
                                <div class="card-header pb-0">
                                    <a href="invoices/create" class="modal-effect btn btn-sm btn-primary" style="color:white"><i
                                            class="fas fa-plus"></i>&nbsp; اضافة فاتورة</a>
                                </div>
                                <div class="card-header pb-0">
                                    <div class="d-flex justify-content-between">
                                        <h4 class="card-title mg-b-0">Bordered Table</h4>
                                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                                    </div>
                                    <p class="tx-12 tx-gray-500 mb-2">Example of Valex Bordered Table.. <a href="">Learn more</a></p>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="example" class="table key-buttons text-md-nowrap">
                                            <thead>
                                            <tr>
                                                <th class="border-bottom-0">#</th>
                                                <th class="border-bottom-0">رقم الفاتورة</th>
                                                <th class="border-bottom-0">تاريخ الفاتورة</th>
                                                <th class="border-bottom-0">تاريخ الاستحقاق</th>
                                                <th class="border-bottom-0">المنتج</th>
                                                <th class="border-bottom-0">القسم</th>
                                                <th class="border-bottom-0">الخصم</th>
                                                <th class="border-bottom-0">نسبة الضريبه</th>
                                                <th class="border-bottom-0">قيمة الضريبه</th>
                                                <th class="border-bottom-0">الاجمالي</th>
                                                <th class="border-bottom-0">الحالة</th>
                                                <th class="border-bottom-0">الملاحظات</th>
                                                <th class="border-bottom-0">العمليات</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                            $i = 0
                                            @endphp
                                            @foreach($invoices as $invoice)
                                            <tr>
                                                <td>{{ ++$i }}</td>
                                                <td>
                                                    <a href="{{ url('invoices') }}/{{ $invoice->id }}">{{ $invoice->invoice_number }}</a>
                                                </td>
                                                <td>{{ $invoice->invoice_Date }}</td>
                                                <td>{{ $invoice->due_date }}</td>
                                                <td>{{ $invoice->product->product_name }}</td>
                                                <td>{{ $invoice->section->section_name }}</td>
                                                <td>{{ $invoice->discount }}</td>
                                                <td>{{ $invoice->rate_VAT }}</td>
                                                <td>{{ $invoice->value_VAT }}</td>
                                                <td>{{ $invoice->total }}</td>
                                                <td class="{{ $invoice->value_status == 'غير مدفوعة' ? 'text-danger' : ($invoice->value_status == 'مدفوعه' ? 'text-success' : 'text-warning')}}">
                                                    {{ $invoice->value_status }}
                                                </td>
                                                <td>{{ $invoice->note }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button aria-expanded="false" aria-haspopup="true"
                                                                class="btn ripple btn-primary btn-sm" data-toggle="dropdown"
                                                                type="button">العمليات<i class="fas fa-caret-down ml-1"></i></button>
                                                        <div class="dropdown-menu tx-13">

                                                            @can('حذف الفاتورة')
                                                                <a class="dropdown-item" href="#" data-invoice_id="{{ $invoice->id }}"
                                                                   data-toggle="modal" data-target="#delete_invoice"><i
                                                                        class="text-danger fas fa-trash-alt"></i>&nbsp;&nbsp;حذف
                                                                    الفاتورة</a>
                                                            @endcan

                                                            @can('تغير حالة الدفع')
                                                                <a class="dropdown-item" href="#" data-invoice_id="{{ $invoice->id }}"
                                                                   data-toggle="modal" data-target="#Transfer_invoice"><i
                                                                        class="text-warning fas fa-exchange-alt"></i>&nbsp;&nbsp;نقل الي
                                                                    الفواتير</a>
                                                            @endcan

                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="modal fade" id="delete_invoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">حذف الفاتورة</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <form id="delete-form" action="/delete" method="post">
                                    @method('delete')
                                    @csrf
                                </div>
                                <div class="modal-body">
                                    هل انت متاكد من عملية الحذف ؟
                                    <input type="hidden" name="invoice_id" id="invoice_id" value="">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                                    <button type="submit" class="btn btn-danger">تاكيد</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
				</div>

                <!--الغاء الارشفة-->
                <div class="modal fade" id="Transfer_invoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">الغاء ارشفة الفاتورة</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <form id="restore-form" method="post">
                                @method('delete')
                                @csrf
                            </div>
                            <div class="modal-body">
                                هل انت متاكد من عملية الغاء الارشفة ؟
                                <input type="hidden" name="invoice_id" id="invoice_id" value="">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                                <button type="submit" class="btn btn-success">تاكيد</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
				<!-- row closed -->

@endsection
@section('js')
    <!-- Internal Data tables -->
    <script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/pdfmake.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/vfs_fonts.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js')}}"></script>
    <!--Internal  Datatable js -->
    <script src="{{URL::asset('assets/js/table-data.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/notify/js/notifIt.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/notify/js/notifit-custom.js')}}"></script>

    <script>
        $('#delete_invoice').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var invoice_id = button.data('invoice_id')
            var modal = $(this)
            modal.find('.modal-body #invoice_id').val(invoice_id);
        })
    </script>

    <script>
        $('#Transfer_invoice').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var invoice_id = button.data('invoice_id')
            var modal = $(this)
            modal.find('.modal-body #invoice_id').val(invoice_id);
            document.getElementById('restore-form').setAttribute('action', '/restore/'+invoice_id);
        })
    </script>

@endsection
