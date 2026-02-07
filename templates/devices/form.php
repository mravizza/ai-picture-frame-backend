<div class="page-header">
    <h1><?= $isEdit ? 'Geraet bearbeiten' : 'Neues Geraet' ?></h1>
</div>

<div class="card">
    <form method="POST" action="<?= base_url($isEdit ? '/admin/devices/update' : '/admin/devices/store') ?>">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int) ($device['id'] ?? 0) ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="device_id">Device-ID *</label>
                <input type="text" id="device_id" name="device_id" required
                       placeholder="z.B. tablet-001"
                       value="<?= e($device['device_id'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name"
                       placeholder="z.B. Wohnzimmer-Tablet"
                       value="<?= e($device['name'] ?? '') ?>">
            </div>
        </div>

        <?php if ($isEdit && !empty($device['api_token'])): ?>
            <div class="form-group">
                <label>API-Token</label>
                <input type="text" value="<?= e($device['api_token']) ?>" readonly
                       style="background:#f8fafc;font-family:monospace;font-size:0.8125rem;">
            </div>
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="slideshow_interval_sec">Slideshow Interval (Sek.)</label>
                <input type="number" id="slideshow_interval_sec" name="slideshow_interval_sec" min="1"
                       value="<?= (int) ($device['slideshow_interval_sec'] ?? 10) ?>">
            </div>
            <div class="form-group">
                <label for="face_stable_sec">Face Stable (Sek.)</label>
                <input type="number" id="face_stable_sec" name="face_stable_sec" min="1"
                       value="<?= (int) ($device['face_stable_sec'] ?? 4) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="cooldown_sec">Cooldown (Sek.)</label>
                <input type="number" id="cooldown_sec" name="cooldown_sec" min="0"
                       value="<?= (int) ($device['cooldown_sec'] ?? 300) ?>">
            </div>
            <div class="form-group">
                <label for="similarity_threshold">Similarity Threshold (0-1)</label>
                <input type="number" id="similarity_threshold" name="similarity_threshold"
                       step="0.01" min="0" max="1"
                       value="<?= number_format((float) ($device['similarity_threshold'] ?? 0.65), 3) ?>">
            </div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="tts_enabled" value="1"
                       <?= !empty($device['tts_enabled']) ? 'checked' : '' ?>>
                TTS aktiviert
            </label>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="aktiv" value="1"
                       <?= !empty($device['aktiv']) ? 'checked' : '' ?>>
                Aktiv
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Speichern' : 'Erstellen' ?></button>
            <a href="<?= base_url('/admin/devices') ?>" class="btn btn-outline" style="color:#1a1a1a;border-color:#d1d5db;">Abbrechen</a>
        </div>
    </form>
</div>
