@props(['name', 'label', 'type' => 'text', 'autocomplete' => null, 'inputmode' => null, 'class' => ''])
<label class="field {{ $class }}">
    <span>{{ $label }}</span>
    <input type="{{ $type }}" wire:model.blur="{{ $name }}" @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif @if($inputmode) inputmode="{{ $inputmode }}" @endif aria-describedby="{{ $name }}-error">
    @error($name)<small id="{{ $name }}-error" class="field-error" role="alert">{{ $message }}</small>@enderror
</label>
