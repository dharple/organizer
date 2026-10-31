@forelse ($boxes->chunk(3) as $row)
    <div class="row mb-lg-3">
        @foreach ($row as $box)
            <div class="col-12 col-lg-4">
                @include('partials.box-card', ['box' => $box, 'showLocation' => false, 'showModel' => true])
            </div>
        @endforeach
    </div>
@empty
    <p class="text-center text-muted">No boxes in this location.</p>
@endforelse
