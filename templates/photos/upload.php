<div class="page-header">
    <h1>Fotos hochladen</h1>
</div>

<div class="card">
    <form method="POST" action="<?= base_url('/admin/photos/upload') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="photos">Bilder auswaehlen (mehrere moeglich)</label>
            <input type="file" id="photos" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" required>
            <div class="text-sm text-muted mt-1">Erlaubt: JPEG, PNG, WebP. Max. <?= config('max_upload_mb') ?> MB pro Datei.</div>
        </div>

        <div class="form-group">
            <label>Personen zuordnen (optional)</label>
            <div class="checkbox-list">
                <?php foreach ($persons as $p): ?>
                    <label>
                        <input type="checkbox" name="person_ids[]" value="<?= (int) $p['id'] ?>">
                        <?= e($p['vorname']) ?>
                        <?php if ($p['rolle_beziehung']): ?>
                            <span class="text-muted">(<?= e($p['rolle_beziehung']) ?>)</span>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
                <?php if (empty($persons)): ?>
                    <span class="text-muted">Keine Personen vorhanden.</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Hochladen</button>
            <a href="<?= base_url('/admin/photos') ?>" class="btn btn-outline" style="color:#1a1a1a;border-color:#d1d5db;">Abbrechen</a>
        </div>
    </form>
</div>
