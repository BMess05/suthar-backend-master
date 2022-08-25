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
                    <h3 class="mb-0">Store Managers&nbsp;&nbsp; <span class="badge badge-success">Total: {{ $users->total() }}</span></h3>
                </div>
                <div class="col text-right">
                    <a href="{{ route('addUser') }}" class="btn btn-sm btn-primary">Add New Manager</a>
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
                    <th scope="col">Stores count</th>
                    <th scope="col">Active / Inactive</th>
                    <th scope="col">Actions</th>
                    <th scope="col">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @php
                $count = 1;
                if ($users->hasPages()):
                $per_page = config('constants.admin.per_page') ?? 10;
                $page = $users->currentPage();
                $count = ($per_page * ($page - 1)) + 1;
                endif;
                @endphp
                @forelse($users as $user)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>{{ strlen($user['name']) > 20 ? substr($user['name'],0,20)."..." : $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>{{ $user->manager_stores->count() }}</td>
                    <td>
                        <div class="switch">
                            <input type="checkbox" class="active-inactive" data-id="{{ $user['id'] }}" {{ ($user['is_active'] == 1) ? 'checked' : '' }}>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('editUser', $user['id']) }}"><i class="fa fa-pen text-warning mr-3"></i></a>
                        <a onclick="javascript:confirmationDelete($(this));return false;" href="{{ route('deleteUser', $user['id']) }}"><i class="fa fa-trash text-danger mr-3"></i></a>
                        <a href="{{ route('viewUser', $user['id']) }}"><i class="fa fa-eye text-info mr-3"></i></a>
                    </td>
                    <td>
                        <a class="result_{{$user['id']}}">&nbsp;</a>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="8">No Users added yet</td>
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
                    if(isset($_GET['store'])) {
                        $append += ['store' => $_GET['store']];
                    }
                    ?>
                    {{$users->appends($append)->links('vendor.pagination.bootstrap-4', compact('users'))}}
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
    $('.active-inactive').bootstrapToggle({
      on: 'Active',
      off: 'Inactive'
    });
  });

$(document).on('change', '.active-inactive', function(e) {
    let id = $(this).data('id');
    let data = {
        "_token" : "{{csrf_token()}}",
        "id" : id
    };
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "{{ route('activeInactiveUser') }}",
        data: data,
        success: function(res) {
            if(res.success == 1) {
                $(`.result_${id}`).html('<i class="fa fa-check text-green"></i>');
                setTimeout(() => {
                    $(`.result_${id}`).html('');
                }, 1000);
            }
        }
    });
});
function confirmationDelete(anchor) {
    swal({
        title: "Are you sure want to delete this manager?",
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
</script>
@endsection