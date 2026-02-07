# CLAUDE CODE PROMPT – Backend MVP (PHP + MySQL)  
## Projekt: Digitaler Bilderrahmen – Backend + Admin-Portal + API (ohne Face-Daten)

Du bist **Claude Code** und agierst als **Senior Full-Stack Engineer (PHP/MySQL)** mit Fokus auf robuste, pragmatische MVPs.  
Baue ein produktionsnahes Backend fuer den **Digitalen Erinnerungsrahmen**.

WICHTIG:
- Keine Kamera-Frames, keine Audio-Daten, keine Face-Embeddings in der Cloud.
- Das Backend verwaltet nur: **Personen, Fotos, Konfiguration, Logs, Konversationstexte (optional)**.

---

## 0) Deployment / Umgebung (fix)
- Ziel-Domain: `https://senioren.startline.ch/`
- Zugriff: FTP
  - Host: `sl301.web.hostpoint.ch`
  - User: `senioren@startline.ch`
- Stack: **PHP + MySQL**
- Hosting: Hostpoint (Shared Hosting, keine Container annehmen)
- App-Struktur so bauen, dass sie via FTP deploybar ist (keine Spezial-Infra voraussetzen).

WICHTIG:
- Keine echten Credentials in Code committen.
- Konfiguration via `.env` oder `config.php` ausserhalb Webroot (wenn moeglich) oder per `config.local.php`, das nicht ins Repo gehoert.

---

## 1) Ziele (MVP)
1. Admin-Portal (Web) fuer:
   - Personen verwalten
   - Fotos verwalten und Personen zuordnen
   - Device-Konfiguration verwalten
   - Logs einsehen
2. API fuer Android-Client:
   - /config
   - /persons
   - /photos
   - /logs/event
   - optional /conversation/start und /conversation/event (Text only)
3. Foto-Sync (MVP) ueber:
   - Upload im Admin-Portal (Dateiupload)
   - Optional: Import aus einem Server-Ordner (falls spaeter via Dropbox/FTP Sync)
4. Sicherheit:
   - Admin Login
   - API Auth via Device Token
   - Rate limiting light (optional)
5. Einfaches, stabiles Datenmodell (MySQL)

---

## 2) Funktionaler Umfang (MVP)

### 2.1 Admin Portal (Server-side rendered, simpel)
Seiten:
- Login/Logout
- Dashboard (Uebersicht: aktive Devices, letzte Logs, Fotoanzahl)
- Personen
  - Liste
  - Create/Edit
  - Aktiv/Inaktiv
  - Felder:
    - id (uuid oder int)
    - vorname
    - rolle_beziehung (z.B. Tochter, Enkel)
    - bevorzugte_anrede
    - greeting_text (z.B. "Hoi Anna. Schoen bisch du da.")
    - themen (Textfeld)
    - gespraechslaenge (kurz/mittel/lang)
    - aktiv (bool)
- Fotos
  - Upload (multiple)
  - Liste mit Filter (Person, Datum)
  - Zuordnung Person <-> Foto
  - Metadaten:
    - checksum
    - mime
    - original_filename
    - created_at
- Devices / Konfiguration
  - deviceId
  - slideshowIntervalSec
  - faceStableSec
  - cooldownSec
  - similarityThreshold
  - ttsEnabled
  - updated_at
- Logs
  - Liste (Filter: deviceId, personId, type, Zeitraum)
  - Detailansicht

UI:
- Minimal, schnell, funktional
- Kein JS-Framework noetig, optional kleine Vanilla JS fuer UX

### 2.2 API (JSON)
Implementiere REST-like Endpoints:

#### GET /api/v1/config?deviceId=...
Response:
```json
{
  "deviceId": "tablet-001",
  "slideshowIntervalSec": 10,
  "faceStableSec": 4,
  "cooldownSec": 300,
  "similarityThreshold": 0.65,
  "ttsEnabled": true,
  "updatedAt": "ISO8601"
}
