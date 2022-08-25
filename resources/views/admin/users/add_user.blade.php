@extends('layouts.adminPanel')
@section('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
<style>
.not-allowed {
    cursor: not-allowed;
}
</style>
@endsection
@section('contents')
<div class="col-xl-12">
    <div class="card bg-secondary border-0 mb-0">
        <div class="card-header border-0">
            <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Add Manager</h3>
                @if($stores->count() == 0)
                <span class="badge badge-warning">No Stores available to assign any new Store manager.</span>
                @endif
            </div>
            <div class="col text-right">
                <a href="{{ route('listUsers') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
            </div>
        </div>
        <div class="card-body px-lg-5 py-lg-5">
            <!-- Projects table -->
            <form id="add-user-form" method="post" action="{{ route('saveUser') }}" enctype='multipart/form-data'>
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Phone Number" name="phone_number" value="{{ old('phone_number') }}" id="phone_number" required>
                </div>
                <div class="form-group">
                    <label for="stores">Select stores: </label>
                    <select name="stores[]" id="stores" class="form-control" multiple>
                        @forelse($stores as $store)
                        <option value="{{ $store->id }}" {{ ((old('stores')) && in_array($store->id, old('stores'))) ? 'selected' : '' }}>{{ $store->name . " - " . $store->city }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <button class="btn btn-primary my-4 {{ ($stores->count() == 0) ? 'not-allowed' : '' }}" {{ ($stores->count() == 0) ? 'disabled' : '' }}>Add Manager</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\UserRequest', '#add-user-form'); !!}

<script>
$(document).ready(function() {
    $('#stores').select2();
});

$(document).on('keypress', '#phone_number', function(e) {
    if(e.which === 32)
        return false;
});
</script>
@endsection