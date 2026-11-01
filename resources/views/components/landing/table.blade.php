<style>
    .data-table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 1rem;
        overflow: hidden;
        background-color: #fff;

        thead {
            background-color: #FCFCFD;
        }

        th, td {
            font-weight: 400;
            padding: 0.75rem 2rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    }
</style>

@props(["header" => ""])

<table class="data-table">
    <thead>
        {{ $header }}
    </thead>
    <tbody>
        {{ $slot }}
    </tbody>
</table>
