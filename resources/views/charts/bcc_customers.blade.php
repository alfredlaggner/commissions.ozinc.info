@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
    <div class="card card-body">
        <div id="month_div1">
			<?= \Lava::render('ColumnChart', 'Months', 'month_div1') ?>
        </div>
        <table id="accounts" class="table table-bordered table-hover table-sm">
            <thead>
            <tr>
                <th>Month</th>
                <th class="text-xl-right">Sales $</th>
                <th class="text-xl-right">Comm $</th>
                <th class="text-xl-right">Avg. Margin $</th>
                <th class="text-xl-right">Sold #</th>
            </tr>
            </thead>
            <tbody>
            @foreach($monthItems as $sl)
                <tr>
                    <td>{{ date("F", mktime(0, 0, 0, $sl->month, 1)) }} {{$sl->year}} </td>
                    <td class="text-xl-right">{{number_format($sl->month_sale,2)}}</td>
                    <td class="text-xl-right">{{number_format($sl->month_commission,2)}}</td>
                    <td class="text-xl-right">{{number_format($sl->month_margin,2)}}</td>
                    <td class="text-xl-right">{{$sl->month_sold}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

