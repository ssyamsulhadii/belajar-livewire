@props(['name', 'label' => '', 'setcol' => 'col-12', 'type' => 'text', 'accept' => '', 'phr' => ''])

<div class="{{ $setcol }}">
    @if ($label)
        <label class="form-label" for="{{ $name }}">{!! $label !!}</label>
    @endif

    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" accept="{{ $accept }}"
        placeholder="{{ $phr }}" class="form-control @error($name) is-invalid @enderror" {{ $attributes }}>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
