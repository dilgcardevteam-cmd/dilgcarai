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
        .textLayer {
            pointer-events: none;
        }
        .textLayer span {
            color: transparent !important;
        }
        .citation-highlight {
            background: rgba(250, 204, 21, 0.55) !important;
            border-radius: 3px;
            outline: 2px solid rgba(234, 179, 8, 0.85);
            box-decoration-break: clone;
            -webkit-box-decoration-break: clone;
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
        const targetParagraph = @json($paragraphIndex);
        const targetSentence = @json($sentenceIndex);
        const targetStartOffset = @json($startOffset);
        const targetEndOffset = @json($endOffset);
        const pageTexts = new Map();
        const pageDetails = new Map();

        const pdfUrl = "{{ route('pdf.stream', $source) }}";

        async function loadPdf() {
            try {
                const loadingTask = pdfjsLib.getDocument(pdfUrl);
                pdfDoc = await loadingTask.promise;
                document.getElementById('total-pages').textContent = pdfDoc.numPages;
                
                for (let i = 1; i <= pdfDoc.numPages; i++) {
                    await renderPage(i);
                }

                const resolvedPage = resolveTargetPage();
                document.getElementById('current-page').textContent = resolvedPage;

                setTimeout(() => {
                    scrollToPage(resolvedPage);
                }, 350);
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
            const rawText = textContent.items.map((item) => item.str || '').join(' ');
            pageTexts.set(pageNum, normalizeText(rawText));
            const textLayerDiv = document.createElement('div');
            textLayerDiv.className = 'textLayer';
            textLayerDiv.style.position = 'absolute';
            textLayerDiv.style.left = '0';
            textLayerDiv.style.top = '0';
            textLayerDiv.style.right = '0';
            textLayerDiv.style.bottom = '0';
            textLayerDiv.style.overflow = 'hidden';
            textLayerDiv.style.opacity = '1';
            
            textLayerDiv.style.setProperty('--scale-factor', scale);

            wrapper.appendChild(textLayerDiv);

            const textDivs = [];
            const textLayerTask = pdfjsLib.renderTextLayer({
                textContent: textContent,
                container: textLayerDiv,
                viewport: viewport,
                textDivs: textDivs
            });

            await (textLayerTask?.promise ?? Promise.resolve());

            pageDetails.set(pageNum, {
                rawText: rawText,
                normalizedText: normalizeText(rawText),
                textContent: textContent,
                textDivs: textDivs,
            });
        }

        function normalizeText(value) {
            return (value || '')
                .toString()
                .toLowerCase()
                .replace(/\s+/g, ' ')
                .replace(/[^\p{L}\p{N}\s]/gu, '')
                .trim();
        }

        function resolveTargetPage() {
            const normalizedHighlight = normalizeText(highlightText);
            const requestedPage = clampPage(targetPage);

            if (!normalizedHighlight) {
                return requestedPage;
            }

            const compactHighlight = normalizedHighlight.slice(0, 450);
            const requestedPageText = pageTexts.get(requestedPage) || '';
            const requestedTerms = compactHighlight
                .split(' ')
                .filter((term) => term.length > 3)
                .slice(0, 18);
            const requestedScore = requestedTerms.reduce((total, term) => total + (requestedPageText.includes(term) ? 1 : 0), 0);

            // Citation metadata is the source of truth. Only search other pages when
            // the requested page clearly does not contain the cited passage.
            if (
                requestedPageText.includes(compactHighlight) ||
                requestedScore >= Math.min(4, Math.max(1, Math.ceil(requestedTerms.length * 0.35)))
            ) {
                return requestedPage;
            }

            for (const [pageNum, pageText] of pageTexts.entries()) {
                if (pageText.includes(compactHighlight) || compactHighlight.includes(pageText.slice(0, 220))) {
                    return pageNum;
                }
            }

            const terms = compactHighlight
                .split(' ')
                .filter((term) => term.length > 4)
                .slice(0, 24);

            if (!terms.length) {
                return requestedPage;
            }

            let bestPage = requestedPage;
            let bestScore = -1;

            for (const [pageNum, pageText] of pageTexts.entries()) {
                const score = terms.reduce((total, term) => total + (pageText.includes(term) ? 1 : 0), 0);

                if (score > bestScore) {
                    bestScore = score;
                    bestPage = pageNum;
                }
            }

            return bestScore > 0 ? bestPage : clampPage(targetPage);
        }

        function clampPage(page) {
            if (!pdfDoc) return Math.max(1, page || 1);

            return Math.min(Math.max(1, page || 1), pdfDoc.numPages);
        }

        function scrollToPage(page) {
            const pageElement = document.getElementById(`page-${page}`);

            if (pageElement) {
                const highlighted = highlightCitation(page);

                if (highlighted) {
                    highlighted.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
                    return;
                }

                pageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function highlightCitation(page) {
            document.querySelectorAll('.citation-highlight').forEach((element) => {
                element.classList.remove('citation-highlight');
            });

            const details = pageDetails.get(page);

            if (!details || !highlightText) {
                return null;
            }

            const normalizedHighlight = normalizeText(highlightText);
            const terms = normalizedHighlight
                .split(' ')
                .filter((term) => term.length > 3)
                .slice(0, 30);

            let firstMatch = null;
            const visibleDivs = details.textDivs.filter((div) => isUsableTextDiv(div));
            const selectedDivs = findBestTextDivWindow(visibleDivs, normalizedHighlight, terms);

            selectedDivs.forEach((div) => {
                div.classList.add('citation-highlight');
                firstMatch ??= div;
            });

            if (!firstMatch) {
                firstMatch = highlightFallbackTerms(visibleDivs, terms);
            }

            return firstMatch;
        }

        function isUsableTextDiv(div) {
            const text = normalizeText(div.textContent || '');

            if (text.length < 2) {
                return false;
            }

            const rect = div.getBoundingClientRect();

            return rect.width > 3 && rect.height > 3;
        }

        function findBestTextDivWindow(divs, normalizedHighlight, terms) {
            if (!divs.length || !terms.length) {
                return [];
            }

            const maxWindow = Math.min(12, divs.length);
            let best = {
                score: 0,
                divs: [],
            };

            for (let start = 0; start < divs.length; start++) {
                let combined = '';
                const windowDivs = [];

                for (let end = start; end < Math.min(divs.length, start + maxWindow); end++) {
                    const divText = normalizeText(divs[end].textContent || '');

                    if (!divText) {
                        continue;
                    }

                    combined = `${combined} ${divText}`.trim();
                    windowDivs.push(divs[end]);

                    const score = terms.reduce((total, term) => total + (combined.includes(term) ? 1 : 0), 0);
                    const exactBonus = normalizedHighlight.includes(combined) || combined.includes(normalizedHighlight.slice(0, 80)) ? 8 : 0;
                    const totalScore = score + exactBonus;

                    if (totalScore > best.score) {
                        best = {
                            score: totalScore,
                            divs: [...windowDivs],
                        };
                    }
                }
            }

            const minimumScore = Math.min(4, Math.max(1, Math.ceil(terms.length * 0.35)));

            return best.score >= minimumScore ? best.divs : [];
        }

        function highlightFallbackTerms(divs, terms) {
            let firstMatch = null;
            let matched = 0;

            divs.forEach((div) => {
                if (matched >= 8) {
                    return;
                }

                const normalizedDivText = normalizeText(div.textContent || '');
                const termMatch = terms.some((term) => normalizedDivText.includes(term));

                if (termMatch) {
                    div.classList.add('citation-highlight');
                    firstMatch ??= div;
                    matched++;
                }
            });

            return firstMatch;
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
            pageTexts.clear();
            pageDetails.clear();
            loadPdf();
        }

        loadPdf();
    </script>
</body>
</html>
