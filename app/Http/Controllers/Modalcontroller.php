<?php

namespace App\Http\Controllers;

use App\Models\Modal;
use App\Models\ModalCicilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModalController extends Controller
{
    // ---------------------------------------------------------------
    // INDEX
    // ---------------------------------------------------------------
    public function index(Request $request)
    {
        $query = Modal::withTrashed(false)->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_pinjaman', 'like', "%{$request->search}%")
                    ->orWhere('nama_pemberi_pinjaman', 'like', "%{$request->search}%");
            });
        }

        $modals = $query->paginate(10)->withQueryString();

        $summary = [
            'total_pinjaman'   => Modal::sum('jumlah_pinjaman'),
            'total_pencairan'  => Modal::sum('nominal_pencairan'),
            'total_kewajiban'  => Modal::where('status', 'aktif')->sum('sisa_kewajiban'),
            'total_lunas'      => Modal::where('status', 'lunas')->count(),
            'total_aktif'      => Modal::where('status', 'aktif')->count(),
            'total_macet'      => Modal::where('status', 'macet')->count(),
        ];

        return view('modals.index', compact('modals', 'summary'));
    }

    // ---------------------------------------------------------------
    // STORE
    // ---------------------------------------------------------------
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pemberi_pinjaman' => 'required|string|max:255',
            'jenis_pinjaman'        => 'required|in:bank,koperasi,perorangan,lembaga_keuangan,lainnya',
            'jumlah_pinjaman'       => 'required|numeric|min:1',
            'nominal_pencairan'     => 'required|numeric|min:1',
            'total_bunga'           => 'required|numeric|min:0',
            'tenor'                 => 'required|integer|min:1',
            'satuan_tenor'          => 'required|in:hari,minggu,bulan,tahun',
            'tanggal_pinjaman'      => 'required|date',
            'tanggal_pencairan'     => 'required|date',
            'tanggal_jatuh_tempo'   => 'required|date|after:tanggal_pencairan',
            'status'                => 'sometimes|in:aktif,lunas,macet,restrukturisasi',
            'keterangan'            => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $hitung = self::hitungCicilanNominal(
                $validated['jumlah_pinjaman'],
                $validated['total_bunga'],
                $validated['tenor']
            );

            $modal = Modal::create([
                ...$validated,
                'kode_pinjaman'       => Modal::generateKode(),
                'total_bunga'         => $validated['total_bunga'],
                'total_kewajiban'     => $hitung['total_kewajiban'],
                'cicilan_per_periode' => $hitung['cicilan_per_periode'],
                'sisa_kewajiban'      => $hitung['total_kewajiban'],
                'status'              => $validated['status'] ?? 'aktif',
                'sudah_dicairkan'     => false,
            ]);

            $this->buatJadwalCicilan($modal, $hitung['jadwal'], $validated['tanggal_pencairan'], $validated['satuan_tenor']);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data modal berhasil disimpan.', 'data' => $modal]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Modal store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    // ---------------------------------------------------------------
    // SHOW
    // ---------------------------------------------------------------
    public function show($id)
    {
        $modal = Modal::with(['cicilans' => fn($q) => $q->orderBy('cicilan_ke')])->findOrFail($id);
        return view('modals.show', compact('modal'));
    }

    // ---------------------------------------------------------------
    // UPDATE
    // ---------------------------------------------------------------
    public function update(Request $request, $id)
    {
        $modal = Modal::findOrFail($id);

        $validated = $request->validate([
            'nama_pemberi_pinjaman' => 'required|string|max:255',
            'jenis_pinjaman'        => 'required|in:bank,koperasi,perorangan,lembaga_keuangan,lainnya',
            'jumlah_pinjaman'       => 'required|numeric|min:1',
            'nominal_pencairan'     => 'required|numeric|min:1',
            'total_bunga'           => 'required|numeric|min:0',
            'tenor'                 => 'required|integer|min:1',
            'satuan_tenor'          => 'required|in:hari,minggu,bulan,tahun',
            'tanggal_pinjaman'      => 'required|date',
            'tanggal_pencairan'     => 'required|date',
            'tanggal_jatuh_tempo'   => 'required|date',
            'status'                => 'required|in:aktif,lunas,macet,restrukturisasi',
            'keterangan'            => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $perluHitungUlang =
                $modal->jumlah_pinjaman != $validated['jumlah_pinjaman'] ||
                $modal->total_bunga     != $validated['total_bunga']     ||
                $modal->tenor           != $validated['tenor'];

            if ($perluHitungUlang) {
                $hitung = self::hitungCicilanNominal(
                    $validated['jumlah_pinjaman'],
                    $validated['total_bunga'],
                    $validated['tenor']
                );

                $validated['total_kewajiban']     = $hitung['total_kewajiban'];
                $validated['cicilan_per_periode'] = $hitung['cicilan_per_periode'];
                $validated['sisa_kewajiban']      = $hitung['total_kewajiban'] - $modal->total_terbayar;

                // Hapus jadwal yang belum bayar, buat ulang
                $modal->cicilans()->where('status', 'belum_bayar')->delete();
                $sudahBayar   = $modal->cicilans()->where('status', 'sudah_bayar')->count();
                $jadwalBaru   = array_filter($hitung['jadwal'], fn($j) => $j['cicilan_ke'] > $sudahBayar);

                $this->buatJadwalCicilan($modal, $jadwalBaru, $validated['tanggal_pencairan'], $validated['satuan_tenor']);
            }

            $modal->update($validated);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data modal berhasil diperbarui.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui: ' . $e->getMessage()], 500);
        }
    }

    // ---------------------------------------------------------------
    // DESTROY
    // ---------------------------------------------------------------
    public function destroy($id)
    {
        $modal = Modal::findOrFail($id);

        if ($modal->sudah_dicairkan) {
            return response()->json(['success' => false, 'message' => 'Modal yang sudah dicairkan tidak dapat dihapus.'], 422);
        }

        $modal->delete();
        return response()->json(['success' => true, 'message' => 'Data modal berhasil dihapus.']);
    }

    // ---------------------------------------------------------------
    // BAYAR CICILAN
    // ---------------------------------------------------------------
    public function bayarCicilan(Request $request, $id)
    {
        $validated = $request->validate([
            'cicilan_id'    => 'required|exists:modal_cicilans,id',
            'tanggal_bayar' => 'required|date',
            'denda'         => 'nullable|numeric|min:0',
            'keterangan'    => 'nullable|string',
        ]);

        $modal   = Modal::findOrFail($id);
        $cicilan = ModalCicilan::where('modal_id', $id)->findOrFail($validated['cicilan_id']);

        if ($cicilan->status === 'sudah_bayar') {
            return response()->json(['success' => false, 'message' => 'Cicilan ini sudah dibayar.'], 422);
        }

        DB::beginTransaction();
        try {
            $denda      = $validated['denda'] ?? 0;
            $totalBayar = $cicilan->nominal_cicilan + $denda;

            $cicilan->update([
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'denda'         => $denda,
                'total_bayar'   => $totalBayar,
                'status'        => 'sudah_bayar',
                'keterangan'    => $validated['keterangan'],
            ]);

            $sudahBayar    = $modal->cicilans()->where('status', 'sudah_bayar')->count();
            $totalTerbayar = $modal->cicilans()->where('status', 'sudah_bayar')->sum('nominal_cicilan');
            $sisaKewajiban = $modal->total_kewajiban - $totalTerbayar;
            $statusBaru    = ($sisaKewajiban <= 0) ? 'lunas' : 'aktif';

            $modal->update([
                'cicilan_ke'     => $sudahBayar,
                'total_terbayar' => $totalTerbayar,
                'sisa_kewajiban' => max(0, $sisaKewajiban),
                'status'         => $statusBaru,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Cicilan ke-{$cicilan->cicilan_ke} berhasil dibayar.",
                'data'    => [
                    'progress_persen' => $modal->fresh()->getProgressPersen(),
                    'sisa_kewajiban'  => $modal->fresh()->sisa_kewajiban,
                    'status'          => $statusBaru,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal membayar: ' . $e->getMessage()], 500);
        }
    }

    // ---------------------------------------------------------------
    // KALKULASI (AJAX preview — opsional, dipakai jika masih dibutuhkan)
    // ---------------------------------------------------------------
    public function kalkulasi(Request $request)
    {
        $validated = $request->validate([
            'jumlah_pinjaman' => 'required|numeric|min:1',
            'total_bunga'     => 'required|numeric|min:0',
            'tenor'           => 'required|integer|min:1',
        ]);

        $result = self::hitungCicilanNominal(
            $validated['jumlah_pinjaman'],
            $validated['total_bunga'],
            $validated['tenor']
        );

        return response()->json(['success' => true, 'data' => $result]);
    }

    // ---------------------------------------------------------------
    // HELPER: Hitung cicilan berdasarkan nominal bunga
    //
    //   Pokok per cicilan  = jumlah_pinjaman / tenor
    //   Bunga per cicilan  = total_bunga / tenor
    //   Cicilan per periode = (jumlah_pinjaman + total_bunga) / tenor
    // ---------------------------------------------------------------
    public static function hitungCicilanNominal(float $pinjaman, float $totalBunga, int $tenor): array
    {
        $totalKewajiban    = $pinjaman + $totalBunga;
        $cicilanPerPeriode = round($totalKewajiban / $tenor, 2);

        $jadwal    = [];
        $terbayar  = 0;

        for ($i = 1; $i <= $tenor; $i++) {
            // Koreksi pembulatan pada cicilan terakhir agar total pas
            $cicilan   = ($i === $tenor) ? round($totalKewajiban - $terbayar, 2) : $cicilanPerPeriode;
            $terbayar += $cicilan;

            $jadwal[] = [
                'cicilan_ke'      => $i,
                'nominal_cicilan' => $cicilan,
            ];
        }

        return [
            'total_kewajiban'     => $totalKewajiban,
            'total_bunga'         => $totalBunga,
            'cicilan_per_periode' => $cicilanPerPeriode,
            'jadwal'              => $jadwal,
        ];
    }

    // ---------------------------------------------------------------
    // HELPER: Buat jadwal cicilan ke database
    // ---------------------------------------------------------------
    private function buatJadwalCicilan(Modal $modal, array $jadwal, string $tanggalPencairan, string $satuanTenor): void
    {
        $tanggalMulai = \Carbon\Carbon::parse($tanggalPencairan);

        foreach ($jadwal as $item) {
            $tgl = match ($satuanTenor) {
                'hari'   => $tanggalMulai->copy()->addDays($item['cicilan_ke']),
                'minggu' => $tanggalMulai->copy()->addWeeks($item['cicilan_ke']),
                'bulan'  => $tanggalMulai->copy()->addMonths($item['cicilan_ke']),
                'tahun'  => $tanggalMulai->copy()->addYears($item['cicilan_ke']),
            };

            ModalCicilan::create([
                'modal_id'            => $modal->id,
                'cicilan_ke'          => $item['cicilan_ke'],
                'tanggal_jatuh_tempo' => $tgl->toDateString(),
                'nominal_cicilan'     => $item['nominal_cicilan'],
                'status'              => 'belum_bayar',
            ]);
        }
    }
}
