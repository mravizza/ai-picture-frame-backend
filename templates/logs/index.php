<div class="page-header">
    <h1>Logs</h1>
</div>

<div class="card">
    <form method="GET" action="<?= base_url('/admin/logs') ?>" class="filter-bar">
        <div class="form-group">
            <label>Device</label>
            <select name="device_id">
                <option value="">Alle</option>
                <?php foreach ($logDeviceIds as $did): ?>
                    <option value="<?= e($did) ?>" <?= ($filters['device_id'] ?? '') === $did ? 'selected' : '' ?>>
                        <?= e($did) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Person</label>
            <select name="person_id">
                <option value="">Alle</option>
                <?php foreach ($persons as $p): ?>
                    <option value="<?= (int) $p['id'] ?>" <?= ($filters['person_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                        <?= e($p['vorname']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Typ</label>
            <select name="type">
                <option value="">Alle</option>
                <?php foreach ($logTypes as $t): ?>
                    <option value="<?= e($t) ?>" <?= ($filters['type'] ?? '') === $t ? 'selected' : '' ?>>
                        <?= e($t) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Von</label>
            <input type="date" name="from" value="<?= e($filters['from'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Bis</label>
            <input type="date" name="to" value="<?= e($filters['to'] ?? '') ?>">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-sm">Filtern</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Zeitpunkt</th>
                    <th>Device</th>
                    <th>Typ</th>
                    <th>Person</th>
                    <th>Nachricht</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="7" class="text-muted">Keine Logs gefunden.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $l): ?>
                        <tr>
                            <td><?= (int) $l['id'] ?></td>
                            <td class="text-sm"><?= format_datetime($l['created_at']) ?></td>
                            <td><?= e($l['device_id']) ?></td>
                            <td><span class="badge badge-active"><?= e($l['type']) ?></span></td>
                            <td><?= e($l['person_vorname'] ?? '-') ?></td>
                            <td class="text-sm"><?= e(mb_strimwidth($l['message'] ?? '', 0, 80, '...')) ?></td>
                            <td>
                                <a href="<?= base_url('/admin/logs/detail?id=' . (int) $l['id']) ?>" class="btn btn-sm btn-primary">Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
