@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'autofocus' => false,
    'autocomplete' => 'on',
    'icon' => '',
    'disabled' => false,
    'readonly' => false,
    'class' => '',
    'class_input' => '',
    'type' => 'text',
    'is_currency' => false,
])

<div class="form-group row mb-0 align-items-center {{ $class }}">
    @if ($label)
        <label class="text-primary text-xsm col-form-label fw-bold col-4 col-md-3 col-lg-2"
               for="{{ $name }}">{{ $label }}</label>
    @endif
    <div class="col-8 col-md-9 col-lg-10">
        <input @if ($required) required @endif
               @if ($disabled) disabled @endif
               @if ($readonly) readonly @endif
               @if ($autofocus) autofocus @endif
               aria-describedby="basic-addon1"
               autocomplete="{{ $autocomplete }}"
               class="form-control text-xsm @error($name) is-invalid @enderror {{ $class_input }}"
               id="{{ $name }}"
               name="{{ $name }}"
               placeholder="{{ $placeholder }}"
               type="{{ $type }}"
               value="{{ $value }}">
        @error($name)
            <div class="invalid-feedback">
                <i class="isax isax-info-circle"></i>
                {{ $message }}
            </div>
        @enderror
    </div>
</div>

@push('scripts')
    <script>
        @if ($type == 'date')
            flatpickr('#{{ $name }}', {
                altInput: true,
                altFormat: "j F Y",
                dateFormat: "d-m-Y",
                defaultDate: "{{ $value }}",
            });
        @endif
        @if ($is_currency)
            new AutoNumeric('#{{ $name }}', {
                currencySymbol: "Rp ",
                decimalCharacter: ",",
                digitGroupSeparator: ".",
                decimalPlaces: 2,
                unformatOnSubmit: true,
            });
        @endif
        @if ($type == 'daterange')
            $('#{{ $name }}').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf(
                            'month')
                    ],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf(
                        'year')]
                },
                // max 7 days
                maxSpan: {
                    days: 365
                },
                alwaysShowCalendars: true,
            });

            $('#{{ $name }}').val(moment().subtract(1, 'months').format('DD/MM/YYYY') + ' - ' + moment().format(
                'DD/MM/YYYY'));
            $('#{{ $name }}').data('daterangepicker').setStartDate(moment().subtract(1, 'months'));

            $('#{{ $name }}').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                        'DD/MM/YYYY'))
                    .trigger('change');
            });
        @endif
    </script>
@endpush
