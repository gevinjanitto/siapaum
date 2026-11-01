<style>
    .data-card {
        background-color: #fff;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;

        .data-card-label {
            font-weight: bold;
            color: #333;
        }

        .data-card-value {
            color: #555;
        }
    }

</style>

@props(['label', 'value'])

<div class="d-flex justify-content-between data-card">
    <div class="data-card-label text-primary">{{ $label }}</div>
    <div class="data-card-value">{{ $value }}</div>
</div>
