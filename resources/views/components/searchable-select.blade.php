@props([
    'name',
    'id' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Seleccione...',
    'required' => false,
])

@php
    $id = $id ?? $name;
@endphp

<div class="searchable-select-wrapper" data-target="{{ $id }}">
    <div class="dropdown">
        <input type="text"
            class="form-control searchable-select-input shadow-none"
            id="{{ $id }}_search"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            value="{{ $selected ? ($options[$selected] ?? '') : '' }}"
            {{ $required ? 'required' : '' }}>
        <input type="hidden" name="{{ $name }}" id="{{ $id }}" value="{{ $selected }}">
        <ul class="dropdown-menu w-100 searchable-select-dropdown" aria-labelledby="{{ $id }}_search" style="max-height: 220px; overflow-y: auto;">
            <li><a class="dropdown-item searchable-select-option" data-value="" href="#">{{ $placeholder }}</a></li>
            @foreach($options as $value => $label)
                <li>
                    <a class="dropdown-item searchable-select-option {{ (string)$selected === (string)$value ? 'active' : '' }}"
                       data-value="{{ $value }}" href="#">{{ $label }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.searchable-select-wrapper').each(function() {
        var wrapper = $(this);
        var targetId = wrapper.data('target');
        var input = wrapper.find('.searchable-select-input');
        var hidden = wrapper.find('#' + targetId);
        var dropdown = wrapper.find('.searchable-select-dropdown');
        var items = dropdown.find('.searchable-select-option');

        // Filter on input
        input.on('keyup', function() {
            var val = $(this).val().toLowerCase();
            items.each(function() {
                var text = $(this).text().toLowerCase();
                $(this).closest('li').toggle(text.indexOf(val) !== -1);
            });
            if (!dropdown.is(':visible')) {
                input.dropdown('show');
            }
        });

        // Select item
        dropdown.on('click', '.searchable-select-option', function(e) {
            e.preventDefault();
            var value = $(this).data('value');
            var text = $(this).text();
            hidden.val(value);
            input.val(value ? text : '');
            items.removeClass('active');
            $(this).addClass('active');
            input.dropdown('hide');

            // Trigger change event on hidden input
            hidden.trigger('change');
        });

        // Prevent dropdown from closing on input click
        input.on('click', function(e) {
            e.stopPropagation();
        });

        // When dropdown hides, restore input text to selected value
        input.on('blur', function() {
            var selectedVal = hidden.val();
            if (selectedVal) {
                var selectedItem = items.filter('[data-value="' + selectedVal + '"]');
                if (selectedItem.length) {
                    input.val(selectedItem.text());
                }
            } else {
                input.val('');
            }
        });
    });
});
</script>
@endpush
