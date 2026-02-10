<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="page-header">
    <h1>Personen</h1>
    <a href="<?= base_url('/admin/persons/create') ?>" class="btn btn-primary">+ Neue Person</a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-success"><?= e($flash) ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vorname</th>
                    <th>Rolle/Beziehung</th>
                    <th>Gespraechslaenge</th>
                    <th>Sprache</th>
                    <th>Fotos</th>
                    <th>Status</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($persons)): ?>
                    <tr><td colspan="8" class="text-muted">Keine Personen vorhanden.</td></tr>
                <?php else: ?>
                    <?php foreach ($persons as $p): ?>
                        <tr>
                            <td><?= (int) $p['id'] ?></td>
                            <td><strong><?= e($p['vorname']) ?></strong></td>
                            <td><?= e($p['rolle_beziehung']) ?></td>
                            <td><?= e($p['gespraechslaenge']) ?></td>
                            <td><?= e($p['sprache'] ?? 'hochdeutsch') ?></td>
                            <td><?= (int) $p['photo_count'] ?></td>
                            <td>
                                <?php if ($p['aktiv']): ?>
                                    <span class="badge badge-active">Aktiv</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inaktiv</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('/admin/persons/edit?id=' . (int) $p['id']) ?>" class="btn btn-sm btn-primary">Bearbeiten</a>
                                <form method="POST" action="<?= base_url('/admin/persons/delete') ?>" style="display:inline" onsubmit="return confirm('Person wirklich loeschen?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Loeschen</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
