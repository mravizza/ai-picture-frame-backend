<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="page-header">
    <h1>Fotos</h1>
    <a href="<?= base_url('/admin/photos/upload') ?>" class="btn btn-primary">+ Fotos hochladen</a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-success"><?= e($flash) ?></div>
<?php endif; ?>

<div class="card">
    <form method="GET" action="<?= base_url('/admin/photos') ?>" class="filter-bar">
        <div class="form-group">
            <label>Person</label>
            <select name="person_id" onchange="this.form.submit()">
                <option value="">Alle</option>
                <?php foreach ($persons as $p): ?>
                    <option value="<?= (int) $p['id'] ?>" <?= ($filterPersonId ?? null) == $p['id'] ? 'selected' : '' ?>>
                        <?= e($p['vorname']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<?php if (empty($photos)): ?>
    <div class="card"><p class="text-muted">Keine Fotos vorhanden.</p></div>
<?php else: ?>
    <div class="photo-grid">
        <?php foreach ($photos as $photo): ?>
            <div class="photo-card">
                <img src="<?= base_url('/uploads/' . e($photo['filename'])) ?>" alt="<?= e($photo['original_filename']) ?>" loading="lazy">
                <div class="photo-info">
                    <div><?= e($photo['original_filename']) ?></div>
                    <div><?= format_datetime($photo['created_at']) ?></div>
                    <?php if (!empty($photo['taken_at'])): ?>
                        <div class="text-muted" style="font-size:0.85em;">Aufnahme: <?= format_datetime($photo['taken_at']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($photo['description'])): ?>
                        <div class="text-muted" style="font-size:0.85em;margin-top:0.25rem;"><?= e($photo['description']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($photo['latitude']) && !empty($photo['longitude'])): ?>
                        <?php
                            $lat = (float) $photo['latitude'];
                            $lng = (float) $photo['longitude'];
                        ?>
                        <div style="margin-top:0.35rem;">
                            <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=<?= $lng - 0.008 ?>,<?= $lat - 0.005 ?>,<?= $lng + 0.008 ?>,<?= $lat + 0.005 ?>&amp;layer=mapnik&amp;marker=<?= $lat ?>,<?= $lng ?>"
                                    style="width:100%;height:120px;border:0;border-radius:4px;" loading="lazy"></iframe>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($photo['persons'])): ?>
                        <div>
                            <?php foreach ($photo['persons'] as $pp): ?>
                                <span class="badge badge-active"><?= e($pp['vorname']) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="mt-1">
                        <a href="<?= base_url('/admin/photos/assign?id=' . (int) $photo['id']) ?>" class="btn btn-sm btn-success">Zuordnen</a>
                        <form method="POST" action="<?= base_url('/admin/photos/delete') ?>" style="display:inline" onsubmit="return confirm('Foto wirklich loeschen?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $photo['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Loeschen</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
