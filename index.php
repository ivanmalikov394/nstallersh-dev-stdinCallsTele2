<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Локальная диагностика браузера</title>

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
    <h1>Локальная диагностика браузера</h1>

    <div id="error-box" class="error hidden"></div>

    <div class="card">
        <h2>Геолокация (браузер)</h2>
        <p id="geo">Загружается...</p>
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

    // Геолокация из браузера
    function loadGeo() {
        const geoEl = document.getElementById("geo");
        geoEl.textContent = "Запрос геолокации...";

        if (!navigator.geolocation) {
            geoEl.textContent = "Геолокация не поддерживается браузером";
            return;
        }

        navigator.geolocation.getCurrentPosition(
            pos => {
                const { latitude, longitude, accuracy } = pos.coords;
                geoEl.textContent =
                    `Широта: ${latitude}\nДолгота: ${longitude}\nТочность: ${accuracy} м`;
            },
            err => {
                geoEl.textContent = "Ошибка";
                showError("Геолокация недоступна: " + err.message);
            }
        );
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

    function refreshAll() {
        clearError();
        loadGeo();
        loadDeviceInfo();
    }

    document.getElementById("refresh-btn").addEventListener("click", refreshAll);

    refreshAll();
</script>

</body>
</html>
