@props([
    'name',
    'label' => '',
    'setcol' => 'col-12',
    'options' => [],
    'placeholder' => 'Pilih...',
    'optionValue' => 'id',
    'optionLabel' => 'nama',
])

<div class="{{ $setcol }}">
    @if ($label)
        <label class="form-label" for="{{ $name }}">{!! $label !!}</label>
    @endif

    <select name="{{ $name }}" id="{{ $name }}" class="form-select @error($name) is-invalid @enderror"
        {{ $attributes }}>

        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $item)
            @php
                // Mendukung item berupa object maupun array
                $val = is_array($item) ? $item[$optionValue] : $item->$optionValue;
                $lbl = is_array($item) ? $item[$optionLabel] : $item->$optionLabel;
            @endphp
            <option value="{{ $val }}">{{ $lbl }}</option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
