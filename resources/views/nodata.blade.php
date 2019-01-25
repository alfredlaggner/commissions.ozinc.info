@extends('layouts.app')
@section('title', 'Commissions Report')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        No data for this month found.
                    </div>
                    <div class="card-footer">
                        <div class="col-auto">
                            <a href="{{ route('go-home') }}" class="btn btn-primary" role="button"
                               aria-pressed="true">Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
