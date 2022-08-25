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
                    <h3 class="mb-0">Store Managers</h3>
                </div>
                <div class="col text-right">
                    <a href="{{ route('listUsers') }}" class="btn btn-sm btn-primary">Back</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <!-- Projects table -->
            <table class="table align-items-center table-flush text-center detail-table">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Name: </th>
                        <th class="man_val" scope="col">{{ $manager->name }}</th>
                    </tr>
                    <tr>
                        <th scope="col">Email: </th>
                        <th class="man_val" scope="col">{{ $manager->email }}</th>
                    </tr>
                    <tr>
                        <th scope="col">Phone: </th>
                        <th class="man_val" scope="col">{{ $manager->phone }}</th>
                    </tr>
                    <tr>
                        <th scope="col">Stores: </th>
                        <th class="man_val" scope="col">{{ $manager->manager_stores->count() }}</th>
                    </tr>
                </thead>
            </table>

            <hr>

            <table class="table align-items-center table-flush text-center">
            <thead class="thead-light">
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Stores</th>
                    <th scope="col"></th>
                </tr>
                <tr>
                    <th scope="col">Sr. No.</th>
                    <th scope="col">Name</th>
                    <th scope="col">City</th>
                </tr>
            </thead>
            <tbody>
                @php
                $count = 1;
                @endphp
                @forelse($manager->manager_stores as $mstore)
                @if($mstore->store)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>{{ $mstore->store->name }}</td>
                    <td>{{ $mstore->store->city }}</td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="8">No Users added yet</td>
                </tr>
                @endforelse
            </tbody>

            </table>
        </div>
    </div>
</div>
@endsection