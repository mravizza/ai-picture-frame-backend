<h1 class="mb-2">Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $activePersonCount ?></div>
        <div class="stat-label">Aktive Personen (<?= $personCount ?> total)</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $photoCount ?></div>
        <div class="stat-label">Fotos</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $activeDevices ?></div>
        <div class="stat-label">Aktive Geraete</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $recentLogs ?></div>
        <div class="stat-label">Logs (letzte 24h)</div>
    </div>
</div>

<div class="card">
    <h2>Letzte Logs</h2>
    <?php if (empty($latestLogs)): ?>
        <p class="text-muted">Keine Logs vorhanden.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Zeitpunkt</th>
                        <th>Device</th>
                        <th>Typ</th>
                        <th>Person</th>
                        <th>Nachricht</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestLogs as $l): ?>
                        <tr>
                            <td class="text-sm"><?= format_datetime($l['created_at']) ?></td>
                            <td><?= e($l['device_id']) ?></td>
                            <td><span class="badge badge-active"><?= e($l['type']) ?></span></td>
                            <td><?= e($l['person_vorname'] ?? '-') ?></td>
                            <td class="text-sm"><?= e(mb_strimwidth($l['message'] ?? '', 0, 60, '...')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-1">
            <a href="<?= base_url('/admin/logs') ?>">Alle Logs anzeigen</a>
        </div>
    <?php endif; ?>
</div>
