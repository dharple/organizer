@extends('layouts.app')

@section('title', 'Organizer: All Locations')

@section('body')
    <div class="row mb-3">
        <div class="col text-center">
            All Locations
        </div>
    </div>
    @foreach ($locations->chunk(3) as $row)
        <div class="row mb-lg-3">
            @foreach ($row as $location)
                <div class="col-12 col-lg-4">
                    @include('partials.location-card', ['location' => $location])
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
