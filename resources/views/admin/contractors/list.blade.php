@extends('layouts.adminPanel')
@section('head')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection
@section('contents')
<div class="col-xl-12">
    <div class="card">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Users&nbsp;&nbsp; <span class="badge badge-success">Total: {{ $contractors->total() }}</span></h3>
                </div>
                <div class="col text-right">
                    <button class="btn btn-sm btn-primary" data-href="{{ route('exportUsers') }}" id="export" onclick="exportTasks(event.target);">Export</button>
                    @if(\Auth::user()->role == 1)
                    <a href="{{ route('addContractor') }}" class="btn btn-sm btn-primary">Add New User</a>
                    @endif
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-12">
                    <form action="{{ route('listContractors') }}" id="form-filter" autocomplete="off">
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
                                        <option value="" {{ ($store_id == "") ? 'selected' : '' }} disabled>All</option>
                                        @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ ($store->id == $store_id) ? 'selected' : '' }}>{{ $store->name . " \n(" . $store->city . ")" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="type">Type: </label>
                                    <select name="type" class="form-control" id="type_select">
                                        <option value="" {{ ($type == "") ? 'selected' : '' }} disabled>All</option>
                                        @foreach($contractor_types as $val => $type)
                                        <option value="{{ $val }}" {{ ($val == $type) ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="block_unblock">Blocked/Unblocked: </label>
                                    <select name="block_unblock" class="form-control" id="block_unblock_filter">
                                        <option value="" {{ ($block_unblock == "") ? 'selected' : '' }} disabled>All</option>
                                        <option value="1" {{ ($block_unblock == 1) ? 'selected' : '' }}>Blocked</option>
                                        <option value="0" {{ ($block_unblock == 0) ? 'selected' : '' }}>Unblocked</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <button class="btn btn-sm btn-primary btn-filter mt-5">Filter</button>
                                <a href="{{ route('listContractors') }}" class="btn btn-sm btn-primary mt-5">Reset</a>
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
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        @if(\Auth::user()->role == 1)
                        <th scope="col">Actions</th>
                        <th scope="col">Block/Unblock</th>
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                    $count = 1;
                    if ($contractors->hasPages()):
                    $per_page = config('constants.admin.per_page') ?? 10;
                    $page = $contractors->currentPage();
                    $count = ($per_page * ($page - 1)) + 1;
                    endif;
                    @endphp
                    @forelse($contractors as $contractor)
                    <tr>
                        <td>{{ $count++ }}</td>
                        <td>{{ $contractor['id'] }}</td>
                        <td>{{ strlen($contractor['name']) > 20 ? substr($contractor['name'],0,20)."..." : $contractor['name'] }}</td>
                        <td>{{ $contractor['email'] }}</td>
                        <td>{{ (isset($contractor['phone']) && ($contractor['phone'] != '')) ? $contractor['phone'] : 'NA' }}</td>
                        @if(\Auth::user()->role == 1)
                        <td>
                            <a href="{{ route('editContractor', $contractor['id']) }}"><i class="fa fa-pen text-warning mr-3"></i></a>
                            <a onclick="javascript:confirmationDelete($(this));return false;" href="{{ route('deleteContractor', $contractor['id']) }}"><i class="fa fa-trash text-danger mr-3"></i></a>
                            <a href="{{ route('viewContractor', $contractor['id']) }}"><i class="fa fa-eye text-info mr-3"></i></a>
                        </td>
                        <td>
                            <div class="switch">
                                <input type="checkbox" class="block-unblock" data-id="{{ $contractor['id'] }}" {{ ($contractor['block_unblock'] == 1) ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <a class="result_{{$contractor['id']}}">&nbsp;</a>
                        </td>
                        @endif
                    </tr>

                    @empty
                    <tr>
                        <td colspan="9">No contractors added yet</td>
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
                    if (isset($_GET['type'])) {
                        $append += ['type' => $_GET['type']];
                    }
                    if (isset($_GET['block_unblock'])) {
                        $append += ['block_unblock' => $_GET['block_unblock']];
                    }
                    ?>
                    {{$contractors->appends($append)->links('vendor.pagination.bootstrap-4', compact('contractors'))}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
    $(function() {
        $('.block-unblock').bootstrapToggle({
            on: 'Blocked',
            off: 'Unblocked'
        });
    });

    $(document).on('change', '.block-unblock', function(e) {
        let id = $(this).data('id');
        let data = {
            "_token": "{{csrf_token()}}",
            "id": id
        };
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('blockUnblock') }}",
            data: data,
            success: function(res) {
                if (res.success == 1) {
                    $(`.result_${id}`).html('<i class="fa fa-check text-green"></i>');
                    setTimeout(() => {
                        $(`.result_${id}`).html('');
                    }, 1000);
                }
            }
        });
    });
</script>
<script>
    function confirmationDelete(anchor) {
        swal({
            title: "Are you sure want to delete this user?",
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
    $('#type_select').select2();
    $('#block_unblock_filter').select2();
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

    function exportTasks(_this) {
        let _url = $(_this).data('href');
        window.location.href = _url;
    }
</script>
@endsection
