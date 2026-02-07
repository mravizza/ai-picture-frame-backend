<div class="page-header">
    <h1>Foto zuordnen</h1>
</div>

<div class="card">
    <div style="display:flex;gap:1.5rem;flex-wrap:wrap;">
        <div>
            <img src="<?= base_url('/uploads/' . e($photo['filename'])) ?>"
                 alt="<?= e($photo['original_filename']) ?>"
                 style="max-width:300px;border-radius:8px;">
        </div>
        <div style="flex:1;min-width:250px;">
            <p><strong>Dateiname:</strong> <?= e($photo['original_filename']) ?></p>
            <p><strong>Typ:</strong> <?= e($photo['mime']) ?></p>
            <p><strong>Hochgeladen:</strong> <?= format_datetime($photo['created_at']) ?></p>

            <form method="POST" action="<?= base_url('/admin/photos/assign') ?>" class="mt-2">
                <?= csrf_field() ?>
                <input type="hidden" name="photo_id" value="<?= (int) $photo['id'] ?>">

                <div class="form-group">
                    <label>Personen zuordnen</label>
                    <div class="checkbox-list">
                        <?php foreach ($persons as $p): ?>
                            <label>
                                <input type="checkbox" name="person_ids[]" value="<?= (int) $p['id'] ?>"
                                       <?= in_array($p['id'], $assignedIds) ? 'checked' : '' ?>>
                                <?= e($p['vorname']) ?>
                                <?php if ($p['rolle_beziehung']): ?>
                                    <span class="text-muted">(<?= e($p['rolle_beziehung']) ?>)</span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Speichern</button>
                    <a href="<?= base_url('/admin/photos') ?>" class="btn btn-outline" style="color:#1a1a1a;border-color:#d1d5db;">Zurueck</a>
                </div>
            </form>
        </div>
    </div>
</div>
