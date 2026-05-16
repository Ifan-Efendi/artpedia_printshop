@if($sedangDiproses->count() > 0)
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Sedang Dikerjakan</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Operator</th>
                            <th>No. Pesanan</th>
                            <th>Produk</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sedangDiproses as $proses)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary text-white rounded-circle p-1"
                                            style="width: 24px; height: 24px; font-size: 10px; display: flex; align-items: center; justify-content: center;">
                                            {{ substr($proses->operator->name, 0, 1) }}
                                        </div>
                                        <span>{{ $proses->operator->name }}</span>
                                    </div>
                                </td>
                                <td><code>{{ $proses->nomor_pesanan }}</code></td>
                                <td>{{ $proses->produk->nama }}</td>
                                <td>
                                    <span class="badge bg-primary">Sedang Dikerjakan</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('produksi.show', $proses->id) }}"
                                            class="btn btn-sm btn-outline-primary queue-action-btn">Detail</a>
                                        @if($proses->diproses_oleh == auth()->id())
                                            <form action="{{ route('produksi.selesai', $proses->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success queue-action-btn">Selesai</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Antrian Proses Pesanan</h5>
    </div>
    <div class="card-body p-0">
        @if($antrian->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>No. Pesanan</th>
                            <th>Produk & Spek</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($antrian as $key => $antri)
                            @php
                                $operatorHasActiveJob = $sedangDiproses->where('diproses_oleh', auth()->id())->count() > 0;
                                $isFirstQueueItem = $antrian->currentPage() === 1 && $key === 0;
                                $canStart = $isFirstQueueItem && !$operatorHasActiveJob;
                            @endphp
                            <tr class="{{ $key == 0 ? 'table-warning' : '' }}">
                                <td class="text-center">
                                    @if($key == 0)
                                        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> URUTAN 1</span>
                                    @else
                                        {{ $antrian->firstItem() + $key }}
                                    @endif
                                </td>
                                <td><code>{{ $antri->nomor_pesanan }}</code></td>
                                <td>
                                    <strong>{{ $antri->produk->nama }}</strong><br>
                                    <small class="text-muted">{{ $antri->ukuranKertas->nama }} |
                                        {{ $antri->jenisKertas->nama }}</small>
                                </td>
                                <td class="fw-bold">{{ $antri->jumlah }}</td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('produksi.show', $antri->id) }}"
                                            class="btn btn-sm btn-outline-primary queue-action-btn">Detail</a>
                                        <form action="{{ route('produksi.mulai', $antri->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success queue-action-btn" {{ $canStart ? '' : 'disabled' }}>
                                                Kerjakan
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $antrian->links() }}
            </div>
        @else
            <div class="empty-state py-5">
                <i class="bi bi-clipboard-check text-success"></i>
                <h5 class="mt-3">Antrian kosong!</h5>
                <p class="text-muted">Tidak ada pesanan yang menunggu dikerjakan.</p>
            </div>
        @endif
    </div>
</div>
