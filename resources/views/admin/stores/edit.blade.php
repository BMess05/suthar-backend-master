@extends('layouts.adminPanel')
@section('contents')
<div class="col-xl-12">
    <div class="card bg-secondary border-0 mb-0">
        <div class="card-header border-0">
            <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Edit Store</h3>
            </div>
            <div class="col text-right">
                <a href="{{ route('listStores') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
            </div>
        </div>
        <div class="card-body px-lg-5 py-lg-5">
            <!-- Projects table -->
            <form id="add-user-form" method="post" action="{{ route('updateStore', $store['id']) }}" enctype='multipart/form-data'>
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name" name="name" value="{{ $store['name'] }}">
                </div>
                <div class="form-group">
                    <select class="form-control" id="districts" name="city">
                        <option disabled>--Select City--</option>
                        @foreach($cities as $city)
                        <option value="{{$city}}" {{ ($city == $store['city']) ? 'selected' : '' }}>{{$city}}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary my-4">Update Store</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
{!! JsValidator::formRequest('App\Http\Requests\StoreRequest', '#add-user-form'); !!}
<script>
$('#districts').select2();
</script>
@endsection