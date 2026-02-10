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
            <?php if (!empty($photo['taken_at'])): ?>
                <p><strong>Aufnahme:</strong> <?= format_datetime($photo['taken_at']) ?></p>
            <?php endif; ?>
            <?php if (!empty($photo['description'])): ?>
                <p><strong>Beschreibung:</strong> <?= e($photo['description']) ?></p>
            <?php endif; ?>
            <?php if (!empty($photo['latitude']) && !empty($photo['longitude'])): ?>
                <?php
                    $lat = (float) $photo['latitude'];
                    $lng = (float) $photo['longitude'];
                ?>
                <div style="margin:0.75rem 0;">
                    <strong style="font-size:0.875rem;">Standort:</strong>
                    <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=<?= $lng - 0.005 ?>,<?= $lat - 0.003 ?>,<?= $lng + 0.005 ?>,<?= $lat + 0.003 ?>&amp;layer=mapnik&amp;marker=<?= $lat ?>,<?= $lng ?>"
                            style="width:100%;height:200px;border:0;border-radius:6px;margin-top:0.5rem;"></iframe>
                    <a href="https://www.openstreetmap.org/?mlat=<?= $lat ?>&amp;mlon=<?= $lng ?>#map=16/<?= $lat ?>/<?= $lng ?>"
                       target="_blank" rel="noopener" style="font-size:0.8rem;">Groessere Karte anzeigen</a>
                </div>
            <?php endif; ?>

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
