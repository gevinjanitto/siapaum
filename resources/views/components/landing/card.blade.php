<style>
    .landing-card {
        background-color: #fff;
        padding: 24px;
        border-radius: 40px;
        /* box-shadow: 0px 1.75px 1.94px 0px rgba(0, 0, 0, 0.02), 0px 4.2px 4.66px 0px rgba(0, 0, 0, 0.03), 0px 7.9px 8.78px 0px rgba(0, 0, 0, 0.04), 0px 14.1px 15.66px 0px rgba(0, 0, 0, 0.04), 0px 26.36px 29.29px 0px rgba(0, 0, 0, 0.05), 0px 63.11px 70.12px 0px rgba(0, 0, 0, 0.07); */

    }
</style>

<div {{ $attributes->merge(['class' => 'landing-card shadow-sm']) }}>
    {{ $slot }}
</div>
