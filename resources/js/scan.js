import { Html5Qrcode } from 'html5-qrcode';

/**
 * @param {string} raw
 * @returns {string|null} URL absolut atau path /scan/... untuk navigasi; null jika tidak dikenali
 */
function resolveScanNavigation(raw) {
    const trimmed = (raw || '').trim();
    if (!trimmed) return null;

    let targetPath = null;
    const baseUrl = 'https://tasm-mis-management-information-system.com/public/scan';

    // 1. Jika URL lengkap, ambil ID-nya
    if (/^https?:\/\//i.test(trimmed)) {
        try {
            const u = new URL(trimmed);
            const pathParts = u.pathname.split('/').filter(p => p);
            const scanIndex = pathParts.findIndex(p => p === 'scan');
            if (scanIndex !== -1 && pathParts[scanIndex + 1]) {
                targetPath = baseUrl + '/' + encodeURIComponent(pathParts[scanIndex + 1]);
            }
        } catch (e) { return null; }
    } 
    // 2. Jika Input Manual ID (IB-..., CB-..., EMP-...)
    else if (trimmed.startsWith('IB-') || trimmed.startsWith('CB-') || trimmed.startsWith('EMP-')) {
        targetPath = baseUrl + '/' + encodeURIComponent(trimmed);
    }
    // 3. Jika input adalah path parsial (misal: scan/IB-...)
    else {
        const parts = trimmed.split('/').filter(p => p);
        const lastPart = parts[parts.length - 1];
        if (lastPart && (lastPart.startsWith('IB-') || lastPart.startsWith('CB-') || lastPart.startsWith('EMP-'))) {
            targetPath = baseUrl + '/' + encodeURIComponent(lastPart);
        }
    }

    if (targetPath) {
        console.log('Navigating to forced URL:', targetPath);
    }
    return targetPath;
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
    const switchCameraBtn = document.getElementById('switchCameraBtn');

    if (readerDiv && typeof Html5Qrcode !== 'undefined') {
        const html5QrCode = new Html5Qrcode('reader');
        let currentCameraId = null;
        let cameras = [];
        let cameraIndex = 0;
        let currentMode = 'qr';

        const getConfig = (mode) => ({
            fps: 10,
            qrbox: mode === 'qr' ? { width: 250, height: 250 } : { width: 250, height: 100 },
            formatsToSupport: mode === 'qr' ? [0] : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        });

        const startCamera = (cameraId, mode) => {
            currentMode = mode;
            html5QrCode.start(
                cameraId,
                getConfig(mode),
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
            });
        };

        const scanModeRadios = document.querySelectorAll('input[name="scan_mode"]');
        scanModeRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                const newMode = e.target.value;
                if (html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        startCamera(cameras[cameraIndex].id, newMode);
                    });
                } else {
                    startCamera(cameras[cameraIndex].id, newMode);
                }
            });
        });

        Html5Qrcode.getCameras().then(foundCameras => {
            cameras = foundCameras;
            if (cameras && cameras.length) {
                if (cameras.length > 1 && switchCameraBtn) {
                    switchCameraBtn.classList.remove('hidden');
                    switchCameraBtn.addEventListener('click', () => {
                        cameraIndex = (cameraIndex + 1) % cameras.length;
                        html5QrCode.stop().then(() => {
                            startCamera(cameras[cameraIndex].id, currentMode);
                        });
                    });
                }

                // Prefer back camera initially
                const backCameraIndex = cameras.findIndex(c => c.label.toLowerCase().includes('back'));
                cameraIndex = backCameraIndex !== -1 ? backCameraIndex : 0;
                
                startCamera(cameras[cameraIndex].id, 'qr');
            } else {
                readerDiv.innerHTML = '<p class="p-4 text-gray-500">Tidak ada kamera terdeteksi. Gunakan input manual.</p>';
            }
        }).catch(err => {
            console.warn('Camera list error:', err);
            readerDiv.innerHTML = '<p class="p-4 text-gray-500">Gagal mengakses kamera. Gunakan input manual.</p>';
        });
    }
});
