@extends('layouts.adminPanel')
@section('contents')
<div class="col-xl-12">
    <div class="card">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Stores</h3>
                </div>
                <div class="col text-right">
                    <a href="{{ route('addStore') }}" class="btn btn-sm btn-primary">Add New Store</a>
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
                    <th scope="col">Location</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                $count = 1;
                if ($stores->hasPages()):
                $per_page = config('constants.admin.per_page') ?? 10;
                $page = $stores->currentPage();
                $count = ($per_page * ($page - 1)) + 1;
                endif;
                @endphp
                @forelse($stores as $store)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>{{ strlen($store['name']) > 20 ? substr($store['name'],0,20)."..." : $store['name'] }}</td>
                    <td>{{ $store['city'] }}</td>
                    <td>
                        <a href="{{ route('editStore', $store['id']) }}"><i class="fa fa-pen text-warning mr-3"></i></a>
                        <a onclick="javascript:confirmationDelete($(this));return false;" href="{{ route('deleteStore', $store['id']) }}"><i class="fa fa-trash text-danger mr-3"></i></a>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="8">No stores added yet</td>
                </tr>
                @endforelse
            </tbody>

            </table>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="pagination justify-content-end">
                    {{$stores->links('vendor.pagination.bootstrap-4', compact('stores'))}}
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
        title: "Are you sure want to delete this store?",
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