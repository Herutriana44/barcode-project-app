import { Html5Qrcode } from 'html5-qrcode';

/**
 * @param {string} raw
 * @returns {string|null} URL absolut atau path /scan/... untuk navigasi; null jika tidak dikenali
 */
function resolveScanNavigation(raw) {
    const trimmed = (raw || '').trim();
    if (!trimmed) {
        return null;
    }

    const pathOnly = trimmed.match(/^\/?scan\/([^/?#]+)\/?$/i);
    if (pathOnly) {
        const id = decodeURIComponent(pathOnly[1]);
        return '/scan/' + encodeURIComponent(id);
    }

    if (/^https?:\/\//i.test(trimmed)) {
        try {
            const u = new URL(trimmed);
            const m = u.pathname.match(/\/scan\/([^/]+)\/?$/);
            if (m) {
                const id = decodeURIComponent(m[1]);
                return '/scan/' + encodeURIComponent(id);
            }
        } catch (e) {
            return null;
        }
        return trimmed;
    }

    if (trimmed.startsWith('IB-') || trimmed.startsWith('CB-')) {
        return '/scan/' + encodeURIComponent(trimmed);
    }

    return null;
}

document.addEventListener('DOMContentLoaded', function () {
    const manualForm = document.getElementById('manualScanForm');
    const barcodeInput = document.getElementById('barcode_input');

    if (manualForm && barcodeInput) {
        manualForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const nav = resolveScanNavigation(barcodeInput.value);
            if (nav) {
                window.location.href = nav;
                return;
            }
            window.alert('Masukkan ID (IB-… / CB-…), path seperti /scan/IB-…, atau URL lengkap ke halaman scan.');
        });
    }

    const readerDiv = document.getElementById('reader');
    if (readerDiv && typeof Html5Qrcode !== 'undefined') {
        const html5QrCode = new Html5Qrcode('reader');

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 150 },
            formatsToSupport: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        };

        Html5Qrcode.getCameras().then(cameras => {
            if (cameras && cameras.length) {
                const backCamera = cameras.find(c => c.label.toLowerCase().includes('back')) || cameras[0];
                html5QrCode.start(
                    backCamera.id,
                    config,
                    (decodedText) => {
                        const nav = resolveScanNavigation(decodedText);
                        if (!nav) {
                            return;
                        }
                        html5QrCode.stop();
                        window.location.href = nav;
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
