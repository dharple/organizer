@php $boxCount = $boxModel->boxes->count() @endphp
<div class="card mb-3">
    <div class="card-header">
        {{ $boxModel->label }}
    </div>
    <div class="card-body">
        <a class="card-link" href="{{ route('box-model.edit', ['id' => $boxModel->id]) }}">Edit</a>
        <a class="card-link" href="{{ route('box.search.model', ['id' => $boxModel->id]) }}">{{ $boxCount }} Box{{ $boxCount != 1 ? 'es' : '' }}</a>
    </div>
</div>
