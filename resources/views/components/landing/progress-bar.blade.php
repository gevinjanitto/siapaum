@props(["value" => 0, "label" => ""])

<div {{ $attributes->merge(["class" => "progress-bar"]) }}>
    <div class="progress-bar-fill" style="width: {{ ($value / 100) * 100 }}%;"></div>
    <div class="progress-bar-label">{{ $label }}</div>
</div>
