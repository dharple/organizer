@extends('layouts.app')

@section('title', 'Organizer')

@section('body')
    @if ($recentBoxes->isNotEmpty())
        <div class="row mb-3 justify-content-center">
            <div class="col d-grid">
                <a class="btn btn-primary-outline border border-primary rounded text-primary" href="{{ route('box.recent') }}" role="button">Recently Added or Changed</a>
            </div>
        </div>
        <div class="row mb-lg-3">
            @foreach ($recentBoxes as $box)
                <div class="col-12 col-lg-4">
                    @include('partials.box-card', ['box' => $box, 'showLocation' => true, 'showModel' => true])
                </div>
            @endforeach
        </div>
    @endif
    <div class="row mb-3 justify-content-center">
        <div class="col d-grid">
            <a class="btn btn-primary-outline border border-primary rounded text-primary" href="{{ route('location.index') }}" role="button">Locations with Boxes</a>
        </div>
    </div>
    <div class="accordion">
        @foreach ($locations as $location)
            @php $accordionTarget = 'children-' . $location->id @endphp
            <div class="row mb-3">
                <div class="col d-grid">
                    <button class="btn btn-primary btn-accordion" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $accordionTarget }}">
                        {{ $location->getDisplayLabel() }}
                    </button>
                </div>
            </div>
            <div class="container collapse accordionTarget" id="{{ $accordionTarget }}">
                @foreach ($location->boxes->chunk(3) as $row)
                    <div class="row mb-lg-3">
                        @foreach ($row as $box)
                            <div class="col-12 col-lg-4">
                                @include('partials.box-card', ['box' => $box, 'showLocation' => false, 'showModel' => true])
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection
