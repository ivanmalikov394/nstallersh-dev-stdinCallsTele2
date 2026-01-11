<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Диагностика клиента1</title>
<img src="https://2ip.ru/userbar/3/red/" alt="Userbar ot 2ipRU">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
        }

        .card {
            background: #fff;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .error {
            background: #ffdddd;
            color: #a30000;
            padding: 10px 15px;
            border-left: 4px solid #ff0000;
            margin-bottom: 15px;
            border-radius: 5px;
            white-space: pre-wrap;
        }

        .hidden {
            display: none;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Диагностика клиента</h1>

    <div id="error-box" class="error hidden"></div>

    <div class="card">
        <h2>IP-адрес</h2>
        <p id="ip">Загружается...</p>
    </div>

    <div class="card">
        <h2>Местоположение по IP</h2>
        <p id="location">Загружается...</p>
    </div>

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

    <button id="refresh-btn">Обновить данные</button>
</div>

<script>
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

    // Получение IP
    async function loadIP() {
        const ipEl = document.getElementById("ip");
        ipEl.textContent = "Загружается...";

        try {
            const res = await fetch("https://api.ipify.org?format=json");
            if (!res.ok) throw new Error("HTTP " + res.status);

            const data = await res.json();
            ipEl.textContent = data.ip;
            return data.ip;
        } catch (e) {
            ipEl.textContent = "Ошибка";
            showError("Не удалось получить IP: " + e.message);
            return null;
        }
    }

    // Получение геолокации по IP
    async function loadLocation(ip) {
        const locEl = document.getElementById("location");
        locEl.textContent = "Загружается...";

        if (!ip) {
            locEl.textContent = "Нет IP — нет геолокации";
            return;
        }

        try {
            const res = await fetch("https://ipapi.co/" + ip + "/json/");
            if (!res.ok) throw new Error("HTTP " + res.status);

            const data = await res.json();

            locEl.textContent =
                `${data.city || "?"}, ${data.region || "?"}, ${data.country_name || "?"}`;
        } catch (e) {
            locEl.textContent = "Ошибка";
            showError("Не удалось получить местоположение: " + e.message);
        }
    }

    // Информация об устройстве
    function loadDeviceInfo() {
        try {
            document.getElementById("browser").textContent = navigator.userAgent;
            document.getElementById("os").textContent = navigator.platform;
            document.getElementById("lang").textContent = navigator.language;
            document.getElementById("online").textContent = navigator.onLine ? "Да" : "Нет";
            document.getElementById("screen").textContent =
                `${window.screen.width}×${window.screen.height}`;
            document.getElementById("orientation").textContent =
                screen.orientation ? screen.orientation.type : "Не поддерживается";
        } catch (e) {
            showError("Ошибка при получении данных устройства: " + e.message);
        }
    }

    async function refreshAll() {
        clearError();
        const ip = await loadIP();
        await loadLocation(ip);
        loadDeviceInfo();
    }

    document.getElementById("refresh-btn").addEventListener("click", refreshAll);

    refreshAll();
</script>

</body>
</html>
