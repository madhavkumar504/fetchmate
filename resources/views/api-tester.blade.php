@extends('layouts.app')

@section('title', 'Fetch Mate - API Tester')

@section('head')
{{-- You can include additional CSS/JS if needed --}}
@endsection

@section('content')
<!-- Request Bar -->
<div class="flex gap-3 mb-5">
    <select id="method" class="bg-gray-800 border border-gray-600 text-white rounded px-3 py-2">
        <option>GET</option>
        <option>POST</option>
        <option>PUT</option>
        <option>DELETE</option>
        <option>PATCH</option>
    </select>
    <input type="text" id="url" onpaste="handleCurlPaste(event)" class="flex-1 px-3 py-2 rounded bg-gray-800 border border-gray-600"
        placeholder="Enter request URL or paste curl command here" />
    <button id="send" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded text-white">Send</button>
</div>

<!-- Tabs -->
<ul class="flex space-x-4 border-b border-gray-700 mb-4" id="tabs">
    <li><button class="tab-btn border-b-2 border-blue-500 pb-2" onclick="showTab('params')">Params</button></li>
    <li><button class="tab-btn pb-2" onclick="showTab('auth')">Authorization</button></li>
    <li><button class="tab-btn pb-2" onclick="showTab('headers')">Headers</button></li>
    <li><button class="tab-btn pb-2" onclick="showTab('body')">Body</button></li>
</ul>

<!-- Tab Contents -->
<div id="params" class="tab-content block">
    <table class="w-full text-sm text-white border border-gray-700">
        <thead>
            <tr class="bg-gray-800 border-b border-gray-700">
                <th class="p-2 text-left">Key</th>
                <th class="p-2 text-left">Value</th>
            </tr>
        </thead>
        <tbody id="params-table">
            <tr>
                <td class="p-2"><input type="text" class="param-key w-full bg-gray-700 p-1 rounded" /></td>
                <td class="p-2"><input type="text" class="param-value w-full bg-gray-700 p-1 rounded" /></td>
            </tr>
        </tbody>
    </table>
</div>
<div id="auth" class="tab-content hidden">
    <p class="text-gray-400">Authorization settings will appear here.</p>
</div>
<div id="headers" class="tab-content hidden">
    <table class="w-full text-sm text-white border border-gray-700">
        <thead>
            <tr class="bg-gray-800 border-b border-gray-700">
                <th class="p-2 text-left">Key</th>
                <th class="p-2 text-left">Value</th>
            </tr>
        </thead>
        <tbody id="headers-table">
            <tr>
                <td class="p-2"><input type="text" class="w-full bg-gray-700 p-1 rounded" /></td>
                <td class="p-2"><input type="text" class="w-full bg-gray-700 p-1 rounded" /></td>
            </tr>
        </tbody>
    </table>
</div>
<div id="body" class="tab-content hidden">
    <textarea id="raw-body" class="w-full h-40 p-3 bg-gray-800 border border-gray-700 rounded text-white"></textarea>
</div>

<!-- Response Section -->
<div class="mt-8">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold">Response</h2>
        <div id="response-status" class="text-sm text-gray-400"></div>
    </div>

    <!-- Response Tabs -->
    <ul class="flex space-x-4 border-b border-gray-700 mt-4">
        <li><button class="main-tab-btn border-b-2 border-blue-500 pb-2" onclick="showMainResponseTab('structured')">Structured</button></li>
        <li><button class="main-tab-btn pb-2" onclick="showMainResponseTab('preview')">Preview</button></li>
    </ul>

    <!-- Structured Tab -->
    <div id="structured" class="main-tab-content block mt-4">
        <!-- Nested Tabs -->
        <ul class="flex space-x-4 border-b border-gray-700">
            <li><button class="resp-tab-btn border-b-2 border-blue-500 pb-2" onclick="showResponseTab('resp-body')">Body</button></li>
            <li><button class="resp-tab-btn pb-2" onclick="showResponseTab('resp-headers')">Headers</button></li>
            <li><button class="resp-tab-btn pb-2" onclick="showResponseTab('resp-cookies')">Cookies</button></li>
        </ul>
        <div class="mt-4">
            <div id="resp-body" class="resp-tab-content block">
                <pre id="response-body" class="bg-black p-4 rounded overflow-auto text-green-400 max-h-64"></pre>
            </div>
            <div id="resp-headers" class="resp-tab-content hidden">
                <table class="w-full text-sm border border-gray-700 text-white">
                    <thead>
                        <tr>
                            <th class="p-2 text-left">Key</th>
                            <th class="p-2 text-left">Value</th>
                        </tr>
                    </thead>
                    <tbody id="response-headers-table"></tbody>
                </table>
            </div>
            <div id="resp-cookies" class="resp-tab-content hidden">
                <pre id="response-cookies" class="bg-black p-4 rounded overflow-auto text-yellow-400 max-h-64"></pre>
            </div>
        </div>
    </div>

    <!-- Preview Tab -->
    <div id="preview" class="main-tab-content hidden mt-4">
        <div class="flex justify-end mb-2">
            <select id="preview-format" class="bg-gray-800 text-white border border-gray-600 px-2 py-1 rounded">
                <option value="json">JSON</option>
                <option value="html">HTML</option>
                <option value="text">Text</option>
            </select>
        </div>
        <iframe id="html-preview" class="w-full h-64 bg-white text-black rounded p-3"></iframe>
    </div>
</div>

<script>
    function handleCurlPaste(event) {
        const clipboardData = event.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('text');

        if (pastedData.startsWith('curl ')) {
            event.preventDefault();
            parseCurlFromText(pastedData);
        }
    }

    function parseCurlFromText(input) {
        const urlMatch = input.match(/curl\s+(?:-[^\s]+\s+|--[^\s]+\s+)*['"]([^'"]+)['"]/);
        if (urlMatch) {
            document.getElementById('url').value = urlMatch[1];
        }

        const methodMatch = input.match(/--request\s+(\w+)/i);
        document.getElementById('method').value = methodMatch ? methodMatch[1].toUpperCase() : (input.includes("--data") || input.includes("--form") ? "POST" : "GET");

        const headers = [...input.matchAll(/--header\s+'([^:]+):\s*([^']+)'/g)];
        const table = document.getElementById('headers-table');
        table.innerHTML = "";
        headers.forEach(h => {
            const row = document.createElement('tr');
            row.innerHTML = `<td class="p-2"><input value="${h[1]}" class="w-full bg-gray-700 p-1 rounded" /></td>
                             <td class="p-2"><input value="${h[2]}" class="w-full bg-gray-700 p-1 rounded" /></td>`;
            table.appendChild(row);
        });

        const dataMatch = input.match(/--data(?:-raw)?\s+'([\s\S]+?)'/);
        if (dataMatch) {
            document.getElementById('raw-body').value = dataMatch[1].trim();
        } else {
            const formMatches = [...input.matchAll(/--form\s+'?([^=]+)="?([^"]+)"?'?/g)];
            if (formMatches.length > 0) {
                const formData = {};
                formMatches.forEach(match => {
                    const key = match[1]?.trim();
                    const value = match[2]?.trim();
                    if (key && value !== undefined) {
                        formData[key] = value;
                    }
                });
                document.getElementById('raw-body').value = JSON.stringify(formData, null, 2);
            }
        }
    }

    function showTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(tabId).classList.remove('hidden');
    }

    function showMainResponseTab(tabId) {
        document.querySelectorAll('.main-tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(tabId).classList.remove('hidden');
    }

    function showResponseTab(tabId) {
        document.querySelectorAll('.resp-tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(tabId).classList.remove('hidden');
    }

    function escapeHtml(unsafe) {
        return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;").replace(/'/g, "&#039;");
    }

    document.getElementById('send').addEventListener('click', async () => {
        const method = document.getElementById('method').value;
        const url = document.getElementById('url').value;
        const bodyText = document.getElementById('raw-body').value;

        const headersTable = document.querySelectorAll('#headers-table tr');
        const headers = {};
        headersTable.forEach(row => {
            const cells = row.querySelectorAll('input');
            if (cells.length === 2 && cells[0].value) {
                headers[cells[0].value] = cells[1].value;
            }
        });

        let body;
        try {
            body = bodyText ? JSON.parse(bodyText) : undefined;
        } catch (err) {
            alert('Invalid JSON body');
            return;
        }

        try {
            const response = await fetch('/api/send-request', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    url: finalUrl,
                    method,
                    headers: {}, // fill if needed
                    body
                }),
            });


            const text = await response.text();
            let parsed;
            try {
                parsed = JSON.parse(text);
            } catch {
                parsed = text;
            }

            document.getElementById('response-status').innerText = `Status: ${response.status}`;
            if (parsed && typeof parsed === 'object' && parsed.body !== undefined) {
                document.getElementById('response-body').innerText = JSON.stringify(parsed.body, null, 2);
            } else {
                document.getElementById('response-body').innerText = JSON.stringify(parsed, null, 2);
            }
            renderResponseHeaders(response.headers);
            renderCookies(response.headers);

            const format = document.getElementById('preview-format').value;
            let previewContent = typeof parsed === 'string' ? parsed : JSON.stringify(parsed, null, 2);

            if (format === 'html') {
                document.getElementById('html-preview').srcdoc = parsed;
            } else {
                document.getElementById('html-preview').srcdoc = `<html><body><pre>${escapeHtml(previewContent)}</pre></body></html>`;
            }
        } catch (err) {
            document.getElementById('response-body').innerText = 'Error: ' + err.message;
            document.getElementById('html-preview').srcdoc = `<pre>${err.message}</pre>`;
        }
    });

    function renderResponseHeaders(headers) {
        const table = document.getElementById('response-headers-table');
        table.innerHTML = '';
        for (const [key, value] of headers.entries()) {
            const row = document.createElement('tr');
            row.innerHTML = `<td class="p-2">${key}</td><td class="p-2">${value}</td>`;
            table.appendChild(row);
        }
    }

    function renderCookies(headers) {
        const cookies = headers.get('set-cookie');
        document.getElementById('response-cookies').innerText = cookies || 'No cookies found';
    }

    function watchParamsInput() {
        const table = document.getElementById('params-table');

        table.addEventListener('input', () => {
            const rows = [...table.querySelectorAll('tr')];
            const lastRow = rows[rows.length - 1];
            const key = lastRow.querySelector('.param-key').value;
            const value = lastRow.querySelector('.param-value').value;

            // If last row is filled, add a new empty row
            if (key && value) {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                <td class="p-2"><input type="text" class="param-key w-full bg-gray-700 p-1 rounded" /></td>
                <td class="p-2"><input type="text" class="param-value w-full bg-gray-700 p-1 rounded" /></td>`;
                table.appendChild(newRow);
            }
        });
    }

    watchParamsInput();

    function collectParams() {
        const rows = document.querySelectorAll('#params-table tr');
        const params = [];

        rows.forEach(row => {
            const key = row.querySelector('.param-key')?.value.trim();
            const value = row.querySelector('.param-value')?.value.trim();
            if (key) {
                params.push({
                    key,
                    value
                });
            }
        });

        return params;
    }
</script>
@endsection 