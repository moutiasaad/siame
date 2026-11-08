"""Simulateur de cartes Arduino + SIM900 (démonstration sans matériel).

Joue le rôle du firmware : envoie température / humidité à l'API toutes les N secondes,
applique l'état on/off renvoyé, et peut déclencher une alarme intrusion.

    python simulate_device.py live --url http://localhost:8090/api/device.php --key change-me
    python simulate_device.py intrusion --machine 1 --url ... --key ...
    python simulate_device.py backfill --hours 24 | mysql -u root siame     # historique de démonstration

Aucune dépendance : bibliothèque standard Python uniquement.
"""
import argparse
import math
import random
import sys
import time
import urllib.parse
import urllib.request
from datetime import datetime, timedelta

# Profil de chaque machine : température de l'armoire / du groupe hydraulique (°C)
PROFILES = {
    1: {"base": 44.0, "amp": 4.0},              # Ligne 1 : fonctionnement normal
    2: {"base": 51.0, "amp": 5.0, "drift": True},  # Ligne 2 : chauffe progressivement (surchauffe)
    3: {"base": 27.0, "amp": 1.5},              # Ligne 3 : à l'arrêt, température ambiante
}


def reading(machine: int, when: datetime, running: bool = True) -> tuple[float, float]:
    p = PROFILES.get(machine, {"base": 40.0, "amp": 3.0})
    hour = when.hour + when.minute / 60
    base = p["base"] if running else 27.0
    cycle = p["amp"] * math.sin((hour - 8) / 24 * 2 * math.pi)  # plus chaud l'après-midi
    drift = 0.0
    if p.get("drift") and running:
        # monte sur les 3 dernières heures jusqu'à ~ 61 °C
        hours_left = (datetime.now() - when).total_seconds() / 3600
        drift = max(0.0, 3 - hours_left) * 3.0
    t = base + cycle + drift + random.gauss(0, 0.4)
    h = 42 - (t - 30) * 0.4 + random.gauss(0, 1.2)
    return round(min(t, 79.5), 1), round(max(15.0, min(h, 90.0)), 1)


def call(url: str, key: str, machine: int, **params) -> str:
    query = urllib.parse.urlencode({"key": key, "machine": machine, **params})
    with urllib.request.urlopen(f"{url}?{query}", timeout=10) as res:
        return res.read().decode().strip()


def live(args):
    print(f"Simulation de {len(args.machines)} carte(s), envoi toutes les {args.period} s (Ctrl+C pour arrêter)")
    states = {m: "on" for m in args.machines}
    while True:
        for m in args.machines:
            t, h = reading(m, datetime.now(), states[m] == "on")
            try:
                states[m] = call(args.url, args.key, m, temperature=t, humidity=h)
                print(f"{datetime.now():%H:%M:%S}  machine {m}  {t:5.1f} °C  {h:4.1f} %  → état demandé : {states[m]}")
            except Exception as exc:  # noqa: BLE001 - simulateur : on affiche et on continue
                print(f"machine {m} : erreur {exc}", file=sys.stderr)
        time.sleep(args.period)


def intrusion(args):
    print(call(args.url, args.key, args.machine, event="intrusion"))


def backfill(args):
    """Écrit du SQL : historique des dernières heures (une mesure toutes les 2 min)."""
    now = datetime.now()
    print("USE siame;")
    rows = []
    for m, running in ((1, True), (2, True), (3, False)):
        t = now - timedelta(hours=args.hours)
        while t <= now:
            temp, hum = reading(m, t, running)
            rows.append(f"({m},{temp},{hum},'{t:%Y-%m-%d %H:%M:%S}')")
            t += timedelta(minutes=2)
    for i in range(0, len(rows), 500):
        print("INSERT INTO measurements (machine_id, temperature, humidity, created_at) VALUES " + ",".join(rows[i:i + 500]) + ";")


def main():
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8")  # console Windows
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    sub = parser.add_subparsers(dest="cmd", required=True)
    for name in ("live", "intrusion"):
        p = sub.add_parser(name)
        p.add_argument("--url", default="http://localhost:8090/api/device.php")
        p.add_argument("--key", default="change-me")
        if name == "live":
            p.add_argument("--machines", type=int, nargs="+", default=[1, 2, 3])
            p.add_argument("--period", type=int, default=30)
        else:
            p.add_argument("--machine", type=int, default=1)
    b = sub.add_parser("backfill")
    b.add_argument("--hours", type=int, default=24)
    args = parser.parse_args()
    {"live": live, "intrusion": intrusion, "backfill": backfill}[args.cmd](args)


if __name__ == "__main__":
    main()
