import { Html5Qrcode } from 'html5-qrcode';

document.addEventListener('DOMContentLoaded', function() {
    const manualForm = document.getElementById('manualScanForm');
    const barcodeInput = document.getElementById('barcode_input');

    if (manualForm && barcodeInput) {
        manualForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const barcodeId = barcodeInput.value.trim();
            if (barcodeId) {
                window.location.href = `/scan/${encodeURIComponent(barcodeId)}`;
            }
        });
    }

    const readerDiv = document.getElementById('reader');
    if (readerDiv && typeof Html5Qrcode !== 'undefined') {
        const html5QrCode = new Html5Qrcode('reader');

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 150 },
            formatsToSupport: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
        };

        Html5Qrcode.getCameras().then(cameras => {
            if (cameras && cameras.length) {
                const backCamera = cameras.find(c => c.label.toLowerCase().includes('back')) || cameras[0];
                html5QrCode.start(
                    backCamera.id,
                    config,
                    (decodedText) => {
                        if (decodedText && (decodedText.startsWith('IB-') || decodedText.startsWith('CB-'))) {
                            html5QrCode.stop();
                            window.location.href = `/scan/${encodeURIComponent(decodedText)}`;
                        }
                    },
                    () => {}
                ).catch(err => {
                    console.warn('Camera error:', err);
                    readerDiv.innerHTML = '<p class="p-4 text-gray-500">Kamera tidak tersedia. Gunakan input manual.</p>';
                });
            } else {
                readerDiv.innerHTML = '<p class="p-4 text-gray-500">Tidak ada kamera terdeteksi. Gunakan input manual.</p>';
            }
        }).catch(err => {
            console.warn('Camera list error:', err);
            readerDiv.innerHTML = '<p class="p-4 text-gray-500">Gagal mengakses kamera. Gunakan input manual.</p>';
        });
    }
});
