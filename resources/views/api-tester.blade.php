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
    <li><button class="tab-btn active-tab border-b-2 pb-2" data-tab="params" onclick="showTab('params')">Params</button></li>
    <li><button class="tab-btn pb-2" data-tab="auth" onclick="showTab('auth')">Authorization</button></li>
    <li><button class="tab-btn pb-2" data-tab="headers" onclick="showTab('headers')">Headers</button></li>
    <li><button class="tab-btn pb-2" data-tab="body" onclick="showTab('body')">Body</button></li>
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
                <td class="p-2 flex items-center gap-2">
                    <input type="checkbox" class="param-active" checked />
                    <input type="text" class="param-key w-full bg-gray-700 p-1 rounded" placeholder="Key" />
                </td>
                <td class="p-2"><input type="text" class="param-value w-full bg-gray-700 p-1 rounded" placeholder="Value" /></td>
            </tr>
        </tbody>
    </table>
</div>
<!-- //Authorization section  -->
<div id="auth" class="tab-content hidden">
    <div class="flex flex-col md:flex-row gap-6 items-start">
        <!-- Left Column: Dropdown -->
        <div class="w-full md:w-1/3">
            <label for="auth-type" class="text-white block mb-2 font-medium">Auth Type</label>
            <select id="auth-type" class="bg-gray-800 text-white border border-gray-600 px-3 py-2 rounded w-full">
                <option value="none">No Auth</option>
                <option value="api-key">API Key</option>
                <option value="bearer">Bearer Token</option>
                <option value="basic">Basic Auth</option>
                <option value="digest">Digest Auth</option>
                <option value="oauth1">OAuth 1.0</option>
                <option value="oauth2">OAuth 2.0</option>
                <option value="hawk">Hawk Authentication</option>
                <option value="aws">AWS Signature</option>
                <option value="ntlm">NTLM Authentication</option>
                <option value="akamai">Akamai EdgeGrid</option>
                <option value="manual">Self-handled Authorization</option>
            </select>
        </div>

        <!-- Right Column: Fields -->
        <div id="auth-fields" class="w-full md:w-2/3 bg-gray-800 border border-gray-700 rounded p-4 space-y-4 shadow-inner">
            <!-- Fields will be injected here -->
        </div>
    </div>
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
                <td class="p-2 flex items-center gap-2">
                    <input type="checkbox" class="header-active" checked />
                    <input type="text" class="w-full bg-gray-700 p-1 rounded" placeholder="Key" />
                </td>
                <td class="p-2"><input type="text" class="w-full bg-gray-700 p-1 rounded" placeholder="Value" /></td>
            </tr>
        </tbody>
    </table>
</div>
<div id="body" class="tab-content hidden">
    <!-- Body Type Radio Buttons -->
    <div class="mb-4">
        <label class="font-semibold block mb-2">Body Type</label>
        <div class="flex gap-6 text-sm">
            <label><input type="radio" name="bodyType" value="none" checked class="mr-1">None</label>
            <label><input type="radio" name="bodyType" value="form-data" class="mr-1">form-data</label>
            <label><input type="radio" name="bodyType" value="x-www-form-urlencoded" class="mr-1">x-www-form-urlencoded</label>
            <label><input type="radio" name="bodyType" value="raw" class="mr-1">raw</label>
            <label><input type="radio" name="bodyType" value="binary" class="mr-1">binary</label>
            <label><input type="radio" name="bodyType" value="graphql" class="mr-1">GraphQL</label>
        </div>
    </div>

    <!-- Dynamic Sections -->
    <div id="body-none" class="body-type-section hidden"></div>

    <div id="body-form-data" class="body-type-section hidden">
        <table class="w-full text-sm text-white border border-gray-700">
            <thead>
                <tr class="bg-gray-800 border-b border-gray-700">
                    <th class="p-2">Send</th>
                    <th class="p-2">Key</th>
                    <th class="p-2">Value</th>
                </tr>
            </thead>
            <tbody id="form-data-table">
                <tr>
                    <td class="p-2 text-center">
                        <input type="checkbox" class="form-active" checked />
                    </td>
                    <td class="p-2">
                        <input type="text" name="formKey[]" class="form-key-input w-full bg-gray-700 p-1 rounded" placeholder="Key">
                    </td>
                    <td class="p-2">
                        <input type="text" name="formValue[]" class="w-full bg-gray-700 p-1 rounded" placeholder="Value">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>



    <div id="body-x-www-form-urlencoded" class="body-type-section hidden">
        <table class="w-full text-sm text-white border border-gray-700">
            <thead>
                <tr class="bg-gray-800 border-b border-gray-700">
                    <th class="p-2">Key</th>
                    <th class="p-2">Value</th>
                </tr>
            </thead>
            <tbody id="urlencoded-table">
                <tr>
                    <td class="p-2"><input type="text" class="w-full bg-gray-700 p-1 rounded" placeholder="Key" /></td>
                    <td class="p-2"><input type="text" class="w-full bg-gray-700 p-1 rounded" placeholder="Value" /></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="body-raw" class="body-type-section hidden">
        <textarea id="raw-body" class="w-full h-40 p-3 bg-gray-800 border border-gray-700 rounded text-white" placeholder="Enter raw JSON, XML, or text..."></textarea>
    </div>

    <div id="body-binary" class="body-type-section hidden">
        <input type="file" class="block w-full text-sm text-white file:bg-gray-700 file:border-0 file:py-2 file:px-4 file:rounded file:text-white">
    </div>

    <div id="body-graphql" class="body-type-section hidden">
        <textarea id="graphql-body" class="w-full h-40 p-3 bg-gray-800 border border-gray-700 rounded text-white" placeholder="Enter GraphQL query..."></textarea>
    </div>
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
            <li>
                <button class="resp-tab-btn pb-2 text-white" onclick="showResponseTab('resp-body')">Body</button>
            </li>
            <li>
                <button class="resp-tab-btn pb-2 text-white" onclick="showResponseTab('resp-headers')">Headers</button>
            </li>
            <li>
                <button class="resp-tab-btn pb-2 text-white" onclick="showResponseTab('resp-cookies')">Cookies</button>
            </li>
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
<!-- Loader -->
<div id="loader" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="loader-box flex flex-col items-center gap-4">
        <div class="loader-spin border-4 border-blue-600 border-t-transparent rounded-full w-16 h-16 animate-spin"></div>
        <p class="text-white font-semibold">Sending request...</p>
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
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

        // Show selected tab content
        document.getElementById(tabId).classList.remove('hidden');

        // Remove 'active-tab' from all tab buttons
        document.querySelectorAll('#tabs .tab-btn').forEach(btn => btn.classList.remove('active-tab'));

        // Add 'active-tab' to the clicked one
        const activeBtn = document.querySelector(`#tabs .tab-btn[data-tab="${tabId}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active-tab');
        }
    }

    function showMainResponseTab(tabId) {
        // Hide all main-tab content
        document.querySelectorAll('.main-tab-content').forEach(el => el.classList.add('hidden'));

        // Show the selected tab content
        document.getElementById(tabId).classList.remove('hidden');

        // Remove active-tab from all main tab buttons
        document.querySelectorAll('.main-tab-btn').forEach(btn => btn.classList.remove('active-tab'));

        // Add active-tab to clicked button
        const activeBtn = document.querySelector(`.main-tab-btn[onclick="showMainResponseTab('${tabId}')"]`);
        if (activeBtn) {
            activeBtn.classList.add('active-tab');
        }
    }

    function showResponseTab(tabId) {
        // Hide all response tab contents
        document.querySelectorAll('.resp-tab-content').forEach(el => el.classList.add('hidden'));

        // Show selected content
        document.getElementById(tabId).classList.remove('hidden');

        // Remove active-tab from all buttons
        document.querySelectorAll('.resp-tab-btn').forEach(btn => btn.classList.remove('active-tab'));

        // Add active-tab to the clicked button
        const activeBtn = document.querySelector(`.resp-tab-btn[onclick="showResponseTab('${tabId}')"]`);
        if (activeBtn) {
            activeBtn.classList.add('active-tab');
        }
    }


    function escapeHtml(unsafe) {
        return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;").replace(/'/g, "&#039;");
    }

    document.getElementById('send').addEventListener('click', async () => {
        // Show loader
        document.getElementById('loader').classList.remove('hidden');

        const method = document.getElementById('method').value;
        const url = document.getElementById('url').value;
        const bodyText = document.getElementById('raw-body').value;

        const headersTable = document.querySelectorAll('#headers-table tr');
        const headers = {};
        headersTable.forEach(row => {
            const checkbox = row.querySelector('.header-active');
            const inputs = row.querySelectorAll('input');
            if (inputs.length === 3 && checkbox.checked && inputs[1].value) {
                headers[inputs[1].value] = inputs[2].value;
            }
        });

        let body;
        try {
            body = bodyText ? JSON.parse(bodyText) : undefined;
        } catch (err) {
            alert('Invalid JSON body');
            document.getElementById('loader').classList.add('hidden');
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
                    url: url,
                    method,
                    headers: {}, // Optional: include headers
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
        } finally {
            // Hide loader in all cases (success/failure)
            document.getElementById('loader').classList.add('hidden');
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
            const keyInput = lastRow.querySelector('.param-key');

            if (keyInput && keyInput.value.trim() !== "") {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td class="p-2 flex items-center gap-2">
                        <input type="checkbox" class="param-active" checked />
                        <input type="text" class="param-key w-full bg-gray-700 p-1 rounded" placeholder="Key" />
                    </td>
                    <td class="p-2"><input type="text" class="param-value w-full bg-gray-700 p-1 rounded" placeholder="Value" /></td>`;
                table.appendChild(newRow);
            }
        });
    }


    watchParamsInput();

    function watchHeadersInput() {
        const table = document.getElementById('headers-table');

        table.addEventListener('input', () => {
            const rows = [...table.querySelectorAll('tr')];
            const lastRow = rows[rows.length - 1];
            const keyInput = lastRow.querySelector('input[type="text"]');

            // Only add new row if key is filled and no empty rows exist
            const allKeys = rows.map(r => r.querySelector('input[type="text"]')?.value.trim());
            const hasBlankKey = allKeys.slice(0, -1).some(val => val === "");

            if (!hasBlankKey && keyInput && keyInput.value.trim() !== "") {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                <td class="p-2 flex items-center gap-2">
                    <input type="checkbox" class="header-active" checked />
                    <input type="text" class="w-full bg-gray-700 p-1 rounded" placeholder="Key" />
                </td>
                <td class="p-2"><input type="text" class="w-full bg-gray-700 p-1 rounded" placeholder="Value" /></td>
            `;
                table.appendChild(newRow);
            }
        });
    }
    watchHeadersInput();


    function collectParams() {
        const rows = document.querySelectorAll('#params-table tr');
        const params = [];

        rows.forEach(row => {
            const checkbox = row.querySelector('.param-active');
            const keyInput = row.querySelector('.param-key');
            const valueInput = row.querySelector('.param-value');
            if (checkbox && checkbox.checked && keyInput?.value.trim()) {
                params.push({
                    key: keyInput.value.trim(),
                    value: valueInput?.value.trim()
                });
            }
        });
        return params;
    }

    document.querySelectorAll('input[name="bodyType"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const selected = this.value;

            // Hide all body type sections
            document.querySelectorAll('.body-type-section').forEach(section => {
                section.classList.add('hidden');
            });

            // Show selected
            const selectedSection = document.getElementById(`body-${selected}`);
            if (selectedSection) {
                selectedSection.classList.remove('hidden');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const table = document.getElementById('form-data-table');

        function createFormRow() {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td class="p-2 text-center">
                <input type="checkbox" class="form-active" checked />
            </td>
            <td class="p-2">
                <input type="text" name="formKey[]" class="form-key-input w-full bg-gray-700 p-1 rounded" placeholder="Key">
            </td>
            <td class="p-2">
                <input type="text" name="formValue[]" class="w-full bg-gray-700 p-1 rounded" placeholder="Value">
            </td>
        `;
            return row;
        }

        table.addEventListener('input', (e) => {
            if (e.target && e.target.matches('.form-key-input')) {
                const rows = table.querySelectorAll('tr');
                const lastRow = rows[rows.length - 1];
                const lastKeyInput = lastRow.querySelector('.form-key-input');

                if (lastKeyInput === e.target && e.target.value.trim() !== '') {
                    table.appendChild(createFormRow());
                }
            }
        });
    });
</script>

<script>
    const authFieldConfigs = {
        "none": [],
        "api-key": [{
                label: "Key",
                id: "api-key-name"
            },
            {
                label: "Value",
                id: "api-key-value"
            },
            {
                label: "Add To",
                id: "api-key-add-to",
                type: "select",
                options: ["Header", "Query Params"]
            }
        ],
        "bearer": [{
            label: "Token",
            id: "bearer-token"
        }],
        "basic": [{
                label: "Username",
                id: "basic-username"
            },
            {
                label: "Password",
                id: "basic-password",
                type: "password"
            }
        ],
        "digest": [{
                label: "Username",
                id: "digest-username"
            },
            {
                label: "Password",
                id: "digest-password",
                type: "password"
            },
            {
                label: "Realm",
                id: "digest-realm"
            },
            {
                label: "Nonce",
                id: "digest-nonce"
            },
            {
                label: "Algorithm",
                id: "digest-algo"
            }
        ],
        "oauth1": [{
                label: "Consumer Key",
                id: "oauth1-consumer-key"
            },
            {
                label: "Consumer Secret",
                id: "oauth1-consumer-secret"
            },
            {
                label: "Access Token",
                id: "oauth1-token"
            },
            {
                label: "Token Secret",
                id: "oauth1-token-secret"
            },
            {
                label: "Signature Method",
                id: "oauth1-signature",
                placeholder: "e.g. HMAC-SHA1"
            },
            {
                label: "Timestamp",
                id: "oauth1-timestamp"
            },
            {
                label: "Nonce",
                id: "oauth1-nonce"
            },
            {
                label: "Version",
                id: "oauth1-version"
            }
        ],
        "oauth2": [{
                label: "Grant Type",
                id: "oauth2-grant",
                placeholder: "Authorization Code / Client Credentials / Password"
            },
            {
                label: "Access Token URL",
                id: "oauth2-token-url"
            },
            {
                label: "Client ID",
                id: "oauth2-client-id"
            },
            {
                label: "Client Secret",
                id: "oauth2-client-secret"
            },
            {
                label: "Scope",
                id: "oauth2-scope"
            },
            {
                label: "Username",
                id: "oauth2-username"
            },
            {
                label: "Password",
                id: "oauth2-password",
                type: "password"
            },
            {
                label: "Auth URL",
                id: "oauth2-auth-url"
            },
            {
                label: "Redirect URI",
                id: "oauth2-redirect-uri"
            },
            {
                label: "Code Verifier",
                id: "oauth2-code-verifier"
            }
        ],
        "hawk": [{
                label: "Hawk ID",
                id: "hawk-id"
            },
            {
                label: "Hawk Key",
                id: "hawk-key"
            },
            {
                label: "Algorithm",
                id: "hawk-algo"
            }
        ],
        "aws": [{
                label: "Access Key",
                id: "aws-access"
            },
            {
                label: "Secret Key",
                id: "aws-secret"
            },
            {
                label: "AWS Region",
                id: "aws-region"
            },
            {
                label: "Service Name",
                id: "aws-service"
            },
            {
                label: "Session Token (optional)",
                id: "aws-session"
            }
        ],
        "ntlm": [{
                label: "Username",
                id: "ntlm-username"
            },
            {
                label: "Password",
                id: "ntlm-password",
                type: "password"
            },
            {
                label: "Domain",
                id: "ntlm-domain"
            },
            {
                label: "Workstation",
                id: "ntlm-workstation"
            }
        ],
        "akamai": [{
                label: "Access Token",
                id: "akamai-access"
            },
            {
                label: "Client Token",
                id: "akamai-client-token"
            },
            {
                label: "Client Secret",
                id: "akamai-client-secret"
            },
            {
                label: "Host",
                id: "akamai-host"
            }
        ],
        "manual": []
    };

    function renderAuthFields(type) {
        const container = document.getElementById('auth-fields');
        container.innerHTML = "";

        const fields = authFieldConfigs[type] || [];

        fields.forEach(field => {
            const inputType = field.type === "password" ? "password" : "text";

            if (field.type === "select") {
                const label = `<label for="${field.id}" class="text-white block mb-1">${field.label}</label>`;
                const options = field.options.map(opt => `<option value="${opt.toLowerCase()}">${opt}</option>`).join('');
                const select = `<select id="${field.id}" class="bg-gray-700 text-white w-full p-2 rounded">${options}</select>`;
                container.innerHTML += `<div>${label}${select}</div>`;
            } else {
                const label = `<label for="${field.id}" class="text-white block mb-1">${field.label}</label>`;
                const input = `<input id="${field.id}" type="${inputType}" class="bg-gray-700 text-white w-full p-2 rounded" placeholder="${field.placeholder || ''}" />`;
                container.innerHTML += `<div>${label}${input}</div>`;
            }
        });

        if (type === "none" || type === "manual") {
            container.innerHTML = `<p class="text-gray-400 text-sm">No authentication fields required. You can add headers manually if needed.</p>`;
        }
    }

    document.getElementById('auth-type').addEventListener('change', (e) => {
        renderAuthFields(e.target.value);
    });

    // Render default (none) on page load
    renderAuthFields('none');
</script>


@endsection