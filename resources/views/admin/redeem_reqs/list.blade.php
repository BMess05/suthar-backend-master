@extends('layouts.adminPanel')
@section('head')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<style>
    img.cropped_image {
        height: 84px;
    }
</style>
@endsection
@section('contents')
<div class="col-xl-12">
    <div class="card">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Redeem Requests&nbsp;&nbsp; <span class="badge badge-success">Total: {{ $redeemReqs->total() }}</span></h3>
                </div>
                <div class="col text-right">
                    <!-- some button on right -->
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-12">
                    <form action="{{ route('listRedeenRequests') }}" id="form-filter" autocomplete="off">
                        <div class="row">
                            @if(\Auth::user()->role == 0)
                            <div class="col">
                                <div class="form-group">
                                    <label for="store_manager">Manager: </label>
                                    <select class="form-control" id="managers_select" name="store_manager">
                                        <option value="" {{ ($store_manager_id == "") ? 'selected' : '' }} disabled>All</option>
                                        @foreach($store_managers as $manager)
                                        <option value="{{ $manager->id }}" {{ ($manager->id == $store_manager_id) ? 'selected' : '' }}>{{ $manager->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col">
                                <div class="form-group">
                                    <label for="store">Stores: </label>
                                    <select class="form-control" id="store_select" name="store">
                                        <option value="" {{ ($store_id == "") ? 'selected' : '' }} disabled hidden>All</option>
                                        @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ ($store->id == $store_id) ? 'selected' : '' }}>{{ $store->name . " \n(" . $store->city . ")" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="status">Status: </label>
                                    <select name="status" class="form-control" id="status_select">
                                        <option value="" {{ ($status == "") ? 'selected' : '' }} disabled>All</option>
                                        <option value="1" {{ ($status == 1) ? 'selected' : '' }}>Completed</option>
                                        <option value="0" {{ ($status == 0) ? 'selected' : '' }}>Pending</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="type">Type: </label>
                                    <select name="type" class="form-control" id="type_select">
                                        <option value="" {{ ($type == "") ? 'selected' : '' }} disabled>All</option>
                                        <option value="1" {{ ($type == 1) ? 'selected' : '' }}>Cash</option>
                                        <option value="2" {{ ($type == 2) ? 'selected' : '' }}>Account</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <button class="btn btn-sm btn-primary btn-filter mt-5">Filter</button>
                                <a href="{{ route('listRedeenRequests') }}" class="btn btn-sm btn-primary mt-5">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <!-- Projects table -->
            <table class="table align-items-center table-flush text-center">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Sr. No.</th>
                        <th scope="col">Contractor Name</th>
                        <th scope="col">Request type</th>
                        <th scope="col">Points</th>
                        <th scope="col">Rupee(s)</th>
                        <th scope="col">Status</th>
                        @if(\Auth::user()->role == 1)
                        <th scope="col">Action</th>
                        @endif
                        <th scope="col">Request Details</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $count = 1;
                    if ($redeemReqs->hasPages()):
                    $per_page = config('constants.admin.per_page') ?? 10;
                    $page = $redeemReqs->currentPage();
                    $count = ($per_page * ($page - 1)) + 1;
                    endif;
                    @endphp
                    @forelse($redeemReqs as $req)
                    <tr>
                        <td>{{ $count++ }}</td>
                        <td><a target="_blank" href="{{ route('viewContractor', $req['contractor']['id']) }}">{{ strlen($req['contractor']['name']) > 20 ? substr($req['contractor']['name'],0,20)."..." : $req['contractor']['name'] }}</a></td>
                        <td>{{ $req['request_type_name'] }}</td>
                        <td>{{ $req->points }}</td>
                        <td>{{ $req->in_rupees }}</td>
                        <td class="res_status_text_{{$req->id}}">{{ ($req->req_status == 1) ? 'Completed' : 'Pending' }}</td>
                        @if(\Auth::user()->role == 1)
                        <td>

                            <div class="btns btns-actions_{{ $req['id'] }}">
                                @if($req->req_status == 0)
                                <button class="btn btn-sm btn-primary pending-complete comp_{{ $req['id'] }} {{-- ($req->req_status == 1) ? 'hide' : '' --}}" data-id="{{ $req['id'] }}" data-action="complete">Complete</button>
                                @else
                                <i class="fa fa-check text-success"></i>
                                @endif
                            </div>
                        </td>
                        @endif
                        <td>
                            @if($req->req_type == 2)
                            <a href=" #" class="view-bank" data-req="{{ $req->id }}"><i class="fa fa-eye text-info mr-3"></i></a>
                            @elseif($req->req_type == 3)
                            <a href="#" class="view-bank" data-req="{{ $req->id }}"><i class="fa fa-eye text-info mr-3"></i></a>
                            @else
                            Cash
                            @endif
                        </td>
                        <td>
                            <a class="result_{{$req['id']}}">&nbsp;</a>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="8">No redeem requests added yet</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="pagination justify-content-end">
                    <?php
                    $append = [];
                    if (isset($_GET['store_manager'])) {
                        $append = ['store_manager' => $_GET['store_manager']];
                    }
                    if (isset($_GET['store'])) {
                        $append += ['store' => $_GET['store']];
                    }
                    if (isset($_GET['status'])) {
                        $append += ['status' => $_GET['status']];
                    }
                    if (isset($_GET['type'])) {
                        $append += ['type' => $_GET['type']];
                    }
                    ?>
                    {{$redeemReqs->appends($append)->links('vendor.pagination.bootstrap-4', compact('redeemReqs'))}}
                </div>
            </div>
        </div>
    </div>
</div>

@forelse($redeemReqs as $req)
@if($req->req_type == 2)

<!-- bank details modal -->
<div class="modal fade" id="modal_{{ $req->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Bank Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Account holder name: </th>
                                        <th class="text-left" scope="col">{{ $req->bank->account_holder_name ?? '' }}</th>
                                    </tr>
                                    <tr>
                                        <th scope="col">Account Number: </th>
                                        <th class="text-left" scope="col">{{ $req->bank->account_number ?? '' }}</th>
                                    </tr>
                                    <tr>
                                        <th scope="col">Bank name: </th>
                                        <th class="text-left" scope="col">{{ $req->bank->bank_name ?? '' }}</th>
                                    </tr>
                                    <tr>
                                        <th scope="col">IFSC code: </th>
                                        <th class="text-left" scope="col">{{ $req->bank->ifsc_code ?? '' }}</th>
                                    </tr>
                                    <tr>
                                        <th scope="col">Account type: </th>
                                        <th class="text-left" scope="col">{{ $req->bank->account_type ?? '' }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@elseif($req->req_type == 3)

<div class="modal fade" id="modal_{{ $req->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Order Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Points</th>
                                        <th scope="col">Image</th>
                                    </tr>
                                    @foreach($req->gift_order->order_gifts as $order_gift)
                                    <tr>
                                        <th scope="col">{{ $order_gift->gift->name ?? 'Gift removed from store' }}</th>
                                        <th scope="col">{{ $order_gift->gift->points ?? 'Gift removed from store' }}</th>
                                        <th>
                                            @if($order_gift->gift && $order_gift->gift->photo_url != "")
                                            <img src="{{ $order_gift->gift->photo_url }}" class="cropped_image img img-sm">

                                            @endif
                                        </th>
                                    </tr>
                                    @endforeach
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endif
@empty
<!--  -->
@endforelse

@endsection
@section('scripts')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
    $(document).on('click', '.view-bank', function(e) {
        e.preventDefault();
        let req_id = $(this).data('req');
        $(`#modal_${req_id}`).modal({
            backdrop: 'static',
            keyboard: false
        });
    });
</script>

<script>
    $(function() {
        // $('.pending-complete').bootstrapToggle({
        //     on: 'Complete',
        //     off: 'Pending'
        // });
    });

    $(document).on('click', '.pending-complete', function(e) {
        let action = $(this).data('action');
        let title = '';
        // if (action == 'complete') {
        title = 'Are you sure want to complete the status for this request?';
        // } else if (action == 'revert') {
        //     title = 'Are you sure want to Revert the points for this request?';
        // } else {
        //     return;
        // }
        // alert(title);
        swal({
            title: title,
            text: "",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willChange) => {
            if (willChange) {

                let id = $(this).data('id');
                let data = {
                    "_token": "{{csrf_token()}}",
                    "id": id
                };
                // if (action == 'complete') {
                url = "{{ route('changeStatusRedeemRequest') }}";
                // } else {
                // url = "{{-- route('revertStatusRedeemRequest') --}}";
                // }
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: url,
                    data: data,
                    success: function(res) {
                        if (res.success == 1) {
                            // if (action == 'complete') {
                            $(`.res_status_text_${id}`).text('Completed');
                            $(`.comp_${id}`).addClass('hide');
                            $(`.btns-actions_${id}`).html('<i class="fa fa-check text-success"></i>');
                            toastr.success(`${res.message}`);
                            // $(`.rev_${id}`).removeClass('hide');
                            // } else {
                            //     $(`.res_status_text_${id}`).text('Pending');
                            //     $(`.rev_${id}`).addClass('hide');
                            //     $(`.comp_${id}`).removeClass('hide');
                            // }
                            $(`.result_${id}`).html('<i class="fa fa-check text-success"></i>');
                            setTimeout(() => {
                                $(`.result_${id}`).html('');
                            }, 1000);
                        } else {
                            toastr.error(`${res.message}`);
                        }
                    }
                });
            }
        });
    });

    $("#managers_select").select2({
        tags: true,
        createTag: function(tag) {

            // Check if the option is already there
            var found = false;
            $("#timezones option").each(function() {
                if ($.trim(tag.term).toUpperCase() === $.trim($(this).text()).toUpperCase()) {
                    found = true;
                }
            });
        }
    });

    $('#store_select').select2({
        tags: true,
        createTag: function(tag) {

            // Check if the option is already there
            var found = false;
            $("#timezones option").each(function() {
                if ($.trim(tag.term).toUpperCase() === $.trim($(this).text()).toUpperCase()) {
                    found = true;
                }
            });
        }
    });
    $('#status_select').select2();
    $('#type_select').select2();
    $(document).on('change', '#managers_select', function(e) {
        let selected_manager = $(this).val();
        let data = {
            "_token": "{{csrf_token()}}",
            "selected_manager": selected_manager
        };
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('getManagerStores') }}",
            data: data,
            success: function(res) {
                if (res.success == 1) {
                    $(`#store_select`).html(res.options);
                }
            }
        });
    });
</script>
@endsection
