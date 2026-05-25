@extends('layouts.app')

@section('title', 'Organizer: All Boxes')

@section('body')
    <div class="row mb-3">
        <div class="col text-center">
            {{ $title }}
        </div>
    </div>
    @foreach ($boxes->chunk(3) as $row)
        <div class="row mb-lg-3">
            @foreach ($row as $box)
                <div class="col-12 col-lg-4">
                    @include('partials.box-card', ['box' => $box, 'showLocation' => true, 'showModel' => true])
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
