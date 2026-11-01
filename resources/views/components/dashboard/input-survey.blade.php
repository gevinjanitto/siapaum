@props(['number', 'kuesioner', 'value' => ''])

<div class="form-group mb-4 pb-1 align-items-center">
    <h6>{{ $number }}. {{ $kuesioner->pertanyaan }}</h6>
    <div class="row justify-content-between align-items-center"
         role="group">
        @foreach ($kuesioner->kuesionerOpsi as $opsi)
            <div class="col-6 col-md-3">
                <input autocomplete="off"
                       class="btn-check"
                       id="option{{ $kuesioner->id }}-{{ $opsi->id }}"
                       name="kuesioner[{{ $kuesioner->id }}]"
                       required
                       type="radio"
                       value="{{ $opsi->id }}">
                <label class="btn btn-outline-danger w-100 text-xsm mb-3"
                       for="option{{ $kuesioner->id }}-{{ $opsi->id }}">{{ $opsi->opsi }}
                </label>
            </div>
        @endforeach
    </div>
</div>
