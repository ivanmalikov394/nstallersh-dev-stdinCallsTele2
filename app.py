#!/usr/bin/env python3
from flask import Flask, render_template, request, jsonify
import speedtest
import subprocess
import json
import os
import time
import math

app = Flask(__name__, template_folder="templates")
LOG_DIR = "logs"
os.makedirs(LOG_DIR, exist_ok=True)

def append_log(filename, obj):
    path = os.path.join(LOG_DIR, filename)
    data = []
    if os.path.exists(path):
        try:
            with open(path, "r", encoding="utf-8") as f:
                data = json.load(f)
        except Exception:
            data = []
    data.append(obj)
    with open(path, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=2)

def run_speedtest():
    st = speedtest.Speedtest()
    st.get_best_server()
    download_b = st.download()
    upload_b = st.upload(pre_allocate=False)
    res = st.results.dict()
    return {
        "timestamp": time.time(),
        "download_Mbps": round(download_b/1e6, 3),
        "upload_Mbps": round(upload_b/1e6, 3),
        "ping_ms": res.get("ping"),
        "server": res.get("server"),
        "raw": res
    }

def ping_host(host="8.8.8.8", count=8):
    try:
        out = subprocess.check_output(["ping", "-c", str(count), host], stderr=subprocess.STDOUT, text=True)
    except subprocess.CalledProcessError as e:
        out = e.output or ""
    loss = None
    rtt = {}
    for line in out.splitlines():
        if "packet loss" in line:
            try:
                parts = line.split(",")
                loss = parts[2].strip()
            except Exception:
                loss = None
        if "rtt min/avg/max/mdev" in line or "round-trip min/avg/max/stddev" in line:
            try:
                rhs = line.split("=")[1].strip().split(" ")[0]
                min_v, avg_v, max_v, mdev_v = rhs.split("/")
                rtt = {
                    "min_ms": float(min_v),
                    "avg_ms": float(avg_v),
                    "max_ms": float(max_v),
                    "mdev_ms": float(mdev_v)
                }
            except Exception:
                rtt = {}
    return {"host": host, "output": out, "packet_loss": loss, "rtt": rtt}

def fspl_db(freq_mhz, dist_km):
    if dist_km <= 0:
        return None
    return 32.44 + 20*math.log10(dist_km) + 20*math.log10(freq_mhz)

def hata_urban_db(freq_mhz, hb_m, hr_m, dist_km):
    if dist_km <= 0:
        return None
    a_hr = (1.1 * math.log10(freq_mhz) - 0.7) * hr_m - (1.56 * math.log10(freq_mhz) - 0.8)
    L = 69.55 + 26.16*math.log10(freq_mhz) - 13.82*math.log10(hb_m) - a_hr + (44.9 - 6.55*math.log10(hb_m))*math.log10(dist_km)
    return L

def estimate_range_for_threshold(freq_mhz, eirp_dbm, rx_sensitivity_dbm, model="fspl", hb_m=30, hr_m=1.5):
    threshold = eirp_dbm - rx_sensitivity_dbm
    if model == "fspl":
        val = threshold - 32.44 - 20*math.log10(freq_mhz)
        d_km = 10**(val/20)
        return d_km
    elif model == "hata_urban":
        for d in [x*0.01 for x in range(1, 20000)]:
            L = hata_urban_db(freq_mhz, hb_m, hr_m, d)
            if L >= threshold:
                return d
        return None
    else:
        return None

@app.route("/")
def index():
    return render_template("index.html")

@app.route("/api/speedtest", methods=["POST"])
def api_speedtest():
    params = request.json or {}
    try:
        st_res = run_speedtest()
    except Exception as e:
        return jsonify({"ok": False, "error": str(e)}), 500
    ping_target = params.get("ping_target", "8.8.8.8")
    ping_res = ping_host(ping_target, count=8)
    rec = {"timestamp": time.time(), "speedtest": st_res, "ping": ping_res}
    append_log("speedtests.json", rec)
    return jsonify({"ok": True, "result": rec})

@app.route("/api/calc", methods=["POST"])
def api_calc():
    body = request.json or {}
    try:
        tx_power = float(body.get("tx_power_dbm", 43))
        tx_gain = float(body.get("tx_antenna_dbi", 15))
        cable_loss = float(body.get("cable_loss_db", 2))
        freq = float(body.get("freq_mhz", 1800))
        dist = float(body.get("distance_km", 1.0))
        rx_sens = float(body.get("rx_sensitivity_dbm", -100))
        hb = float(body.get("hb_m", 30))
        hr = float(body.get("hr_m", 1.5))
        model = body.get("model", "fspl")
    except Exception as e:
        return jsonify({"ok": False, "error": "Неверные входные параметры: " + str(e)}), 400

    eirp = tx_power + tx_gain - cable_loss
    fspl = fspl_db(freq, dist)
    recv_fspl = None
    if fspl is not None:
        recv_fspl = eirp - fspl
    hata_loss = None
    recv_hata = None
    if model == "hata" or model == "hata_urban":
        hata_loss = hata_urban_db(freq, hb, hr, dist)
        if hata_loss is not None:
            recv_hata = eirp - hata_loss
    est_range_fspl = estimate_range_for_threshold(freq, eirp, rx_sens, model="fspl", hb_m=hb, hr_m=hr)
    est_range_hata = None
    if model == "hata" or model == "hata_urban":
        est_range_hata = estimate_range_for_threshold(freq, eirp, rx_sens, model="hata_urban", hb_m=hb, hr_m=hr)
    out = {
        "timestamp": time.time(),
        "input": {"tx_power_dbm": tx_power, "tx_antenna_dbi": tx_gain, "cable_loss_db": cable_loss, "freq_mhz": freq,
                  "distance_km": dist, "rx_sensitivity_dbm": rx_sens, "hb_m": hb, "hr_m": hr, "model": model},
        "eirp_dbm": round(eirp, 3),
        "fspl_db": round(fspl, 3) if fspl is not None else None,
        "received_fspl_dbm": round(recv_fspl, 3) if recv_fspl is not None else None,
        "hata_loss_db": round(hata_loss, 3) if hata_loss is not None else None,
        "received_hata_dbm": round(recv_hata, 3) if recv_hata is not None else None,
        "est_range_km_fspl": round(est_range_fspl, 3) if est_range_fspl is not None else None,
        "est_range_km_hata": round(est_range_hata, 3) if est_range_hata is not None else None
    }
    append_log("calculations.json", out)
    return jsonify({"ok": True, "result": out})

@app.route("/api/logs/<name>", methods=["GET"])
def api_get_logs(name):
    if name not in ("speedtests.json", "calculations.json"):
        return jsonify({"ok": False, "error": "unknown log"}), 404
    path = os.path.join(LOG_DIR, name)
    if not os.path.exists(path):
        return jsonify({"ok": True, "data": []})
    with open(path, "r", encoding="utf-8") as f:
        data = json.load(f)
    return jsonify({"ok": True, "data": data})

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
