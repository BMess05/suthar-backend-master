{{--
@if(session('status'))
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-{{ Session::get('status') }}" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            @if(!is_null(Session::get('block_no'))) Input {{Session::get('block_no')}}: @endif{{ Session::get('message') }}
        </div>
    </div>
</div>
@endif
--}}