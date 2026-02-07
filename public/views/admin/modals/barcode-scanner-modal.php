<?php
// Barcode Scanner Modal
?>
<div id="barcodeScannerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50" style="transition: none;">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 flex flex-col max-h-[90vh]" style="transition: none;">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Barcode Scanner</h2>
            <button type="button" class="text-gray-600 hover:text-gray-800 text-2xl" onclick="closeBarcodeScanner()" style="background: none; border: none; cursor: pointer;">
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
            <p class="font-semibold">Barcode Detected:</p>
            <p id="detected-barcode" class="font-mono text-lg break-all"></p>
        </div>

        <div class="flex gap-2 mb-3">
            <button type="button" class="flex-1 px-3 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition font-semibold text-sm flex items-center justify-center gap-2" onclick="document.getElementById('barcode-upload-input').click()">
                <span class="material-icons text-base">upload</span>
                <span>Upload Image</span>
            </button>
            <input type="file" id="barcode-upload-input" class="hidden" accept="image/*" onchange="handleBarcodeImageUpload(event)">
        </div>

        <div class="flex gap-3">
            <button type="button" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold" onclick="closeBarcodeScanner()">
                Close
            </button>
            <button type="button" id="use-barcode-btn" class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-semibold hidden" onclick="useDetectedBarcode()">
                Use Barcode
            </button>
        </div>

        <div id="scanner-error" class="hidden mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <p id="scanner-error-text" class="font-semibold"></p>
        </div>
    </div>
</div>
