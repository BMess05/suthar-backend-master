@extends('layouts.adminPanel')
@section('contents')
<div class="col-xl-12">
    <div class="card">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Gifts&nbsp;&nbsp; <span class="badge badge-success">Total: {{ $gifts->total() }}</span></h3>

                </div>
                <div class="col text-right">
                    <a href="{{ route('addGift') }}" class="btn btn-sm btn-primary">Add New Gift</a>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-12">
                    <form action="{{ route('listGifts') }}" id="form-filter" autocomplete="off">
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
                                    <label for="stores">Stores: </label>
                                    <select class="form-control" id="store_select" name="stores">
                                        <option value="" selected disabled>All</option>
                                        @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ ($store->id == $store_id) ? 'selected' : '' }}>{{ $store->name . " (" . $store->city . ")" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <button class="btn btn-sm btn-primary btn-filter mt-5">Filter</button>
                                <a href="{{ route('listGifts') }}" class="btn btn-sm btn-primary mt-5">Reset</a>
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
                    <th scope="col">Points</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                $count = 1;
                if ($gifts->hasPages()):
                $per_page = config('constants.admin.per_page') ?? 10;
                $page = $gifts->currentPage();
                $count = ($per_page * ($page - 1)) + 1;
                endif;
                @endphp
                @forelse($gifts as $gift)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>{{ strlen($gift['name']) > 20 ? substr($gift['name'],0,20)."..." : $gift['name'] }}</td>
                    <td>{{ $gift['points'] }}</td>
                    <td>
                        <a href="{{ route('editGift', $gift['id']) }}"><i class="fa fa-pen text-warning mr-3"></i></a>
                        <a onclick="javascript:confirmationDelete($(this));return false;" href="{{ route('deleteGift', $gift['id']) }}"><i class="fa fa-trash text-danger mr-3"></i></a>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="8">No gifts added yet</td>
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
                    if(isset($_GET['stores'])) {
                        $append += ['stores' => $_GET['stores']];
                    }
                    ?>
                    {{$gifts->appends($append)->links('vendor.pagination.bootstrap-4', compact('gifts'))}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
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