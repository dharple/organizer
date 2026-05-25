@php $boxCount = $location->boxes->count() @endphp
<div class="card mb-3">
    <div class="card-header">
        {{ $location->getDisplayLabel() }}
    </div>
    <div class="card-body">
        <p class="card-text">
            @if (!empty($location->description))
                {{ substr($location->description, 0, 90) }}@if (strlen($location->description) > 100) ...@endif
                <br />
            @endif
            @if ($location->hide_from_search)
                <em>This location is hidden from searches.</em>
            @endif
        </p>
        <a class="card-link" href="{{ route('location.edit', ['id' => $location->id]) }}">Edit</a>
        <a class="card-link" href="{{ route('box.search.location', ['id' => $location->id]) }}">{{ $boxCount }} Box{{ $boxCount != 1 ? 'es' : '' }}</a>
    </div>
</div>
