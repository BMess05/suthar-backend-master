@extends('layouts.adminPanel')
@section('contents')
<div class="col-xl-12">
    <div class="card">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">References&nbsp;&nbsp; <span class="badge badge-success">Total: {{ $references->total() }}</span></h3>
                </div>
                <div class="col text-right">
                    <!-- some button on right -->
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-12">
                    <form action="{{ route('listReferences') }}" id="form-filter" autocomplete="off">
                        <div class="row">
                            @if(\Auth::user()->role == 0)
                            <div class="col">
                                <div class="form-group">
                                    <label for="store_manager">Manager: </label>
                                    <select class="form-control" id="managers_select" name="store_manager">
                                        <option value="" selected disabled>All</option>
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
                                        <option value="" selected disabled>All</option>
                                        @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ ($store->id == $store_id) ? 'selected' : '' }}>{{ $store->name . " (" . $store->city . ")" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="Status">Status: </label>
                                    <select name="status" class="form-control" id="status">
                                        <option value="" selected disabled>All</option>
                                        @foreach($references_statuses as $status)
                                        <option value="{{$status}}" {{ ($status_filter == $status) ? 'selected' : '' }}>{{$status}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div><div class="col">
                                <label for="from_date">From Date: </label>
                                <input type="text" name="from_date" class="form-control" id="startDate" value="{{$from_date}}">
                                <span class="date-error"></span>
                            </div>
                            <div class="col">
                                <label for="to_date">To Date: </label>
                                <input type="text" name="to_date" class="form-control" id="endDate" value="{{$to_date}}">
                            </div>
                            <div class="col">
                                <button class="btn btn-sm btn-primary btn-filter mt-5">Filter</button>
                                <a href="{{ route('listReferences') }}" class="btn btn-sm btn-primary mt-5">Reset</a>
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
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Status</th>
                    <!-- <th scope="col">Status</th> -->
                    <th scope="col">View</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @php
                $count = 1;
                if ($references->hasPages()):
                $per_page = config('constants.admin.per_page') ?? 10;
                $page = $references->currentPage();
                $count = ($per_page * ($page - 1)) + 1;
                endif;
                @endphp
                @forelse($references as $ref)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>{{ strlen($ref['full_name']) > 20 ? substr($ref['full_name'],0,20)."..." : $ref['full_name'] }}</td>
                    <td>{{ ($ref['email'] != "") ? $ref['email'] : "NA" }}</td>
                    <td>{{ $ref->phone_number }}</td>
                    <td>{{ $ref->status }}</td>
                    {{--
                    <td>
                        <select class="form-control" id="ref_status" data-ref="{{ $ref->id }}">
                            <option value="Pending" {{ ($ref->status == "Pending") ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ ($ref->status == "In Progress") ? 'selected' : '' }}>Accepted</option>
                            <option value="Rejected" {{ ($ref->status == "Rejected") ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </td>
                    --}}
                    <td>
                        <a href="{{ route('viewReference', $ref->id) }}"><i class="fa fa-eye text-info mr-3"></i></a>
                    </td>
                    <td>
                        <a class="result_{{$ref['id']}}">&nbsp;</a>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="8">No references added yet</td>
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
                    if(isset($_GET['store_manager'])) {
                        $append = ['store_manager' => $_GET['store_manager']];
                    }
                    if(isset($_GET['store'])) {
                        $append += ['store' => $_GET['store']];
                    }
                    if(isset($_GET['status'])) {
                        $append += ['status' => $_GET['status']];
                    }
                    if(isset($_GET['from_date'])) {
                        $append += ['from_date' => $_GET['from_date']];
                    }
                    if(isset($_GET['to_date'])) {
                        $append += ['to_date' => $_GET['to_date']];
                    }
                    ?>
                    {{$references->appends($append)->links('vendor.pagination.bootstrap-4', compact('references'))}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@if($from_date != "")
<script>
    $('#endDate').datepicker({
        dateFormat: "yy-mm-dd",
        minDate: "{{$from_date}}"
    });
</script>
@endif
<script>

$( function() {
var todaydt = new Date();
$("#startDate").datepicker({
            autoclose: true,
                dateFormat: "yy-mm-dd",
                endDate: todaydt,
                maxDate: '0',
            onSelect: function (date) {
                //Get selected date
                var date2 = $('#startDate').datepicker('getDate');
                //sets minDate to endDate
                $('#endDate').datepicker('option', 'minDate', date2);
            }
        });
        $('#endDate').datepicker({
            dateFormat: "yy-mm-dd",
            maxDate: '0'
        });
} );
</script>

<script>
$(document).on('click', '.btn-filter', function(e) {
    e.preventDefault();
    var startDate = new Date($('#startDate').val());
    var endDate = new Date($('#endDate').val());

    if (startDate > endDate){
        $('.date-error').html('From date must be before To date.');
    }   else {
        $('.date-error').html('');
        $('#form-filter').submit();
    }
});
function confirmationDelete(anchor) {
    swal({
        title: "Are you sure want to delete this gift?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            window.location = anchor.attr("href");
        }
    });
    //   var conf = confirm("Are you sure want to delete this User?");
    //   if (conf) window.location = anchor.attr("href");
}

$("#managers_select").select2({
    tags: true,
    createTag: function (tag) {

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
    createTag: function (tag) {

        // Check if the option is already there
        var found = false;
        $("#timezones option").each(function() {
            if ($.trim(tag.term).toUpperCase() === $.trim($(this).text()).toUpperCase()) {
                found = true;
            }
        });
    }
});

$('#status').select2();

$(document).on('change', '#managers_select', function(e) {
    let selected_manager = $(this).val();
    let data = {
        "_token" : "{{csrf_token()}}",
        "selected_manager" : selected_manager
    };
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "{{ route('getManagerStores') }}",
        data: data,
        success: function(res) {
            if(res.success == 1) {
                $(`#store_select`).html(res.options);
            }
        }
    });
});
</script>


@endsection