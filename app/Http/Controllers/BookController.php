<?php

namespace App\Http\Controllers;

use App\Models\BookPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    public function index()
    {
        return view('books.index', ['books' => BookPeriod::orderByDesc('starts_at')->get(), 'active' => BookPeriod::active()]);
    }

    private function firstTransactionDate(): ?Carbon
    {
        $earliest = collect([
            'sales' => 'created_at', 'orders' => 'paid_at', 'rentals' => 'created_at',
            'services' => 'done_at', 'expenses' => 'entry_date', 'payrolls' => 'release_date',
            'modals' => 'tanggal_pencairan', 'modal_cicilans' => 'tanggal_bayar',
        ])->map(fn ($column, $table) => Schema::hasTable($table) && Schema::hasColumn($table, $column)
            ? DB::table($table)->min($column) : null)->filter()->min();

        return $earliest ? Carbon::parse($earliest)->startOfDay() : null;
    }

    public function close(Request $request)
    {
        $data = $request->validate(['next_start' => 'required|date|before_or_equal:'.now()->addDay()->toDateString()]);
        DB::transaction(function () use ($data) {
            $book = BookPeriod::whereNull('ends_at')->lockForUpdate()->first();
            if (!$book) {
                if (BookPeriod::exists()) {
                    throw ValidationException::withMessages(['next_start' => 'Buka buku baru terlebih dahulu.']);
                }
                $book = BookPeriod::create(['starts_at' => $this->firstTransactionDate() ?? now()->startOfDay(), 'opening_balance' => 0]);
            }
            $next = Carbon::parse($data['next_start'])->startOfDay();
            if ($next->lte($book->starts_at)) {
                throw ValidationException::withMessages(['next_start' => 'Tanggal penutupan harus setelah awal buku.']);
            }
            // ponytail: transaksi masa depan tidak dipindahkan otomatis; pilih batas setelah transaksi terakhir.
            foreach ([
                'sales' => 'created_at', 'sale_payments' => 'paid_at', 'orders' => 'paid_at',
                'rentals' => 'created_at', 'services' => 'done_at', 'expenses' => 'entry_date',
                'payrolls' => 'release_date', 'modals' => 'tanggal_pencairan', 'modal_cicilans' => 'tanggal_bayar',
            ] as $table => $column) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, $column) && DB::table($table)->whereDate($column, '>=', $next)->exists()) {
                    throw ValidationException::withMessages(['next_start' => 'Ada transaksi setelah batas buku. Pilih tanggal setelah transaksi terakhir.']);
                }
            }
            $end = $next->copy()->subDay();
            $book->update(['ends_at' => $end]);
            $report = app(ReportController::class)->index(Request::create('/reports', 'GET', [
                'book' => $book->id, 'from' => $book->starts_at->toDateString(), 'to' => $end->toDateString(), 'trend' => 'monthly',
            ]));
            $data = $report->getData();
            $book->update(['snapshot' => [
                'metrics' => collect($data)->except(['sales', 'onlineOrders', 'transactions', 'chartData', 'from', 'to', 'trend', 'book'])->all(),
                'transactions' => $data['transactions']->map(fn ($row) => [
                    'invoice_number' => $row->invoice_number, 'source' => $row->source,
                    'date' => $row->date->format('d/m/Y H:i'), 'grand_total' => $row->grand_total,
                    'benefit' => $row->benefit, 'payment_method' => $row->payment_method,
                    'payment_status' => $row->payment_status,
                ])->all(),
            ]]);
        });

        return redirect()->route('books.index')->with('success', 'Buku ditutup. Buka buku baru untuk melanjutkan.');
    }

    public function open(Request $request)
    {
        $data = $request->validate(['opening_balance' => 'required|numeric|min:0|max:9999999999999']);
        DB::transaction(function () use ($data) {
            if (BookPeriod::whereNull('ends_at')->lockForUpdate()->exists()) {
                throw ValidationException::withMessages(['opening_balance' => 'Tutup buku aktif terlebih dahulu.']);
            }
            $previous = BookPeriod::orderByDesc('starts_at')->lockForUpdate()->first();
            if (!$previous) {
                throw ValidationException::withMessages(['opening_balance' => 'Tutup buku yang sudah berjalan terlebih dahulu.']);
            }
            BookPeriod::create([
                'starts_at' => $previous->ends_at->copy()->addDay(),
                'opening_balance' => $data['opening_balance'],
            ]);
        });

        return redirect()->route('books.index')->with('success', 'Buku baru dibuka.');
    }

    public function archive(BookPeriod $book)
    {
        abort_unless($book->snapshot, 404);
        return response($this->archiveHtml($book))->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Security-Policy', "default-src 'none'; style-src 'unsafe-inline'; img-src 'self' data:");
    }

    public function archivePdf(BookPeriod $book)
    {
        abort_unless($book->snapshot, 404);
        return Pdf::loadHTML($this->archiveHtml($book))->setPaper('a4', 'landscape')
            ->download('arsip-buku-'.$book->starts_at->toDateString().'.pdf');
    }

    private function archiveHtml(BookPeriod $book): string
    {
        $snapshot = $book->snapshot;
        // ponytail: arsip HTML lama hanya menyimpan markup; ambil ringkasan/tabelnya tanpa mengubah angka tersimpan.
        if (isset($snapshot['html'])) {
            $dom = new \DOMDocument();
            @$dom->loadHTML($snapshot['html']);
            $text = new \DOMXPath($dom);
            $summary = $text->query("//*[contains(@class, 'bg-indigo-900')]")->item(0)?->textContent;
            $tables = [];
            foreach ($text->query('//table') as $table) {
                $rows = [];
                foreach ($text->query('.//tr', $table) as $tr) {
                    $cells = [];
                    foreach ($text->query('./th|./td', $tr) as $cell) {
                        $cells[] = trim(preg_replace('/\s+/', ' ', $cell->textContent));
                    }
                    if ($cells) $rows[] = $cells;
                }
                if ($rows) $tables[] = $rows;
            }
            $snapshot = ['legacy_summary' => trim(preg_replace('/\s+/', ' ', $summary ?? 'Arsip laporan lama')), 'legacy_tables' => $tables];
        }

        return view('books.archive', compact('book', 'snapshot'))->render();
    }

    public function cancel(BookPeriod $book)
    {
        DB::transaction(function () use ($book) {
            $book = BookPeriod::whereKey($book->id)->lockForUpdate()->firstOrFail();
            if (!$book->ends_at || !$book->snapshot) {
                throw ValidationException::withMessages(['book' => 'Buku ini tidak sedang ditutup.']);
            }
            BookPeriod::where('starts_at', '>', $book->starts_at)->delete();
            $book->update(['ends_at' => null, 'snapshot' => null]);
        });

        return redirect()->route('books.index')->with('success', 'Penutupan dibatalkan. Buku terpilih kembali aktif; buku berikutnya digabung.');
    }
}
