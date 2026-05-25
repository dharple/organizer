@extends('layouts.app')

@section('title', 'Organizer: Search Results')

@section('body')
    @if (empty($hideMessage))
        <div class="row mb-3">
            <div class="col">
                <div class="p-3 mb-2 bg-secondary text-white">
                    @if (isset($entity))
                        @if ($type === 'BoxModel')
                            Showing boxes for Box Model: {{ $entity->label }}
                        @elseif ($type === 'Location')
                            Showing boxes for Location: {{ $entity->getDisplayLabel() }}
                        @elseif ($type === 'Box')
                            Showing Box {{ $entity->getDisplayId() }}
                        @endif
                    @else
                        Search results for "{{ $query }}"
                    @endif
                </div>
            </div>
        </div>
    @endif
    @if (!empty($boxes))
        @foreach (collect($boxes)->chunk(3) as $row)
            <div class="row mb-lg-3">
                @foreach ($row as $box)
                    <div class="col-12 col-lg-4">
                        @include('partials.box-card', [
                            'box' => $box,
                            'showLocation' => $type !== 'Location',
                            'showModel' => $type !== 'BoxModel',
                        ])
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <div class="row mb-3">
            <div class="col">
                <div class="p-3 mb-2 bg-danger text-white">
                    No results found
                </div>
            </div>
        </div>
    @endif
@endsection
