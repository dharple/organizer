@extends('layouts.app')

@section('title', 'Organizer: All Box Models')

@section('body')
    <div class="row mb-3">
        <div class="col text-center">
            All Box Models
        </div>
    </div>
    @foreach ($boxModels->chunk(3) as $row)
        <div class="row mb-lg-3">
            @foreach ($row as $boxModel)
                <div class="col-12 col-lg-4">
                    @include('partials.box-model-card', ['boxModel' => $boxModel])
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
