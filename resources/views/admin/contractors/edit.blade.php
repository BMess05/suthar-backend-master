@extends('layouts.adminPanel')
@section('contents')
<div class="col-xl-12">
    <div class="card bg-secondary border-0 mb-0">
        <div class="card-header border-0">
            <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Edit User</h3>
            </div>
            <div class="col text-right">
                <a href="{{ route('listContractors') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
            </div>
        </div>
        <div class="card-body px-lg-5 py-lg-5">
            <!-- Projects table -->
            <form id="add-user-form" method="post" action="{{ route('updateContractor', $contractor->id) }}" enctype='multipart/form-data'>
                @csrf
                <input type="hidden" name="generated" id="generated" value="{{ old('generated') }}">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name" name="name" value="{{ old('name') ?? $contractor['name'] }}" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') ?? $contractor['email'] }}" required>
                </div>
                <div class="form-group">
                    <label for="type">Type: </label>
                    <select name="type" class="form-control">
                        <option value="" selected disabled></option>
                        @foreach($contractor_types as $val => $type)
                        <option value="{{ $val }}" {{ ($val == $contractor['type']) ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="store">Select stores: </label>
                    <select name="store" id="stores" class="form-control">
                        <option value="" selected disabled></option>
                        @forelse($stores as $store)
                        <option value="{{ $store->id }}" {{ ($store->id == $contractor['store_id']) ? 'selected' : '' }}>{{ $store->name . " - " . $store->city }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Phone Number" name="phone" value="{{ old('phone') ?? $contractor['phone'] }}" id="phone_number" required>
                </div>
                <div class="form-group">
                    <textarea name="address" class="form-control" rows="3" placeholder="Address">{{ old('address') ?? $contractor['address'] }}</textarea>
                </div>
                <button class="btn btn-primary my-4">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
{!! JsValidator::formRequest('App\Http\Requests\ContractorRequest', '#add-user-form'); !!}
<script>
$(document).on('keypress', '#phone_number', function(e) {
    if(e.which === 32)
        return false;
});
</script>
@endsection