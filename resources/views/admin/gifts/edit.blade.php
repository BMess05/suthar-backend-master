@extends('layouts.adminPanel')
@section('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
<style>
img {
    display: block;
    max-width: 100%;
}
.preview {
    overflow: hidden;
    width: 160px;
    height: 160px;
    margin: 10px;
    border: 1px solid red;
}
.modal-lg{
    max-width: 1000px !important;
}
img.cropped_image {
    height: 84px;
}
.hide {
    display: none;
}
</style>
@endsection
@section('contents')
<div class="col-xl-12">
    <div class="card bg-secondary border-0 mb-0">
        <div class="card-header border-0">
            <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Edit Gift</h3>
            </div>
            <div class="col text-right">
                <a href="{{ route('listGifts') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
            </div>
        </div>
        <div class="card-body px-lg-5 py-lg-5">
            <!-- Projects table -->
            <form id="add-user-form" method="post" action="{{ route('updateGift', $gift->id) }}" enctype='multipart/form-data'>
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name" name="name" value="{{ old('name') ?? $gift->name }}">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control no_space" placeholder="Points" name="points" value="{{ old('points') ?? $gift->points }}" maxlength=10>
                </div>
                <div class="form-group">
                    <label for="photo">Photo: </label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="file" class="form-control image" name="photo">
                        </div>
                        <div class="col-md-6">
                            @if($gift->photo == null)
                            <img class="cropped_image img img-sm hide" id="cropped_image">
                            @else
                            <img src="{{ url('uploads/gifts/'.$gift->photo) }}" class="cropped_image img img-sm" id="cropped_image">
                            @endif
                            <input type="hidden" id="cropped_image_name" name="cropped_image_name" value="">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="stores">Select stores: </label>
                    <select name="stores[]" id="stores" class="form-control" multiple>
                        @php
                        $giftStores = old('stores') ?? $giftStores;
                        @endphp
                        @forelse($stores as $store)
                        <option value="{{ $store->id }}" {{ in_array($store->id, $giftStores) ? 'selected' : '' }}>{{ $store->name . " - " . $store->city }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <button class="btn btn-primary my-4">Update Gift</button>
            </form>
        </div>
    </div>
</div>

<!-- Cropping modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Crop Image Before Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-8">
                        <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
                        </div>
                        <div class="col-md-4">
                            <div class="preview"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop">Crop</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\GiftRequest', '#add-user-form'); !!}
<script>
$(document).ready(function() {
    $('#stores').select2();
});
$(document).on('keypress', '.no_space', function(e) {
    if(e.which === 32)
        return false;
});
var loadCategoryPic = function(event) {
    var output = document.getElementById('cp_preview');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src); // free memory
    }
};

let $modal = $('#modal');
let image = document.getElementById('image');
let cropper;
$(document).on("change", ".image", function(e){

    let files = e.target.files;
    let done = function (url) {
        image.src = url;
        $modal.modal({
            backdrop: 'static',
            keyboard: false
        });
    };
    let reader;
    let file;
    let url;
    if (files && files.length > 0) {
        file = files[0];
        if (URL) {
            done(URL.createObjectURL(file));
        } else if (FileReader) {
            reader = new FileReader();
                reader.onload = function (e) {
                done(reader.result);
            };
            reader.readAsDataURL(file);
        }
    }
});
$modal.on('shown.bs.modal', function () {
        cropper = new Cropper(image, {
        aspectRatio: 1,
        viewMode: 3,
        preview: '.preview'
    });
}).on('hidden.bs.modal', function () {
        cropper.destroy();
        cropper = null;
    });

    $("#crop").click(function(){
            canvas = cropper.getCroppedCanvas({
            width: 160,
            height: 160,
        });
    canvas.toBlob(function(blob) {
        url = URL.createObjectURL(blob);
        let reader = new FileReader();
        reader.readAsDataURL(blob);
        reader.onloadend = function() {
            let base64data = reader.result;
            $('#cropped_image_name').val(base64data);
            var output = document.getElementById('cropped_image');
            output.src = url;
            $('.cropped_image').removeClass('hide');
            $modal.modal('hide');
        }
    });
});


</script>


@endsection