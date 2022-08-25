@extends('layouts.adminPanel')
@section('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
@endsection
@section('contents')
<div class="col-xl-12">
    <div class="card bg-secondary border-0 mb-0">
        <div class="card-header border-0">
            <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Edit Manager</h3>
            </div>
            <div class="col text-right">
                <a href="{{ route('listUsers') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
            </div>
        </div>
        <div class="card-body px-lg-5 py-lg-5">
            <!-- Projects table -->
            <form id="add-user-form" method="post" action="{{ route('updateUser', $manager->id) }}" enctype='multipart/form-data'>
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name" name="name" value="{{ old('name') ?? $manager->name }}" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') ?? $manager->email }}" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Phone Number" name="phone_number" value="{{ old('phone_number') ?? $manager->phone }}" id="phone_number" required>
                </div>
                <div class="form-group">
                    <label for="stores">Select stores: </label>
                    @php
                    $selected_stores = old('stores') ?? $manager_stores;
                    @endphp
                    <select name="stores[]" id="stores" class="form-control" multiple>
                        @forelse($stores as $store)
                        <option value="{{ $store->id }}" {{ in_array($store->id, $selected_stores) ? 'selected' : '' }}>{{ $store->name . " - " . $store->city }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <button class="btn btn-primary my-4">Update Manager</button>
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