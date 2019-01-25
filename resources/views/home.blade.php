@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        <h4 class="text-center">Commissions per Salesperson</h4><br/>
                        <form method="post" action="{{action('CommissionController@calcCommissions')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="form-group col-md-4">
                                    <label for="salesperson">SalesPerson:</label>
                                    <select class="form-control" name="salesperson_id">
                                        @foreach($salesperson as $sp)
                                            @if ($sp->sales_person_id == $data['salesperson_id'])
                                                <option value="{{$sp->sales_person_id}}" selected>{{$sp->name}}
                                                    ({{$sp->region}})
                                                </option>
                                            @else
                                                <option value="{{$sp->sales_person_id}}">{{$sp->name}} ({{$sp->region}}
                                                    )
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="form-group col-md-4">
                                    <label for="salesperson">Month:</label>
                                    <select class="form-control" name="month">
                                        @foreach($months as $sp)
                                            @if ($sp->month_id == $data['month'])
                                                <option value="{{$sp->month_id}}" selected>{{$sp->name}} </option>
                                            @else
                                                <option value="{{$sp->month_id}}">{{$sp->name}} </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="form-group col-md-4">
                                    <button type="submit" name="display" value="display" class="btn btn-primary">
                                        Ready set go
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <h4 class="text-center">Commissions per Region</h4><br/>
                        <form method="post" action="{{action('DevelopController@calcRegions')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="form-group col-md-4">
                                    <label for="region">Region:</label>
                                    <select class="form-control" name="region">
                                        <option value="0" selected>All</option>
                                        <option value="N">North</option>
                                        <option value="S">South</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="form-group col-md-4">
                                    <button type="submit" name="display" value="display" class="btn btn-primary">
                                        Ready set go
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
