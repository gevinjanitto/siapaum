apt-get install libreoffice --no-install-recommends
sudo apt-get install libreoffice-java-common default-jre
export HOME=/tmp

## Menjalankan dengan Docker

Prasyarat: Docker Desktop dengan Docker Compose.

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

Akses layanan:

- Aplikasi: http://localhost:8080
- Vite: http://localhost:5173
- Mailpit: http://localhost:8025
- MySQL dari host: `127.0.0.1:3308`

Perintah Laravel dijalankan melalui container aplikasi, misalnya:

```bash
docker compose exec app php artisan test
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

Untuk menghentikan container tanpa menghapus database:

```bash
docker compose down
```

Gunakan `docker compose down -v` hanya jika ingin sekaligus menghapus seluruh data MySQL Docker.

## Sebelum Sinkronisasi, Jalankan Query Berikut

```sql
DELETE
FROM
	kelengkapan_permohonan
WHERE
	kelengkapan_permohonan.id_permohonan IN (
	SELECT
		*
	FROM
		(
		SELECT DISTINCT
			( b.id_permohonan )
		FROM
			permohonan a
			RIGHT JOIN kelengkapan_permohonan b ON a.id = b.id_permohonan
		WHERE
			a.id IS NULL UNION
		SELECT DISTINCT
			( b.id_permohonan )
		FROM
			permohonan a
			RIGHT JOIN detail_permohonan b ON a.id = b.id_permohonan
		WHERE
			a.id IS NULL
		) AS c
	);
```

```sql
DELETE
FROM
	detail_permohonan
WHERE
	detail_permohonan.id_permohonan IN (
	SELECT
		*
	FROM
		(
		SELECT DISTINCT
			( b.id_permohonan )
		FROM
			permohonan a
			RIGHT JOIN kelengkapan_permohonan b ON a.id = b.id_permohonan
		WHERE
			a.id IS NULL UNION
		SELECT DISTINCT
			( b.id_permohonan )
		FROM
			permohonan a
			RIGHT JOIN detail_permohonan b ON a.id = b.id_permohonan
		WHERE
			a.id IS NULL
		) AS c
	);
```

```sql
DELETE
FROM
	alur_permohonan
WHERE
	id_user = 16
```

```sql
DELETE
FROM
	alur_permohonan
WHERE
	alur_permohonan.id_permohonan IN (
	SELECT
		*
	FROM
		(
		SELECT DISTINCT
			( b.id_permohonan )
		FROM
			permohonan a
			RIGHT JOIN alur_permohonan b ON a.id = b.id_permohonan
		WHERE
			a.id IS NULL
		) AS c
	);
```

```sql
DELETE
FROM
	kuisioner
WHERE
	kuisioner.id_permohonan IN (
	SELECT
		*
	FROM
		(
		SELECT DISTINCT
			( b.id_permohonan )
		FROM
			permohonan a
			RIGHT JOIN kuisioner b ON a.id = b.id_permohonan
		WHERE
			a.id IS NULL
		) AS c
	);
```

```sql
DELETE
FROM
	alur_permohonan
WHERE
	alur_permohonan.id_permohonan IN (
	SELECT
		*
	FROM
		(
		SELECT DISTINCT
			( b.id_permohonan )
		FROM
			permohonan a
			RIGHT JOIN alur_permohonan b ON a.id = b.id_permohonan
		WHERE
			a.id IS NULL
			or a.`status` = 'Tidak Aktif'
		) AS c
	);
```

```sql
DELETE
FROM
	kelengkapan_permohonan
WHERE
	kelengkapan_permohonan.id_permohonan IN (
	SELECT
		*
	FROM
		(
		SELECT DISTINCT
			( b.id_permohonan )
		FROM
			permohonan a
			RIGHT JOIN kelengkapan_permohonan b ON a.id = b.id_permohonan
		WHERE
			a.id IS NULL
			or a.`status` = 'Tidak Aktif'
		) AS c
	);
```

```sql

DELETE
FROM
	reklame_pembayaran
WHERE
	reklame_pembayaran.id_permohonan IN (
	SELECT
		*
	FROM
		(
		SELECT DISTINCT
			( b.id_permohonan )
		FROM
			permohonan a
			RIGHT JOIN reklame_pembayaran b ON a.id = b.id_permohonan
		WHERE
			a.id IS NULL
		) AS c
	);
```

```sql

UPDATE kuesioners
SET pendidikan = 'SMA/SMK'
WHERE
	pendidikan = 'SMA';
UPDATE kuesioners
SET pendidikan = 'DIPLOMA'
WHERE
	pendidikan = 'DIPL';
```

$permohonan = Permohonan::with('pembayaranReklame')->where('jenis_izin_id', 9)->get();
foreach ($permohonan as $permohonans) {
    $no_skpd = $permohonans->pembayaranReklame->nomor_skpd;
    $permohonans->kelengkapanPermohonan()->create([
        'label' => 'Nomor SKPD',
        'tipe' => 'text',
        'kode_isian' => 'NO_SKPD',
        'value' => $no_skpd,
    ]);
}

php artisan generate:ulid-jenis-izin

php artisan kuesioner:normalization
