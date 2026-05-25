@extends('layouts.app')

@section('title', 'Organizer: ' . ($entity->label ?? 'New Box'))

@section('body')
    <div class="row mb-3 text-center">
        <div class="col">
            @if ($entity->id !== null)
                <h4>{{ $entity->getDisplayLabel() }}</h4>
            @else
                <h4>New Box</h4>
            @endif
        </div>
    </div>
    @if ($entity->id !== null)
        <div class="mb-3">
            <label for="boxNumber">Box Number</label>
            <input type="text" class="form-control" disabled id="boxNumber" placeholder="Automatically Assigned" value="{{ $entity->getDisplayId() }}">
        </div>
    @endif
    <form method="POST" action="{{ $entity->id ? route('box.update', ['id' => $entity->id]) : route('box.store') }}">
        @csrf
        <div class="mb-3">
            <label for="label" class="form-label">Label</label>
            <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $entity->label) }}" required>
            @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $entity->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="location_id" class="form-label">Location</label>
            <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id">
                <option value="">Choose a location...</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}" {{ old('location_id', $entity->location_id) == $location->id ? 'selected' : '' }}>
                        {{ $location->getDisplayLabel() }}
                    </option>
                @endforeach
            </select>
            @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="box_model_id" class="form-label">Box Model</label>
            <select class="form-select @error('box_model_id') is-invalid @enderror" id="box_model_id" name="box_model_id">
                <option value="">Choose a model...</option>
                @foreach ($boxModels as $boxModel)
                    <option value="{{ $boxModel->id }}" {{ old('box_model_id', $entity->box_model_id) == $boxModel->id ? 'selected' : '' }}>
                        {{ $boxModel->label }}
                    </option>
                @endforeach
            </select>
            @error('box_model_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
@endsection
