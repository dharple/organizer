@extends('layouts.app')

@section('title', 'Organizer: ' . ($entity->label ?? 'New Location'))

@section('body')
    <div class="row mb-3 text-center">
        <div class="col">
            @if ($entity->id !== null)
                <h4>{{ $entity->getDisplayLabel() }}</h4>
            @else
                <h4>New Location</h4>
            @endif
        </div>
    </div>
    <form method="POST" action="{{ $entity->id ? route('location.update', ['id' => $entity->id]) : route('location.store') }}">
        @csrf
        <div class="mb-3">
            <label for="label" class="form-label">Label</label>
            <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $entity->label) }}" required>
            @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="parent_location_id" class="form-label">Parent Location</label>
            <select class="form-select @error('parent_location_id') is-invalid @enderror" id="parent_location_id" name="parent_location_id">
                <option value="">This is a Top Level Location</option>
                @foreach ($locations as $location)
                    @if ($entity->id === null || $location->id !== $entity->id)
                        <option value="{{ $location->id }}" {{ old('parent_location_id', $entity->parent_location_id) == $location->id ? 'selected' : '' }}>
                            {{ $location->getDisplayLabel() }}
                        </option>
                    @endif
                @endforeach
            </select>
            @error('parent_location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $entity->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input @error('hide_from_search') is-invalid @enderror" id="hide_from_search" name="hide_from_search" value="1" {{ old('hide_from_search', $entity->hide_from_search) ? 'checked' : '' }}>
            <label class="form-check-label" for="hide_from_search">Hide From Search</label>
            @error('hide_from_search')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
@endsection
