<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $source->name }} - PDF Viewer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js"></script>
    <style>
        body {
            font-family: 'Manrope', sans-serif;
            margin: 0;
            padding: 0;
            background: #f1f5f9;
        }
        #pdf-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .pdf-page {
            margin: 10px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            background: white;
        }
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .highlight {
            background-color: #fef08a;
            padding: 2px 4px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div class="flex items-center gap-4">
            <button onclick="history.back()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-semibold text-gray-700 transition-all">
                ← Back
            </button>
            <h1 class="text-xl font-bold text-gray-800 truncate max-w-md">{{ $source->name }}</h1>
        </div>
        <div class="flex items-center gap-4">
            <button id="zoom-out" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all">−</button>
            <span id="zoom-level" class="font-semibold text-gray-700">100%</span>
            <button id="zoom-in" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all">+</button>
            <div class="flex items-center gap-2">
                <span class="text-gray-600 font-semibold">Page:</span>
                <span id="current-page" class="font-bold text-gray-800">{{ $page }}</span>
                <span class="text-gray-500">/</span>
                <span id="total-pages" class="text-gray-600">0</span>
            </div>
        </div>
    </div>

    <div id="pdf-container"></div>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const container = document.getElementById('pdf-container');
        let pdfDoc = null;
        let currentScale = 1.0;
        const targetPage = {{ $page }};
        const highlightText = @json($highlightText);

        const pdfUrl = "{{ route('pdf.stream', $source) }}";

        async function loadPdf() {
            try {
                const loadingTask = pdfjsLib.getDocument(pdfUrl);
                pdfDoc = await loadingTask.promise;
                document.getElementById('total-pages').textContent = pdfDoc.numPages;
                
                for (let i = 1; i <= pdfDoc.numPages; i++) {
                    await renderPage(i);
                }

                setTimeout(() => {
                    const pageElement = document.getElementById(`page-${targetPage}`);
                    if (pageElement) {
                        pageElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 500);
            } catch (error) {
                console.error('Error loading PDF:', error);
            }
        }

        async function renderPage(pageNum) {
            const page = await pdfDoc.getPage(pageNum);
            const scale = currentScale;
            const viewport = page.getViewport({ scale: scale });

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            canvas.id = `page-${pageNum}`;
            canvas.className = 'pdf-page';

            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            wrapper.appendChild(canvas);

            container.appendChild(wrapper);

            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            await page.render(renderContext).promise;

            const textContent = await page.getTextContent();
            const textLayerDiv = document.createElement('div');
            textLayerDiv.className = 'textLayer';
            textLayerDiv.style.position = 'absolute';
            textLayerDiv.style.left = '0';
            textLayerDiv.style.top = '0';
            textLayerDiv.style.right = '0';
            textLayerDiv.style.bottom = '0';
            textLayerDiv.style.overflow = 'hidden';
            textLayerDiv.style.opacity = '0.2';
            
            textLayerDiv.style.setProperty('--scale-factor', scale);

            wrapper.appendChild(textLayerDiv);

            pdfjsLib.renderTextLayer({
                textContent: textContent,
                container: textLayerDiv,
                viewport: viewport,
                textDivs: []
            });
        }

        document.getElementById('zoom-in').addEventListener('click', () => {
            currentScale += 0.25;
            updateZoom();
        });

        document.getElementById('zoom-out').addEventListener('click', () => {
            if (currentScale > 0.5) {
                currentScale -= 0.25;
                updateZoom();
            }
        });

        function updateZoom() {
            document.getElementById('zoom-level').textContent = Math.round(currentScale * 100) + '%';
            container.innerHTML = '';
            loadPdf();
        }

        loadPdf();
    </script>
</body>
</html>
