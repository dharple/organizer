@extends('layouts.app')

@section('title', 'Organizer: Data Export')

@section('body')
    <div class="row mb-3 text-center">
        <div class="col">
            <h4>Data Export</h4>
        </div>
    </div>
    <form method="POST" action="{{ route('bulk.export.submit') }}">
        @csrf
        <div class="mb-3">
            <label for="format" class="form-label">File Format</label>
            <select class="form-select @error('format') is-invalid @enderror" id="format" name="format">
                <option value="csv"  {{ old('format') === 'csv'  ? 'selected' : '' }}>CSV</option>
                <option value="html" {{ old('format') === 'html' ? 'selected' : '' }}>HTML</option>
                <option value="json" {{ old('format') === 'json' ? 'selected' : '' }}>JSON</option>
                <option value="ods"  {{ old('format') === 'ods'  ? 'selected' : '' }}>ODS</option>
                <option value="xlsx" {{ old('format') === 'xlsx' ? 'selected' : '' }}>XLSX</option>
                <option value="xml"  {{ old('format') === 'xml'  ? 'selected' : '' }}>XML</option>
                <option value="yaml" {{ old('format') === 'yaml' ? 'selected' : '' }}>YAML</option>
            </select>
            @error('format')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Export Type</label>
            <div class="@error('type') is-invalid @enderror">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type" id="type_simple" value="simple" {{ old('type', 'simple') === 'simple' ? 'checked' : '' }}>
                    <label class="form-check-label" for="type_simple">
                        Simple Box Export (box card contents only)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type" id="type_full" value="full" {{ old('type', 'simple') === 'full' ? 'checked' : '' }}>
                    <label class="form-check-label" for="type_full">
                        Full Export (supports JSON, XML, YAML)
                    </label>
                </div>
            </div>
            @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Export</button>
    </form>
@endsection
