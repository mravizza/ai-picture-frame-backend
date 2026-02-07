<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="page-header">
    <h1>Geraete</h1>
    <a href="<?= base_url('/admin/devices/create') ?>" class="btn btn-primary">+ Neues Geraet</a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-success"><?= e($flash) ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Device-ID</th>
                    <th>Name</th>
                    <th>Slideshow (s)</th>
                    <th>TTS</th>
                    <th>Status</th>
                    <th>Zuletzt gesehen</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($devices)): ?>
                    <tr><td colspan="7" class="text-muted">Keine Geraete vorhanden.</td></tr>
                <?php else: ?>
                    <?php foreach ($devices as $d): ?>
                        <tr>
                            <td><strong><?= e($d['device_id']) ?></strong></td>
                            <td><?= e($d['name']) ?></td>
                            <td><?= (int) $d['slideshow_interval_sec'] ?></td>
                            <td><?= $d['tts_enabled'] ? 'Ja' : 'Nein' ?></td>
                            <td>
                                <?php if ($d['aktiv']): ?>
                                    <span class="badge badge-active">Aktiv</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inaktiv</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-sm text-muted"><?= format_datetime($d['last_seen_at']) ?></td>
                            <td>
                                <a href="<?= base_url('/admin/devices/edit?id=' . (int) $d['id']) ?>" class="btn btn-sm btn-primary">Bearbeiten</a>
                                <form method="POST" action="<?= base_url('/admin/devices/delete') ?>" style="display:inline" onsubmit="return confirm('Geraet wirklich loeschen?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $d['id'] ?>">
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
