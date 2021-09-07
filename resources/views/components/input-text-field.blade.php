<div class="form-splashr">
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}" placeholder=" " {{ $required ? 'required' : ''}} {{ $autofocus ? 'autofocus' : ''}} {{ $autocomplete != '' ? 'autocomplete='. $autocomplete : '' }}>
    <label for="{{ $id }}">
        <span class="content-name">{{ $label }}</span>
    </label>
</div>