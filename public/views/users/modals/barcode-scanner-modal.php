<!-- Barcode Scanner Modal -->
<div id="barcodeScannerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-[60]" style="transition: none;">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 flex flex-col h-4/5 max-h-96" style="transition: none;">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Barcode Scanner</h2>
            <button type="button" class="text-gray-600 hover:text-gray-800 text-2xl" onclick="closeBarcodeScannerModal()" style="background: none; border: none; cursor: pointer;">
                ×
            </button>
        </div>

        <div id="scanner-container" class="flex-1 flex flex-col items-center justify-center bg-gray-100 rounded-lg mb-4 overflow-hidden">
            <video id="scanner-video" style="width: 100%; height: 100%; object-fit: cover; display: none;"></video>
            <canvas id="scanner-canvas" style="display: none;"></canvas>
            <img id="upload-preview" style="display: none; max-width: 100%; max-height: 100%; object-fit: contain;">
            <div id="scanner-placeholder" class="flex flex-col items-center justify-center h-full">
                <span class="material-icons text-5xl text-gray-400 mb-2">camera_alt</span>
                <p class="text-gray-500 text-center px-4">Camera or upload image</p>
            </div>
        </div>

        <div id="scanner-result" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <p class="font-semibold">Barcode Detected:</p>
            <p id="detected-barcode" class="font-mono text-lg break-all"></p>
        </div>

        <div class="flex gap-2 mb-3">
            <button type="button" class="flex-1 px-3 py-2 bg-gold hover:bg-gold/90 text-white rounded-lg transition font-semibold text-sm flex items-center justify-center gap-2" onclick="document.getElementById('barcode-upload-input').click()">
                <span class="material-icons text-base">upload</span>
                <span>Upload Image</span>
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

    function closeBarcodeScannerModal() {
        document.getElementById('barcodeScannerModal').classList.add('hidden');
        userDetectedBarcodeValue = null;
        document.getElementById('upload-preview').style.display = 'none';
        document.getElementById('scanner-placeholder').style.display = 'flex';
        document.getElementById('scanner-result').classList.add('hidden');
        document.getElementById('scanner-error').classList.add('hidden');
        document.getElementById('use-barcode-btn').classList.add('hidden');
    }

    function openBarcodeScannerModal() {
        userDetectedBarcodeValue = null;
        document.getElementById('barcodeScannerModal').classList.remove('hidden');
        document.getElementById('upload-preview').style.display = 'none';
        document.getElementById('scanner-placeholder').style.display = 'flex';
        document.getElementById('scanner-result').classList.add('hidden');
        document.getElementById('scanner-error').classList.add('hidden');
        document.getElementById('use-barcode-btn').classList.add('hidden');
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
        if (userDetectedBarcodeValue) {
            console.log("[Barcode Scanner] Using detected barcode:", userDetectedBarcodeValue);
            document.getElementById("productBarcode").value = userDetectedBarcodeValue;
            closeBarcodeScannerModal();
            console.log("[Barcode Scanner] Barcode populated");
        } else {
            alert('No barcode detected. Please scan or upload an image again.');
        }
    }
</script>
