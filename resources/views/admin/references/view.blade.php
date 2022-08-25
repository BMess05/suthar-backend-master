@extends('layouts.adminPanel')
@section('head')
<style>
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
                    <h3 class="mb-0">References</h3>
                </div>
                <div class="col text-right">
                    <a href="{{ route('listReferences') }}" class="btn btn-sm btn-primary">Back</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <!-- Projects table -->
            <table class="table align-items-center table-flush text-center">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Name: </th>
                        <th class="man_val" scope="col">{{ $reference->full_name }}</th>
                    </tr>
                    <tr>
                        <th scope="col">Email: </th>
                        <th class="man_val" scope="col">{{ ($reference->email == "") ? 'NA' : $reference->email }}</th>
                    </tr>
                    <tr>
                        <th scope="col">Phone: </th>
                        <th class="man_val" scope="col">{{ $reference->phone_number }}</th>
                    </tr>
                    <tr>
                        <th scope="col">Address: </th>
                        <th class="man_val" scope="col">{{ $reference->address }}</th>
                    </tr>
                    <tr>
                        <th scope="col">Status: </th>
                        <th class="man_val" scope="col"><span class="current_status">{{ $reference->status }}</span></th>
                    </tr>
                    <tr>
                        <th scope="col">Building Type: </th>
                        <th class="man_val" scope="col"><span>{{ $reference->building_type }}</span></th>
                    </tr>
                    <tr>
                        <th scope="col">State: </th>
                        <th class="man_val" scope="col"><span>{{ $reference->state }}</span></th>
                    </tr>
                    <tr>
                        <th scope="col">City: </th>
                        <th class="man_val" scope="col"><span>{{ $reference->city }}</span></th>
                    </tr>
                    <tr>
                        <th scope="col">Landmark: </th>
                        <th class="man_val" scope="col"><span>{{ ($reference->landmark == "") ? 'NA' : $reference->landmark }}</span></th>
                    </tr>
                    <tr>
                        <th scope="col">Area in square ft: </th>
                        <th class="man_val" scope="col"><span>{{ $reference->area_in_sqft }}</span></th>
                    </tr>
                    <tr>
                        <th scope="col">Required Frames: </th>
                        <th class="man_val" scope="col"><span>{{ $reference->frames_count }}</span></th>
                    </tr>
                    <tr>
                        <th scope="col">Contractor Name: </th>
                        <th class="man_val" scope="col"><a target="_blank" href="{{ route('viewContractor', $reference->contractor->id) }}">{{ $reference->contractor->name ?? '' }}</a></th>
                    </tr>
                    @if((\Auth::user()->role == 1) &&($reference->status == 'Pending'))
                    <tr class="change_status_block">
                        <th scope="col">Change status: </th>
                        <th class="man_val" scope="col">
                            <button class="btn btn-primary btn-sm btn-accept change-status" data-ref="{{ $reference->id }}" data-status="In Progress">Accept</button>
                            <button class="btn btn-primary btn-sm btn-reject change-status" data-ref="{{ $reference->id }}" data-status="Rejected">Reject</button>
                        </th>
                    </tr>
                    @endif
                    <tr class="pointsRows @if($reference->status != 'Completed') hide @endif">
                        <th scope="col">Points: </th>
                        <th class="man_val" scope="col"><span class="ref_points">{{ $reference->point->points ?? '' }}</span></th>
                    </tr>
                    {{--
                    <tr class="pointsRows  @if($reference->status != 'Completed') hide @endif">
                        <th scope="col">Points added by: </th>
                        <th class="man_val" scope="col"><span class="ref_points_by">{{ $reference->point->store_manager->name ?? '' }}</span></th>
                    </tr>
                    --}}
                </thead>
            </table>
            @if(\Auth::user()->role == 1)
            <hr>
            <form class="addPointForm">
                <table class="table align-items-center table-flush text-center add_points_block @if($reference->status != 'In Progress') hide @endif">
                    <thead>
                        <tr>
                            <th>Add Points: </th>
                            <th>
                                <input type="number" class="form-control points_input" placeholder="E.g 100" min=1 onkeypress='return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)'>
                            </th>
                        </tr>
                        <tr>
                            <th>&nbsp;</th>
                            <th class="text-left"><button type="button" class="btn btn-primary btn-sm btn-add-points" data-ref="{{ $reference->id }}">Add</button></th>
                        </tr>
                    </thead>
                </table>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
// document.querySelector(".points_input").addEventListener("keypress", function (evt) {
//     if (evt.which != 190 && evt.which != 8 && evt.which < 48 || evt.which > 57) // && evt.which != 0
//     {
//         evt.preventDefault();
//     }
// });
    $(document).on('click', '.change-status', function(e) {
        let id = $(this).data('ref');
        let status = $(this).data('status');
        let data = {
            "_token" : "{{csrf_token()}}",
            "id" : id,
            "status" : status
        };
        $('.change_status_block').addClass('hide');
        $(".loader-block").removeClass("hide");
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('refChangeStatus') }}",
            data: data,
            success: function(res) {
                if(res.success == 1) {
                    $(`.current_status`).html(status);
                    $(`.current_status`).addClass('text-success');
                    if(status == "In Progress") {
                        $('.add_points_block').removeClass('hide');
                        $('.change_status_block').remove();
                    }   else {
                        $('.add_points_block').addClass('hide');
                        $('.change_status_block').remove();
                    }
                    toastr.success('Referece status updated.');
                }
                $(".loader-block").addClass("hide");
            }
        });
    });

    $(document).on('click', '.btn-add-points', function() {
        let id = $(this).data('ref');
        let points = $('.points_input').val();
        if($.trim(points) == "") {
            toastr.warning('Invalid Points!');
            return false;
        }
        points = parseInt(points);
        let data = {
            "_token" : "{{csrf_token()}}",
            "id" : id,
            "points" : points
        };
        $('.addPointForm').addClass('hide');
        $(".loader-block").removeClass("hide");
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('refAddPoints') }}",
            data: data,
            success: function(res) {
                if(res.success == 1) {
                    $(`.current_status`).html('Completed');
                    $(`.current_status`).addClass('text-success');
                    $('.ref_points').html(points);
                    $('.ref_points_by').html("{{\Auth::user()->name}}");
                    $('.ref_points').addClass('text-green');
                    $('.ref_points_by').addClass('text-green');
                    $('.change_status_block').remove();
                    $('.addPointForm').remove();
                    $('.pointsRows').removeClass('hide');
                    toastr.success(res.message);
                }   else {
                    toastr.warning(res.message);
                }
                $('.addPointForm').removeClass('hide');
                $(".loader-block").addClass("hide");
            }
        });
    });

});

</script>
@endsection