@extends('layouts.app')

@section('content')
	<?= \Lava::render('DonutChart', 'CustomerBrand', 'poll_div1') ?>
	<?= \Lava::render('DonutChart', 'CustomerBrand2', 'poll_div2') ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="modal fade" id="exampleModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog  modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{$customer_name}} Brand Sales</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <div style="margin-top: -20px" id="poll_div1"></div>
                                    </div>
                                    <div class="col">
                                        <div style="margin-top: -20px" id="poll_div2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" id="goback" onclick="goBack()">Go Back</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        $(document).ready(function () {
            $('#exampleModal').modal('show');
        });
        $(document).ready(function () {
            $('#goback').click(function () {
                parent.history.back();
                $("#viewCustomers").click();
                return false;
            });
        });

    </script>
@endsection
