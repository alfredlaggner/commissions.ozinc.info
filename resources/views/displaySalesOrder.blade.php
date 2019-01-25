@extends('layouts.app')
@section('title', 'Sales Report')
@section('content')
    <div class="container">
        <div class="d-flex p2">
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    <p>{{ \Session::get('success') }}</p>
                </div><br/>
            @endif
            <div class=" d-flex flex-column bd-highlight mb-3">
                <div>
                    <div class="row">

                        <div class="col">
                            <ul class="list-group">
                                <li class="list-group-item">Customer: <b>{{$o->customer->name}}</b></li>
                                <li class="list-group-item">Order date: <b>{{$o->order_date}}</b></li>
                                <li class="list-group-item">Sold by: <b>{{$o->salesperson->name}}</b></li>
                                <li class="list-group-item">Amount: <b>{{$o->amount_untaxed}}</b></li>
                                <li class="list-group-item">Tax: <b>{{$o->amount_tax}}</b></li>
                                <li class="list-group-item">Total Amount: <b>{{$o->amount_total}}</b></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 20px">
                    <table class="table table-bordered table-hover ">
                        <thead>
                        <tr>
                            <th>SO</th>
                            <th>Date</th>
                            <th>Sku</th>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Qty Invoiced</th>
                            <th>Cost</th>
                            <th>Price</th>
                            <th>Amt Invoiced</th>
                            <th>Margin</th>
                            <th>Product Margin</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sline as $sl)
                            @php
                                $style = '';
                                if (! is_null($sl->product_margin)){

                                    if ($sl->margin < $sl->product_margin)
                                    {$style = 'color:red';}
                                    elseif ($sl->margin > $sl->product_margin)
                                     {$style = 'color:green';}
                                else
                                         {$style = '';}
                                }
                            @endphp
                            <tr>
                                <td>{{$sl->invoice_number}}</td>
                                <td>{{date('m-d',strtotime($sl->order_date))}}</td>
                                <td>{{$sl->code}}</td>
                                <td>{{substr($sl->name,0,100)}}</td>
                                <td>{{$sl->quantity}}</td>
                                <td>{{$sl->qty_invoiced}}</td>
                                <td>{{$sl->cost}}</td>
                                <td>{{$sl->unit_price}}</td>
                                <td>{{$sl->amt_invoiced}}</td>
                                <td style= {{$style}}>{{$sl->margin}}</td>
                                <td>{{$sl->product_margin}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="javascript:history.back()" class="btn btn-outline-primary" role="button"
                   aria-pressed="true">Back</a>
            </div>
        </div>
@endsection
