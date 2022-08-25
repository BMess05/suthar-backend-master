@extends('layouts.adminPanel')
@section('head')
<style>
.custom-header {
    padding: 1.25rem 1.25rem 0 1.25rem !important;
}
.custom-body {
    padding: 0 1.5rem 1.5rem 1.5rem !important;
}
.man_val {
    text-transform: unset !important;
}
</style>
@endsection
@section('contents')
<div class="col-xl-12">
    <div class="card">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">User Details</h3>
                </div>
                <div class="col text-right">
                    <a href="{{ route('listContractors') }}" class="btn btn-sm btn-primary">Back</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <!-- Projects table -->
            <table class="table align-items-center table-flush text-center">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Name: </th>
                        <th class="man_val" scope="col">{{ $contractor->name }}</th>
                    </tr>
                    <tr>
                        <th scope="col">Email: </th>
                        <th class="man_val" scope="col">{{ $contractor->email }}</th>
                    </tr>
                    <tr>
                        <th scope="col">Phone: </th>
                        <th class="man_val" scope="col">{{ $contractor->phone }}</th>
                    </tr>
                    <tr>
                        <th scope="col">Address: </th>
                        <th class="man_val" scope="col">{{ $contractor->address }}</th>
                    </tr>
                    <tr>
                        <th scope="col">Total Points: </th>
                        <th class="man_val" scope="col">{{ $contractor->total_points }}</th>
                    </tr>
                    @if(\Auth::user()->role == 1)
                    <tr>
                        <th scope="col">Password: </th>
                        <th class="man_val" scope="col"><button class="btn btn-primary btn-sm reset-password-btn" data-contractor="{{ $contractor->id }}">Reset</button></th>
                    </tr>
                    @endif
                </thead>
            </table>
            <hr>
        </div>
    </div>
</div>



<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header custom-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body custom-body text-center">
            <h3>Password: </h3>
            <h3 id="password-str"></h3>
            <span class="text-success cpy-success"></span>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">OK</button>
            <button type="button" class="btn btn-primary btn-sm cpy-pswd-btn">Copy Password</button>
        </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
$(document).on('click', '.reset-password-btn', function(e) {
    let id = $(this).data('contractor');
    let data = {
        "_token" : "{{ csrf_token() }}",
        "id" : id
    };
    $(".loader-block").removeClass("hide");
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "{{ route('contractorPasswordReset') }}",
        data: data,
        success: function(res) {
            if(res.success == 1) {
                $(".loader-block").addClass("hide");
                $("#password-str").text(res.passsword);
                $('.cpy-success').html('');
                $('#passwordModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                // $(`.result_${id}`).html('<i class="fa fa-check text-green"></i>');
                // setTimeout(() => {
                //     $(`.result_${id}`).html('');
                // }, 1000);
            }
            $(".loader-block").addClass("hide");
        }
    });
});
$(document).on('click', '.cpy-pswd-btn', function(e) {
    let ps = $('#password-str').text();
    navigator.clipboard.writeText(ps);
    $('.cpy-success').html('Password copied successfully.');
});

</script>
@endsection