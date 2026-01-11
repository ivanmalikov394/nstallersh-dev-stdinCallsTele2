<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Диагностика клиента</title>

    <style>
        :root {
            --bg-dark: #000000;
            --card-dark: #0a0a0a;
            --text-dark: #f5f5f5;
            --accent-dark: #bb86fc;

            --bg-light: #f2f2f2;
            --card-light: #ffffff;
            --text-light: #111111;
            --accent-light: #0066ff;

            --border-radius: 10px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        body.dark {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        body.light {
            background-color: var(--bg-light);
            color: var(--text-light);
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        h1 {
            margin: 0;
            font-size: 1.6rem;
        }

        .theme-toggle {
            padding: 8px 14px;
            font-size: 0.9rem;
            border-radius: 999px;
            border: 1px solid;
            cursor: pointer;
            background: transparent;
        }

        body.dark .theme-toggle {
            color: var(--text-dark);
            border-color: var(--accent-dark);
        }

        body.light .theme-toggle {
            color: var(--text-light);
            border-color: var(--accent-light);
        }

        .card {
            border-radius: var(--border-radius);
            padding: 15px 18px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.35);
        }

        body.dark .card {
            background-color: var(--card-dark);
        }

        body.light .card {
            background-color: var(--card-light);
        }

        .card h2 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .error {
            padding: 10px 15px;
            border-left: 4px solid #ff4444;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            font-size: 0.9rem;
            white-space: pre-wrap;
        }

        body.dark .error {
            background: #400000;
            color: #ffcccc;
        }

        body.light .error {
            background: #ffdddd;
            color: #a30000;
        }

        .hidden {
            display: none;
        }

        button.action {
            padding: 10px 18px;
            font-size: 0.95rem;
            cursor: pointer;
            border-radius: 999px;
            border: none;
            margin-top: 5px;
        }

        body.dark button.action {
            background: var(--accent-dark);
            color: #000;
        }

        body.light button.action {
            background: var(--accent-light);
            color: #fff;
        }

        ul {
            padding-left: 18px;
            margin: 0;
        }

        .kv-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            font-size: 0.95rem;
        }

        .kv-key {
            font-weight: bold;
        }

        .flex-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .speed-value {
            font-weight: bold;
        }

        .muted {
            opacity: 0.7;
            font-size: 0.85rem;
        }

        img {
            max-width: 100%;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 1.3rem;
            }
            .card {
                padding: 12px 14px;
            }
        }
    </style>
</head>
<body class="dark">

<div class="container">
    <div class="header-row">
        <h1>Диагностика клиента</h1>
        <button id="theme-toggle" class="theme-toggle">Светлая тема</button>
    </div>

    <!-- Userbar 2ip -->
    <div class="card">
        <h2>Userbar 2ip</h2>
        <a href="https://2ip.ru/" target="_blank">
            <img src="https://2ip.ru/bar/ip12.gif" alt="Userbar 2ip">
        </a>
        <div class="muted">Кликабельно, ведёт на 2ip.ru</div>
    </div>

    <div id="error-box" class="error hidden"></div>

    <!-- Блок IP -->
    <div class="card">
        <h2>IP-адреса</h2>
        <p class="kv-row">
            <span class="kv-key">IP (ipify):</span>
            <span id="ipify-ip">Загружается...</span>
        </p>
        <p class="kv-row">
            <span class="kv-key">IP (Яндекс):</span>
            <span id="yandex-ip">Загружается...</span>
        </p>
        <p class="kv-row">
            <span class="kv-key">WebRTC IP:</span>
            <span id="webrtc-ip">Проверяется...</span>
        </p>
        <div class="muted">
            Если WebRTC IP не определяется — значит браузер/политика его скрывают (это нормально и безопаснее).
        </div>
    </div>

    <!-- Геолокация -->
    <div class="card">
        <h2>Местоположение по IP (ipify / ipapi)</h2>
        <p id="location">Загружается...</p>
        <p class="kv-row">
            <span class="kv-key">ASN / провайдер:</span>
            <span id="asn">Загружается...</span>
        </p>
    </div>

    <!-- Информация об устройстве -->
    <div class="card">
        <h2>Информация об устройстве</h2>
        <ul>
            <li><strong>Браузер:</strong> <span id="browser"></span></li>
            <li><strong>ОС:</strong> <span id="os"></span></li>
            <li><strong>Язык:</strong> <span id="lang"></span></li>
            <li><strong>Онлайн:</strong> <span id="online"></span></li>
            <li><strong>Разрешение экрана:</strong> <span id="screen"></span></li>
            <li><strong>Ориентация:</strong> <span id="orientation"></span></li>
        </ul>
    </div>

    <!-- Скорость интернета -->
    <div class="card">
        <h2>Примерная скорость соединения</h2>
        <p>
            <span class="kv-key">Скорость загрузки:</span>
            <span id="speed-download" class="speed-value">Не измерялась</span>
        </p>
        <div class="muted">
            Лёгкий тест: скачивание небольшого файла. Значения примерные, зависят от нагрузки и кэша.
        </div>
        <button id="speed-btn" class="action">Измерить скорость</button>
    </div>

    <button id="refresh-btn" class="action">Обновить все данные</button>
</div>

<script>
    // ---------- ТЕМА ----------
    const THEME_KEY = "diag_theme";

    function applyTheme(theme) {
        const body = document.body;
        const btn = document.getElementById("theme-toggle");

        body.classList.remove("dark", "light");
        body.classList.add(theme);

        if (theme === "dark") {
            btn.textContent = "Светлая тема";
        } else {
            btn.textContent = "Тёмная тема";
        }
    }

    function initTheme() {
        let saved = localStorage.getItem(THEME_KEY);
        if (!saved) {
            const prefersDark = window.matchMedia &&
                window.matchMedia("(prefers-color-scheme: dark)").matches;
            saved = prefersDark ? "dark" : "dark"; // по умолчанию тёмная
        }
        applyTheme(saved);
    }

    document.getElementById("theme-toggle").addEventListener("click", () => {
        const current = document.body.classList.contains("dark") ? "dark" : "light";
        const next = current === "dark" ? "light" : "dark";
        localStorage.setItem(THEME_KEY, next);
        applyTheme(next);
    });

    initTheme();

    // ---------- ОШИБКИ ----------
    function showError(message) {
        const box = document.getElementById("error-box");
        box.classList.remove("hidden");
        box.textContent = "Ошибка: " + message;
    }

    function clearError() {
        const box = document.getElementById("error-box");
        box.classList.add("hidden");
        box.textContent = "";
    }

    // ---------- IP через ipify ----------
    async function loadIP() {
        const ipEl = document.getElementById("ipify-ip");
        ipEl.textContent = "Загружается...";

        try {
            const res = await fetch("https://api.ipify.org?format=json");
            if (!res.ok) throw new Error("HTTP " + res.status);

            const data = await res.json();
            ipEl.textContent = data.ip;
            return data.ip;
        } catch (e) {
            ipEl.textContent = "Ошибка";
            showError("Не удалось получить IP (ipify): " + e.message);
            return null;
        }
    }

    // ---------- IP через Яндекс ----------
    async function loadYandexIP() {
        const yandexEl = document.getElementById("yandex-ip");
        yandexEl.textContent = "Загружается...";

        try {
            const res = await fetch("https://yandex.ru/internet/api/v0/ip");
            if (!res.ok) throw new Error("HTTP " + res.status);

            const data = await res.json();
            yandexEl.textContent = data.ip;
            return data.ip;
        } catch (e) {
            yandexEl.textContent = "Ошибка";
            showError("Ошибка получения IP от Яндекс: " + e.message);
            return null;
        }
    }

    // ---------- Геолокация + ASN по ipapi ----------
    async function loadLocation(ip) {
        const locEl = document.getElementById("location");
        const asnEl = document.getElementById("asn");

        locEl.textContent = "Загружается...";
        asnEl.textContent = "Загружается...";

        if (!ip) {
            locEl.textContent = "Нет IP — нет геолокации";
            asnEl.textContent = "Нет данных";
            return;
        }

        try {
            const res = await fetch("https://ipapi.co/" + ip + "/json/");
            if (!res.ok) throw new Error("HTTP " + res.status);

            const data = await res.json();

            const city = data.city || "?";
            const region = data.region || "?";
            const country = data.country_name || "?";
            locEl.textContent = `${city}, ${region}, ${country}`;

            const asn = data.asn || "";
            const org = data.org || data.org_name || "";
            if (asn || org) {
                asnEl.textContent = `${asn} ${org}`.trim();
            } else {
                asnEl.textContent = "Нет данных";
            }
        } catch (e) {
            locEl.textContent = "Ошибка";
            asnEl.textContent = "Ошибка";
            showError("Не удалось получить местоположение / ASN: " + e.message);
        }
    }

    // ---------- Информация об устройстве ----------
    function loadDeviceInfo() {
        document.getElementById("browser").textContent = navigator.userAgent;
        document.getElementById("os").textContent = navigator.platform;
        document.getElementById("lang").textContent = navigator.language;
        document.getElementById("online").textContent = navigator.onLine ? "Да" : "Нет";
        document.getElementById("screen").textContent =
            `${window.screen.width}×${window.screen.height}`;
        document.getElementById("orientation").textContent =
            screen.orientation ? screen.orientation.type : "Не поддерживается";
    }

    // ---------- WebRTC IP ----------
    function loadWebRTCIP() {
        const webrtcEl = document.getElementById("webrtc-ip");
        webrtcEl.textContent = "Проверяется...";

        const RTCPeerConnection = window.RTCPeerConnection ||
            window.mozRTCPeerConnection ||
            window.webkitRTCPeerConnection;

        if (!RTCPeerConnection) {
            webrtcEl.textContent = "WebRTC недоступен в этом браузере";
            return;
        }

        let ipFound = false;

        try {
            const pc = new RTCPeerConnection({
                iceServers: []
            });

            pc.createDataChannel("");

            pc.onicecandidate = (ice) => {
                if (!ice || !ice.candidate || !ice.candidate.candidate) {
                    if (!ipFound) {
                        webrtcEl.textContent = "IP не обнаружен (скорее всего, защищён)";
                    }
                    return;
                }
                const candidate = ice.candidate.candidate;
                const ipMatch = candidate.match(/(\d{1,3}(\.\d{1,3}){3})/);
                if (ipMatch) {
                    ipFound = true;
                    webrtcEl.textContent = ipMatch[1];
                }
            };

            pc.createOffer()
                .then((sdp) => pc.setLocalDescription(sdp))
                .catch(() => {
                    webrtcEl.textContent = "Ошибка при получении WebRTC IP";
                });

            setTimeout(() => {
                if (!ipFound && webrtcEl.textContent === "Проверяется...") {
                    webrtcEl.textContent = "IP не обнаружен (скорее всего, скрыт политиками)";
                }
                pc.close();
            }, 3000);
        } catch (e) {
            webrtcEl.textContent = "WebRTC IP недоступен: " + e.message;
        }
    }

    // ---------- Лёгкий тест скорости ----------
    async function testSpeed() {
        const speedEl = document.getElementById("speed-download");
        speedEl.textContent = "Измерение...";

        // небольшой файл; важно, чтобы кэш не мешал → добавляем случайный параметр
        const testUrl = "https://speed.hetzner.de/100MB.bin"; // большой файл, мы оборвём рано
        const CHUNK_MS = 1500; // время, через которое оборвём

        try {
            const controller = new AbortController();
            const signal = controller.signal;
            const startTime = performance.now();
            let bytesLoaded = 0;

            const res = await fetch(testUrl + "?cachebust=" + Math.random(), { signal });

            if (!res.body || !res.ok) {
                throw new Error("Ответ без body или HTTP " + res.status);
            }

            const reader = res.body.getReader();

            const readChunk = () => reader.read().then(({ done, value }) => {
                if (done) {
                    return;
                }
                bytesLoaded += value.byteLength;
                const now = performance.now();
                const elapsed = now - startTime;

                if (elapsed >= CHUNK_MS) {
                    controller.abort();
                    return;
                }
                return readChunk();
            });

            await readChunk().catch(() => {});

            const elapsedSec = (performance.now() - startTime) / 1000;
            if (elapsedSec <= 0 || bytesLoaded <= 0) {
                speedEl.textContent = "Не удалось измерить";
                return;
            }

            const bitsPerSec = (bytesLoaded * 8) / elapsedSec;
            const mbps = bitsPerSec / (1024 * 1024);
            speedEl.textContent = mbps.toFixed(2) + " Мбит/с (примерно)";
        } catch (e) {
            speedEl.textContent = "Ошибка измерения";
            showError("Ошибка при измерении скорости: " + e.message);
        }
    }

    document.getElementById("speed-btn").addEventListener("click", testSpeed);

    // ---------- Главная функция ----------
    async function refreshAll() {
        clearError();
        loadDeviceInfo();
        loadWebRTCIP();

        const ip = await loadIP();     // ipify
        await loadYandexIP();          // Яндекс
        await loadLocation(ip);        // гео + ASN через ipapi
    }

    document.getElementById("refresh-btn").addEventListener("click", refreshAll);

    // первый запуск
    refreshAll();
</script>

</body>
</html>
