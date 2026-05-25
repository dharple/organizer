<div class="card card-click mb-3">
    <div class="card-header">
        {{ $box->getDisplayLabel() }}
    </div>
    <div class="card-body">
        <a class="card-link float-end" href="{{ route('box.edit', ['id' => $box->id]) }}">Edit</a>
        <p class="card-text">
            @if (!empty($box->description))
                @if (strlen($box->description) > 90)
                    <span class="flip d-inline visible">
                        {{ substr($box->description, 0, 90) }} ...
                    </span>
                    <span class="flip d-none invisible">
                        {{ $box->description }}
                    </span>
                @else
                    {{ $box->description }}
                @endif
                <br />
            @endif
            @if ($showLocation ?? false)
                @if ($box->location)
                    <em>{{ $box->location->getDisplayLabel() }}</em>
                @else
                    <em>Location Unknown</em>
                @endif
                <br />
            @endif
            @if (($showModel ?? false) && $box->boxModel)
                <em>{{ $box->boxModel->label }}</em>
            @endif
        </p>
    </div>
</div>
