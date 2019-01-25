@extends('layouts.app')

@section('content')
            <?= \Lava::render('ColumnChart', 'CustomerBrand', 'poll_div1') ?>
                <div id="poll_div1"></div>
@endsection
