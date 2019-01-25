@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
    <div class="container">
        <div class="card">
            <div class='card-header'>
                <h5>Southern Region: Monthly Sales, Commissions, Average Margin </h5>
            </div>

            <div class="card card-body">
                <div id="month_div12">
					<?= \Lava::render('ComboChart', 'MonthsSouth', 'month_div12') ?>
                </div>
                <table id="accounts" class="table table-bordered table-hover table-sm">
                    <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-xl-right">Sales $</th>
                        <th class="text-xl-right">Comm $</th>
                        <th class="text-xl-right">Avg. Margin $</th>
                        <th class="text-xl-right">SO #</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($monthItemsSouth as $sl)
                        <tr>
                            <td>{{ date("F", mktime(0, 0, 0, $sl->month, 1)) }} {{$sl->year}} </td>
                            <td class="text-xl-right">{{number_format($sl->month_sale,2)}}</td>
                            <td class="text-xl-right">{{number_format($sl->month_commission,2)}}</td>
                            <td class="text-xl-right">{{number_format($sl->month_margin,2)}}</td>
                            <td class="text-xl-right">{{number_format($sl->so_count)}}</td>
                        </tr>
                    @endforeach
                    <tr class="makebold">
                        <td>Totals</td>
                        <td class="text-xl-right">{{number_format($SouthernTotals['totalSales'],2)}}</td>
                        <td class="text-xl-right">{{number_format($SouthernTotals['totalCommission'],2)}}</td>
                        <td class="text-xl-right">{{number_format($SouthernTotals['AvMarginTotal'],2)}}</td>
                        <td class="text-xl-right">{{number_format($SouthernTotals['totalSO'])}}</td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

