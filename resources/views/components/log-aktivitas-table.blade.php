<div class="bg-white p-3 rounded-lg shadow-sm mb-4">
    <div class="accordion" id="logAktivitasAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button btn-xsm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Show/Hide Log Aktivitas
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#logAktivitasAccordion">
                <div class="accordion-body">
                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Aktivitas</th>
                                    <th>Nama</th>
                                    <th>Waktu</th>
                                    <th>Jarak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($log_aktivitas as $activity)
                                    <tr>
                                        <td>{{ $activity->description }}</td>
                                        <td>{{ $activity->causer ? $activity->causer->name : 'System' }}</td>
                                        <td>{{ Carbon\Carbon::parse($activity->created_at)->setTimezone('GMT+8')->format('d-m-Y H:i:s') }}</td>
                                        <td>{{ $delays[$activity->id] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
