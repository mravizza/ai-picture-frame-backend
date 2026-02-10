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
```

---

## 3) User Stories & Akzeptanzkriterien

### US-01: Admin Login / Logout

**Als** Administrator
**moechte ich** mich mit Benutzername und Passwort anmelden,
**damit** nur berechtigte Personen Zugriff auf das Admin-Portal haben.

Akzeptanzkriterien:
- [ ] Login-Seite unter `/admin/login` erreichbar
- [ ] Anmeldung mit gueltigem Benutzername + Passwort (bcrypt) leitet auf Dashboard weiter
- [ ] Falsche Credentials zeigen eine Fehlermeldung auf der Login-Seite
- [ ] Session wird bei Login regeneriert (Session-Fixation-Schutz)
- [ ] Logout unter `/admin/logout` beendet die Session und leitet auf Login weiter
- [ ] Alle Admin-Seiten leiten auf Login um, wenn keine aktive Session vorhanden ist

---

### US-02: Dashboard

**Als** Administrator
**moechte ich** auf einen Blick die wichtigsten Kennzahlen sehen,
**damit** ich den Systemzustand schnell erfassen kann.

Akzeptanzkriterien:
- [ ] Dashboard unter `/admin/dashboard` zeigt 4 Statistik-Karten: aktive Personen (+ Total), Fotos, aktive Devices, Logs (letzte 24h)
- [ ] Die 10 neusten Log-Eintraege werden in einer Tabelle angezeigt (Zeitstempel, Device, Typ, Person, Nachricht)
- [ ] Root-URL `/` leitet auf `/admin/dashboard` weiter

---

### US-03: Personen verwalten (CRUD)

**Als** Administrator
**moechte ich** Personen erstellen, bearbeiten, auflisten und loeschen koennen,
**damit** der Bilderrahmen weiss, welche Personen er erkennen und begruessen soll.

Akzeptanzkriterien:
- [ ] Personenliste unter `/admin/persons` zeigt: ID, Vorname, Rolle/Beziehung, Gespraechslaenge, Sprache, Foto-Anzahl, Status (Aktiv/Inaktiv), Aktionen
- [ ] Button "+ Neue Person" oeffnet Erstellformular
- [ ] Formular enthaelt: Vorname (Pflicht), Rolle/Beziehung, Bevorzugte Anrede, Gespraechslaenge (Select: Kurz/Mittel/Lang, Default Mittel), Sprache (Select: Schweizer Hochdeutsch/Schweizerdeutsch Dialekt/Englisch, Default Hochdeutsch), Begruessung (Textarea), Interessen (Textarea), Aktiv (Checkbox)
- [ ] Speichern ohne Vorname zeigt Fehlermeldung und Formular bleibt mit Eingaben erhalten
- [ ] Erfolgreiche Erstellung zeigt Flash-Nachricht "Person erstellt." und leitet auf Liste
- [ ] Bearbeiten laedt bestehende Werte ins Formular
- [ ] Erfolgreiche Aktualisierung zeigt Flash-Nachricht "Person aktualisiert."
- [ ] Loeschen zeigt Bestaetigungsdialog; bei Bestaetigung wird Person geloescht (Kaskade auf Foto-Zuordnungen)
- [ ] ENUM-Felder (`gespraechslaenge`, `sprache`) werden serverseitig auf erlaubte Werte validiert; ungueltige Werte werden auf Default zurueckgesetzt

---

### US-04: Fotos hochladen

**Als** Administrator
**moechte ich** ein oder mehrere Fotos hochladen und optional direkt Personen zuordnen,
**damit** der Bilderrahmen Bilder fuer die Slideshow hat.

Akzeptanzkriterien:
- [ ] Upload-Formular unter `/admin/photos/upload` erlaubt Mehrfachauswahl von Dateien
- [ ] Checkboxen fuer aktive Personen ermoeglichen direkte Zuordnung beim Upload
- [ ] Erlaubte MIME-Typen: `image/jpeg`, `image/png`, `image/webp`
- [ ] Maximale Dateigroesse: 10 MB pro Datei
- [ ] Duplikate werden anhand SHA256-Checksum erkannt und uebersprungen
- [ ] Dateien werden mit UUID-Dateiname im `/uploads/`-Verzeichnis gespeichert
- [ ] Flash-Nachricht zeigt Anzahl erfolgreich hochgeladener Fotos und etwaige Fehler
- [ ] Wenn `openai_api_key` konfiguriert ist, wird automatisch eine Bildbeschreibung via OpenAI Vision (gpt-4o) generiert und gespeichert
- [ ] Wenn Standortdaten (GPS) und/oder das Aufnahmedatum im Bild via EXIF vorhanden sind (nur JPEG), werden `latitude`, `longitude` und `taken_at` extrahiert und in der DB gespeichert
- [ ] Die extrahierten EXIF-Daten (`latitude`, `longitude`, `takenAt`) werden im API-Response unter `/api/v1/photos` zur Verfuegung gestellt
- [ ] Bei Bildern ohne EXIF-Daten (oder PNG/WebP) bleiben die Felder `null`

---

### US-05: Fotos verwalten & zuordnen

**Als** Administrator
**moechte ich** Fotos einsehen, nach Person filtern, Personen zuordnen und Fotos loeschen,
**damit** ich die Foto-Sammlung pflegen kann.

Akzeptanzkriterien:
- [ ] Foto-Uebersicht unter `/admin/photos` zeigt Fotos als Karten mit Thumbnail, Dateiname, Datum, Beschreibung und zugeordneten Personen
- [ ] Dropdown-Filter erlaubt Filterung nach Person
- [ ] "Zuordnen"-Button oeffnet Seite mit Foto-Vorschau und Checkboxen aller aktiven Personen
- [ ] Zuordnung wird synchronisiert (bestehende Zuordnungen werden durch neue ersetzt)
- [ ] Loeschen entfernt Datei vom Filesystem und Datenbank-Eintrag (Kaskade auf `person_photo`)

---

### US-06: Devices verwalten

**Als** Administrator
**moechte ich** Bilderrahmen-Geraete registrieren und deren Konfiguration anpassen,
**damit** jedes Geraet individuell eingestellt werden kann.

Akzeptanzkriterien:
- [ ] Geraete-Liste unter `/admin/devices` zeigt: Device-ID, Name, Slideshow-Intervall, TTS-Status, Aktiv-Status, Letzte Aktivitaet
- [ ] Erstellformular erfordert eindeutige `device_id`
- [ ] Bei Erstellung wird automatisch ein 64-Zeichen API-Token generiert und als Flash-Nachricht angezeigt
- [ ] Konfigurierbare Felder: Name, Slideshow-Intervall (Sek.), Face-Stable (Sek.), Cooldown (Sek.), Similarity-Threshold (0–1), TTS aktiviert, Aktiv
- [ ] Numerische Felder werden mit Min/Max-Grenzen validiert
- [ ] API-Token ist nach Erstellung nicht mehr aenderbar (Read-only im Edit-Formular)
- [ ] Loeschen mit Bestaetigungsdialog

---

### US-07: Logs einsehen

**Als** Administrator
**moechte ich** Ereignis-Logs filtern und im Detail betrachten,
**damit** ich das Verhalten der Geraete nachvollziehen kann.

Akzeptanzkriterien:
- [ ] Log-Liste unter `/admin/logs` zeigt: ID, Zeitstempel, Device, Typ (Badge), Person, Nachricht (gekuerzt)
- [ ] Filter verfuegbar fuer: Device-ID (Dropdown), Person (Dropdown), Typ (Dropdown), Von-Datum, Bis-Datum
- [ ] Dropdowns werden automatisch aus vorhandenen Daten befuellt
- [ ] Maximal 50 Eintraege pro Seite
- [ ] Detail-Ansicht unter `/admin/logs/detail?id={id}` zeigt alle Felder inkl. formatiertem JSON-Payload

---

### US-08: API – Device-Konfiguration abrufen

**Als** Android-Client
**moechte ich** die Konfiguration meines Geraets per API abrufen,
**damit** ich die richtigen Einstellungen (Slideshow-Intervall, Schwellenwerte, TTS) verwenden kann.

Akzeptanzkriterien:
- [ ] `GET /api/v1/config` erfordert Bearer-Token im Authorization-Header
- [ ] Response enthaelt: `deviceId`, `slideshowIntervalSec`, `faceStableSec`, `cooldownSec`, `similarityThreshold`, `ttsEnabled`, `updatedAt`
- [ ] Response-Format ist JSON mit camelCase-Keys
- [ ] Bei ungueltigem Token wird HTTP 401 zurueckgegeben
- [ ] `last_seen_at` des Geraets wird bei jedem API-Aufruf aktualisiert

---

### US-09: API – Personen abrufen

**Als** Android-Client
**moechte ich** die Liste aller aktiven Personen abrufen,
**damit** ich Gesichtserkennung und Begruessungen durchfuehren kann.

Akzeptanzkriterien:
- [ ] `GET /api/v1/persons` erfordert Bearer-Token
- [ ] Nur aktive Personen (`aktiv = 1`) werden zurueckgegeben
- [ ] Response-Felder pro Person: `id`, `vorname`, `rolleBeziehung`, `bevorzugteAnrede`, `greetingText`, `interessen`, `gespraechslaenge`, `sprache`, `photoCount`
- [ ] Response-Format ist JSON mit camelCase-Keys

---

### US-10: API – Fotos abrufen

**Als** Android-Client
**moechte ich** die Fotos einer bestimmten Person abrufen (optional inkrementell),
**damit** ich die Slideshow und Gesichtserkennung lokal ausfuehren kann.

Akzeptanzkriterien:
- [ ] `GET /api/v1/photos?personId={id}` erfordert Bearer-Token
- [ ] Parameter `personId` ist erforderlich
- [ ] Optionaler Parameter `since` (Timestamp) fuer inkrementellen Sync
- [ ] Response-Felder pro Foto: `id`, `url` (vollstaendige URL), `checksum`, `mime`, `description`, `createdAt`
- [ ] Fotos werden nach Erstelldatum absteigend sortiert

---

### US-11: API – Event loggen

**Als** Android-Client
**moechte ich** Ereignisse (Gesichtserkennung, Begruessung, Fehler) an das Backend melden,
**damit** der Administrator das Verhalten der Geraete nachverfolgen kann.

Akzeptanzkriterien:
- [ ] `POST /api/v1/logs/event` erfordert Bearer-Token
- [ ] Request-Body (JSON): `type` (Pflicht), `deviceId`, `personId`, `message`, `payload` (optional)
- [ ] Erfolgreiche Response: `{ok: true, logId: <id>}` mit HTTP 201
- [ ] Fehlende `type` liefert Fehlermeldung

---

### US-12: CSRF-Schutz

**Als** System
**moechte ich** alle POST-Formulare mit CSRF-Token absichern,
**damit** Cross-Site-Request-Forgery-Angriffe verhindert werden.

Akzeptanzkriterien:
- [ ] Jedes Formular enthaelt ein Hidden-Field mit CSRF-Token via `csrf_field()`
- [ ] Alle POST-Routen validieren das Token serverseitig
- [ ] Bei ungueltigem oder fehlendem Token wird HTTP 403 zurueckgegeben

---

### US-13: API-Authentifizierung via Device-Token

**Als** System
**moechte ich** API-Zugriffe ueber Bearer-Token authentifizieren,
**damit** nur registrierte Geraete auf die API zugreifen koennen.

Akzeptanzkriterien:
- [ ] Alle `/api/v1/*`-Endpoints erfordern `Authorization: Bearer {token}` Header
- [ ] Token wird gegen `devices.api_token` geprueft
- [ ] Bei gueltigem Token wird `devices.last_seen_at` aktualisiert
- [ ] Bei ungueltigem oder fehlendem Token wird HTTP 401 mit JSON-Fehlermeldung zurueckgegeben

---

### US-14: OpenAI-Bildbeschreibung

**Als** Administrator
**moechte ich**, dass hochgeladene Fotos automatisch eine KI-generierte Beschreibung erhalten,
**damit** der Bilderrahmen kontextbezogene Informationen zu den Bildern hat.

Akzeptanzkriterien:
- [ ] Nach erfolgreichem Upload wird OpenAI Vision API (gpt-4o) aufgerufen, wenn `openai_api_key` konfiguriert ist
- [ ] Bild wird als Base64 gesendet; Prompt fordert 2–3 Saetze Beschreibung auf Englisch
- [ ] Beschreibung wird in `photos.description` gespeichert
- [ ] Bei fehlendem API-Key oder Fehler wird kein Abbruch ausgeloest (graceful fallback)
- [ ] Beschreibung erscheint in der Admin-Foto-Uebersicht und im API-Response

---

### US-15: Foto-Duplikaterkennung

**Als** Administrator
**moechte ich**, dass doppelte Fotos beim Upload erkannt und uebersprungen werden,
**damit** keine identischen Bilder mehrfach gespeichert werden.

Akzeptanzkriterien:
- [ ] SHA256-Checksum wird fuer jede hochgeladene Datei berechnet
- [ ] Existiert bereits ein Foto mit gleicher Checksum, wird das Duplikat uebersprungen
- [ ] Flash-Nachricht informiert ueber uebersprungene Duplikate
