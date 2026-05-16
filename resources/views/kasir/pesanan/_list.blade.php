@if($pesanans->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pesanans as $pesanan)
                    <tr>
                        <td><code>{{ $pesanan->nomor_pesanan }}</code></td>
                        <td>{{ $pesanan->user->name ?? '-' }}</td>
                        <td>{{ $pesanan->produk->nama ?? '-' }}</td>
                        <td class="fw-bold">{{ $pesanan->total_harga_format }}</td>
                        <td>
                            <span class="badge badge-{{ $pesanan->status }}">
                                {{ $pesanan->status_label }}
                            </span>
                        </td>
                        <td>{{ $pesanan->created_at->format('d/m/y H:i') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('kasir.pesanan.show', $pesanan->id) }}"
                                    class="btn btn-sm text-white" style="background: #c00073; border-color: #c00073;">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                @if($pesanan->status === 'pending' && $pesanan->pembayaran_status === 'pending')
                                    <form action="{{ route('kasir.pesanan.batalkan', $pesanan->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger batalkan-pesanan" data-nomor="{{ $pesanan->nomor_pesanan }}">
                                            <i class="bi bi-x-circle"></i> Batal
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="p-3">
        {{ $pesanans->links() }}
    </div>
@else
    <div class="empty-state py-5">
        <i class="bi bi-inbox"></i>
        <h5>Tidak ada pesanan ditemukan</h5>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const batalkanButtons = document.querySelectorAll('.batalkan-pesanan');
    
    batalkanButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const nomor = this.dataset.nomor;
            const form = this.closest('form');
            
            // Create custom confirmation modal
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                        <div class="modal-body p-4 text-center">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 58px; height: 58px; background: rgba(220, 38, 38, 0.12); color: #b91c1c;">
                                <i class="bi bi-x-circle" style="font-size: 1.9rem;"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Batalkan Pesanan?</h5>
                            <p class="text-muted mb-0">
                                Pesanan <strong>${nomor}</strong> akan dibatalkan dan tidak dilanjutkan ke proses produksi.
                            </p>
                        </div>
                        <div class="modal-footer border-0 bg-light px-4 pb-4 pt-3">
                            <button type="button" class="btn btn-outline-secondary px-4 btn-close-modal">Kembali</button>
                            <button type="button" class="btn btn-danger px-4 fw-bold btn-confirm-batalkan">
                                <i class="bi bi-x-circle me-1"></i> Ya, Batalkan
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            modal.querySelector('.btn-close-modal').addEventListener('click', () => {
                bsModal.hide();
                setTimeout(() => modal.remove(), 500);
            });
            
            modal.querySelector('.btn-confirm-batalkan').addEventListener('click', () => {
                form.submit();
            });
            
            modal.addEventListener('hidden.bs.modal', () => {
                modal.remove();
            });
        });
    });
});
</script>
@endpush
