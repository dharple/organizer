@extends('layouts.app')

@section('title', 'Organizer: ' . ($entity->label ?? 'New Box Model'))

@section('body')
    <div class="row mb-3 text-center">
        <div class="col">
            @if ($entity->id !== null)
                <h4>{{ $entity->label }}</h4>
            @else
                <h4>New Box Model</h4>
            @endif
        </div>
    </div>
    <form method="POST" action="{{ $entity->id ? route('box-model.update', ['id' => $entity->id]) : route('box-model.store') }}">
        @csrf
        <div class="mb-3">
            <label for="label" class="form-label">Label</label>
            <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $entity->label) }}" data-lpignore="true" required>
            @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="make" class="form-label">Make</label>
            <input type="text" class="form-control @error('make') is-invalid @enderror" id="make" name="make" value="{{ old('make', $entity->make) }}" data-lpignore="true">
            @error('make')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="model" class="form-label">Model</label>
            <input type="text" class="form-control @error('model') is-invalid @enderror" id="model" name="model" value="{{ old('model', $entity->model) }}" data-lpignore="true">
            @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="size" class="form-label">Size</label>
            <input type="text" class="form-control @error('size') is-invalid @enderror" id="size" name="size" value="{{ old('size', $entity->size) }}" data-lpignore="true">
            @error('size')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="color" class="form-label">Color</label>
            <input type="text" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $entity->color) }}" data-lpignore="true">
            @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="latch" class="form-label">Latch</label>
            <input type="text" class="form-control @error('latch') is-invalid @enderror" id="latch" name="latch" value="{{ old('latch', $entity->latch) }}" data-lpignore="true">
            @error('latch')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
@endsection
