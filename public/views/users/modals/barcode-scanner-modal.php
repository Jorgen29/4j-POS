<!-- Barcode Scanner Modal -->
<div id="barcodeScannerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-[60]" style="transition: none;">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 flex flex-col max-h-[90vh]" style="transition: none;">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Barcode Scanner</h2>
            <button type="button" class="text-gray-600 hover:text-gray-800 text-2xl" onclick="closeBarcodeScannerModal()" style="background: none; border: none; cursor: pointer;">
                ×
            </button>
        </div>

        <div id="scanner-container" class="flex-1 flex flex-col items-center justify-center bg-gray-100 rounded-lg mb-4 overflow-hidden" style="min-height: 320px;">
            <video id="scanner-video" style="width: 100%; height: 100%; object-fit: cover;" autoplay playsinline muted></video>
            <canvas id="scanner-canvas" style="display: none;"></canvas>
            <img id="upload-preview" style="display: none; max-width: 100%; max-height: 100%; object-fit: contain;">
            <div id="scanner-placeholder" class="flex flex-col items-center justify-center h-full">
                <span class="material-icons text-5xl text-gray-400 mb-2">camera_alt</span>
                <p class="text-gray-500 text-center px-4">Camera or upload image</p>
            </div>
        </div>

        <div id="scanner-result" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <p class="font-semibold mb-2">Barcode Detected:</p>
            <p id="detected-barcode" class="font-mono text-lg break-all mb-2"></p>
            <input 
                type="text" 
                id="barcode-edit-input" 
                class="w-full px-3 py-2 border border-green-400 rounded text-sm text-gray-800 font-mono"
                placeholder="Edit barcode if needed"
            />
            <p class="text-xs mt-2 text-green-600">You can edit the barcode above if the scan wasn't accurate</p>
        </div>

        <div class="flex gap-2 mb-3">
            <button type="button" class="flex-1 px-3 py-2 bg-gold hover:bg-gold/90 text-white rounded-lg transition font-semibold text-sm flex items-center justify-center gap-2" onclick="startUserBarcodeCamera()">
                <span class="material-icons text-base">camera</span>
                <span>Start Camera</span>
            </button>
            <button type="button" class="flex-1 px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition font-semibold text-sm flex items-center justify-center gap-2" onclick="document.getElementById('barcode-upload-input').click()">
                <span class="material-icons text-base">upload</span>
                <span>Upload</span>
            </button>
            <input type="file" id="barcode-upload-input" class="hidden" accept="image/*" onchange="handleBarcodeImageUpload(event)">
        </div>

        <div class="flex gap-3">
            <button type="button" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold" onclick="closeBarcodeScannerModal()">
                Close
            </button>
            <button type="button" id="use-barcode-btn" class="flex-1 px-4 py-2 bg-green text-white rounded-lg hover:bg-green/90 transition font-semibold hidden" onclick="useDetectedBarcodeUser()">
                Use Barcode
            </button>
        </div>

        <div id="scanner-error" class="hidden mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <p id="scanner-error-text" class="font-semibold"></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
<script>
    let userDetectedBarcodeValue = null;
    let userBarcodeStream = null;
    let userQuaggaInitialized = false;

    function closeBarcodeScannerModal() {
        document.getElementById('barcodeScannerModal').classList.add('hidden');
        
        // Stop camera and Quagga
        if (userBarcodeStream) {
            userBarcodeStream.getTracks().forEach(track => track.stop());
            userBarcodeStream = null;
        }
        
        if (userQuaggaInitialized && window.Quagga) {
            try {
                Quagga.stop();
            } catch (e) {
                console.log('[User Barcode Scanner] Quagga already stopped');
            }
        }
        
        userQuaggaInitialized = false;
        userDetectedBarcodeValue = null;
        document.getElementById('upload-preview').style.display = 'none';
        document.getElementById('scanner-placeholder').style.display = 'flex';
        document.getElementById('scanner-result').classList.add('hidden');
        document.getElementById('scanner-error').classList.add('hidden');
        document.getElementById('use-barcode-btn').classList.add('hidden');
        document.getElementById('barcode-edit-input').value = '';
    }

    function openBarcodeScannerModal() {
        userDetectedBarcodeValue = null;
        document.getElementById('barcodeScannerModal').classList.remove('hidden');
        document.getElementById('upload-preview').style.display = 'none';
        document.getElementById('scanner-placeholder').style.display = 'flex';
        document.getElementById('scanner-result').classList.add('hidden');
        document.getElementById('scanner-error').classList.add('hidden');
        document.getElementById('use-barcode-btn').classList.add('hidden');
        document.getElementById('barcode-edit-input').value = '';
    }

    function startUserBarcodeCamera() {
        console.log('[User Barcode Scanner] Starting camera');
        
        const video = document.getElementById('scanner-video');
        const errorDiv = document.getElementById('scanner-error');
        const errorText = document.getElementById('scanner-error-text');
        const placeholder = document.getElementById('scanner-placeholder');

        // Check if site is secure
        const isSecureContext = window.isSecureContext || 
                               window.location.hostname === 'localhost' || 
                               window.location.hostname === '127.0.0.1';

        if (!isSecureContext) {
            errorText.textContent = '⚠️ Camera requires HTTPS or localhost. You can still upload an image.';
            errorDiv.classList.remove('hidden');
            return;
        }

        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(stream => {
                console.log('[User Barcode Scanner] Camera access granted');
                userBarcodeStream = stream;
                video.srcObject = stream;
                video.style.display = 'block';
                
                video.play().then(() => {
                    console.log('[User Barcode Scanner] Video playing');
                }).catch(err => {
                    console.error('[User Barcode Scanner] Video play error:', err);
                });
                
                placeholder.style.display = 'none';

                // Initialize Quagga after a brief delay
                setTimeout(() => {
                    try {
                        // Stop any existing instance first
                        if (userQuaggaInitialized) {
                            try {
                                Quagga.stop();
                            } catch (e) {
                                console.log('[User Barcode Scanner] Previous instance stopped');
                            }
                        }

                        Quagga.init({
                            inputStream: {
                                type: 'LiveStream',
                                target: video,
                                constraints: {
                                    facingMode: 'environment',
                                    width: { ideal: 1280 },
                                    height: { ideal: 720 }
                                }
                            },
                            decoder: {
                                readers: [
                                    'code_128_reader',
                                    'ean_reader',
                                    'ean_8_reader',
                                    'upc_reader',
                                    'upc_e_reader',
                                    'code_39_reader',
                                    'code_93_reader',
                                    'codabar_reader'
                                ],
                                debug: {
                                    showPatternInResult: false,
                                    showCanvasPath: false,
                                    showCanvas: false
                                }
                            }
                        }, function(err) {
                            if (err) {
                                console.error('[User Barcode Scanner] Quagga init error:', err);
                                errorText.textContent = 'Scanner initialization error: ' + err.message;
                                errorDiv.classList.remove('hidden');
                                return;
                            }
                            
                            console.log('[User Barcode Scanner] Quagga initialized');
                            userQuaggaInitialized = true;
                            Quagga.start();

                            Quagga.onDetected(function(result) {
                                if (result && result.codeResult && result.codeResult.code) {
                                    let barcode = result.codeResult.code;
                                    
                                    // Normalize barcode: trim, remove special characters, handle formatting
                                    barcode = barcode.trim();
                                    console.log('[User Barcode Scanner] Raw detected:', barcode);
                                    console.log('[User Barcode Scanner] Barcode length:', barcode.length);
                                    console.log('[User Barcode Scanner] Barcode format:', typeof barcode, barcode.charCodeAt ? Array.from(barcode).map(c => c.charCodeAt(0)).join(',') : 'N/A');

                                    userDetectedBarcodeValue = barcode;
                                    document.getElementById('detected-barcode').textContent = barcode;
                                    document.getElementById('barcode-edit-input').value = barcode;
                                    document.getElementById('scanner-result').classList.remove('hidden');
                                    document.getElementById('use-barcode-btn').classList.remove('hidden');

                                    Quagga.stop();
                                    userQuaggaInitialized = false;
                                }
                            });
                        });
                    } catch (error) {
                        console.error('[User Barcode Scanner] Error:', error);
                        errorText.textContent = 'Error: ' + error.message;
                        errorDiv.classList.remove('hidden');
                    }
                }, 500);
            })
            .catch(err => {
                console.error('[User Barcode Scanner] Camera error:', err);
                let errorMessage = 'Camera access denied';
                
                if (err.name === 'NotAllowedError') {
                    errorMessage = 'Camera permission denied. Please allow camera access.';
                } else if (err.name === 'NotFoundError' || err.name === 'NotSupportedError') {
                    errorMessage = 'No camera found or not supported.';
                } else if (err.name === 'NotSecureError') {
                    errorMessage = '⚠️ HTTPS required for camera access. Use https:// or localhost.';
                }
                
                errorText.textContent = errorMessage;
                errorDiv.classList.remove('hidden');
            });
    }

    function handleBarcodeImageUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        console.log("[Barcode Upload] Processing image:", file.name);

        const reader = new FileReader();
        const errorDiv = document.getElementById("scanner-error");
        const errorText = document.getElementById("scanner-error-text");
        const resultDiv = document.getElementById("scanner-result");
        const detectedBarcodeElement = document.getElementById("detected-barcode");
        const useBarcodeBtn = document.getElementById("use-barcode-btn");

        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
                console.log("[Barcode Upload] Image loaded, scanning...");

                // Show image preview
                const uploadPreview = document.getElementById("upload-preview");
                uploadPreview.src = e.target.result;
                uploadPreview.style.display = "block";
                document.getElementById("scanner-placeholder").style.display = "none";

                // Create canvas from image
                const canvas = document.getElementById("scanner-canvas");
                const ctx = canvas.getContext("2d");
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);

                // Use Quagga to decode barcode from image
                try {
                    Quagga.decodeSingle(
                        {
                            src: e.target.result,
                            numOfWorkers: 0,
                            inputStream: {
                                size: 800,
                            },
                            decoder: {
                                readers: [
                                    "code_128_reader",
                                    "ean_reader",
                                    "ean_8_reader",
                                    "upc_reader",
                                    "upc_e_reader",
                                    "code_39_reader",
                                    "code_93_reader",
                                    "codabar_reader",
                                ],
                            },
                        },
                        function (result) {
                            if (result && result.codeResult) {
                                const barcode = result.codeResult.code;
                                console.log("[Barcode Upload] Barcode detected:", barcode);

                                errorDiv.classList.add("hidden");
                                userDetectedBarcodeValue = barcode;
                                detectedBarcodeElement.textContent = barcode;
                                document.getElementById('barcode-edit-input').value = barcode;
                                resultDiv.classList.remove("hidden");
                                useBarcodeBtn.classList.remove("hidden");
                            } else {
                                console.warn("[Barcode Upload] No barcode found in image");
                                errorText.textContent =
                                    "No barcode detected in the image. Try a clearer image.";
                                errorDiv.classList.remove("hidden");
                            }
                        },
                    );
                } catch (error) {
                    console.error("[Barcode Upload] Decoding error:", error);
                    errorText.textContent = "Error decoding image: " + error.message;
                    errorDiv.classList.remove("hidden");
                }
            };

            img.onerror = function () {
                console.error("[Barcode Upload] Failed to load image");
                errorText.textContent = "Failed to load image. Please try another file.";
                errorDiv.classList.remove("hidden");
            };

            img.src = e.target.result;
        };

        reader.readAsDataURL(file);
    }

    function useDetectedBarcodeUser() {
        // Get the edited barcode value if user modified it, otherwise use detected value
        const editedBarcode = document.getElementById("barcode-edit-input").value.trim();
        const finalBarcode = editedBarcode || userDetectedBarcodeValue;
        
        if (finalBarcode) {
            console.log("[Barcode Scanner] Using barcode:", finalBarcode);
            document.getElementById("productBarcode").value = finalBarcode;
            closeBarcodeScannerModal();
            console.log("[Barcode Scanner] Barcode populated in product form");
        } else {
            alert('No barcode detected. Please scan or upload an image again.');
        }
    }
</script>
