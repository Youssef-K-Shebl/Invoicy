<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $count = Invoice::count();

        $paid = Invoice::where('value_status', 3)->count();
        if ($paid != 0) {
            $perPaid = ($paid/$count)*100;
        } else {
            $perPaid = 0;
        }

        $unpaid = Invoice::where('value_status', 1)->count();
        if ($unpaid != 0) {
            $perUnpaid = ($unpaid/$count)*100;
        } else {
            $perUnpaid = 0;
        }

        $partPaid = Invoice::where('value_status', 2)->count();
        if ($partPaid != 0) {
            $perPartPaid = ($partPaid/$count)*100;
        } else {
            $perPartPaid = 0;
        }

        $bar = app()->chartjs
            ->name('barChartTest')
            ->type('bar')
            ->size(['width' => 360, 'height' => 200])
            ->labels(['الفواتير الغير مدفوعة', 'الفواتير المدفوعة', 'الفواتير المدفوعة جزئيا'])
            ->datasets([
                [
                    "label" => "نسبة الفواتير الغير المدفوعة",
                    'backgroundColor' => ['#FF4A4A'],
                    'data' => [$perUnpaid]
                ],

                [
                    "label" => "نسبة الفواتير المدفوعة",
                    'backgroundColor' => ['#6FEDD6'],
                    'data' => [$perPaid]
                ],

                [
                "label" => "نسبة الفواتير المدفوعة جزئيا",
                'backgroundColor' => ['#FF9551'],
                'data' => [$perPartPaid]
            ]
            ])
            ->options([
                'legend' => [
                    'display' => true,
                    'labels' => [
                        'fontColor' => 'black',
                        'fontFamily' => 'Cairo',
                        'fontStyle' => 'bold',
                        'fontSize' => 14,
                    ]
                ]

            ]);

        $pie = app()->chartjs
            ->name('pieChartTest')
            ->type('pie')
            ->size(['width' => 400, 'height' => 200])
            ->labels(['الفواتير الغير مدفوعة', 'الفواتير المدفوهة جزئيا', ' الفواتير المدفوعة'])
            ->datasets([
                [
                    'backgroundColor' => ['#FF4A4A', '#FF9551', '#6FEDD6'],
//                    'hoverBackgroundColor' => ['#FF6384', '#36A2EB'],
                    'data' => [$perUnpaid, $perPartPaid, $perPaid]
                ]
            ])
            ->options([]);

        return view('home', ['bar' => $bar, 'pie' => $pie]);
    }
}
