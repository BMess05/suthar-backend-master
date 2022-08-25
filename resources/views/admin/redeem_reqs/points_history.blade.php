@extends('layouts.adminPanel')
@section('head')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<style>
span.date-error {
    color: red;
    font-size: 10px;
}
</style>
@endsection
@section('contents')
<div class="col-xl-12">
    <div class="card">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <h3 class="mb-5">Points History&nbsp;&nbsp; <span class="badge badge-success">Total: {{ $point_history->total() }}</span></h3>
                </div>
                <div class="col-md-12">
                    <form action="{{ route('pointHistory') }}" id="form-filter" autocomplete="off">
                        <div class="row">
                            <div class="col">
                                <label for="type">Type: </label>
                                <select name="type" class="form-control" id="type_filter">
                                    <option {{($store_manager_id == "") ? 'selected' : ''}} disabled>All</option>
                                    <option value="0" {{ isset($type) && ($type == 0) ? 'selected' : '' }}>Credited</option>
                                    <option value="1" {{ isset($type) && ($type == 1) ? 'selected' : '' }}>Redeemed</option>
                                </select>
                            </div>
                            @if(\Auth::user()->role == 0)
                            <div class="col">
                                <div class="form-group">
                                    <label for="store_manager">Manager: </label>
                                    <select class="form-control" id="managers_select" name="store_manager">
                                        <option {{($store_manager_id == "") ? 'selected' : ''}} disabled>All</option>
                                        @foreach($store_managers as $manager)
                                        <option value="{{ $manager->id }}" {{ ($manager->id == $store_manager_id) ? 'selected' : '' }}>{{ $manager->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col">
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
                                <a href="{{ route('pointHistory') }}" class="btn btn-sm btn-primary mt-5">Reset</a>
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
                    <th scope="col">Redeemed/Credited</th>
                    <th scope="col">Points</th>
                    <th scope="col">Credited By</th>
                    <th scope="col">Credited for<br> Reference</th>
                    <th scope="col">Redeemed Through</th>
                </tr>
            </thead>
            <tbody>
                @php
                $count = 1;
                if ($point_history->hasPages()):
                $per_page = config('constants.admin.per_page') ?? 10;
                $page = $point_history->currentPage();
                $count = ($per_page * ($page - 1)) + 1;
                endif;
                @endphp
                @forelse($point_history as $point)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td><a target="_blank" target="_blank" href="{{ route('viewContractor', $point['contractor']['id']) }}">{{ strlen($point['contractor']['name']) > 20 ? substr($point['contractor']['name'],0,20)."..." : $point['contractor']['name'] }}</a></td>
                    <td>{{ $point['redeem_credit'] }}</td>
                    <td>{{ $point['points'] }}</td>
                    <td>
                        @if($point['redeem'] == 0)
                        <a target="_blank" href="{{ route('viewUser', $point['added_by']) }}">{{ $point['store_manager']['name'] }}</a>
                        @else
                        NA
                        @endif
                    </td>
                    <td>
                        @if($point['redeem'] == 0)
                        <a target="_blank" href="{{ route('viewReference', $point['reference_id']) }}">{{ $point['reference']['full_name'] ?? '' }}</a>
                        @else
                        NA
                        @endif
                    </td>
                    <td>
                        @if($point['redeem'] == 1)
                        {{ $point['redeem_type_name'] }}
                        @else
                        NA
                        @endif
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="8">No point history found.</td>
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
                    if(isset($_GET['type'])) {
                        $append = ['type' => $_GET['type']];
                    }
                    if(isset($_GET['store_manager'])) {
                        $append += ['store_manager' => $_GET['store_manager']];
                    }
                    if(isset($_GET['from_date'])) {
                        $append += ['from_date' => $_GET['from_date']];
                    }
                    if(isset($_GET['to_date'])) {
                        $append += ['to_date' => $_GET['to_date']];
                    }
                    ?>
                    {{$point_history->appends($append)->links('vendor.pagination.bootstrap-4', compact('point_history'))}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($from_date != "")
<script>
    $('#endDate').datepicker({
        dateFormat: "yy-mm-dd",
        minDate: "{{$from_date}}"
    });
</script>
@endif
<script>

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

        // Show the suggestion only if a match was not found
        // if (!found) {
        //     return {
        //         id: tag.term,
        //         text: tag.term + " (new)",
        //         isNew: true
        //     };
        // }
    }
});
$('#type_filter').select2();
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

</script>

  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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
@endsection