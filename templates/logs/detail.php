<div class="page-header">
    <h1>Log #<?= (int) $log['id'] ?></h1>
    <a href="<?= base_url('/admin/logs') ?>" class="btn btn-outline" style="color:#1a1a1a;border-color:#d1d5db;">Zurueck</a>
</div>

<div class="card">
    <table>
        <tr>
            <th style="width:150px;">ID</th>
            <td><?= (int) $log['id'] ?></td>
        </tr>
        <tr>
            <th>Zeitpunkt</th>
            <td><?= format_datetime($log['created_at']) ?></td>
        </tr>
        <tr>
            <th>Device</th>
            <td><?= e($log['device_id']) ?></td>
        </tr>
        <tr>
            <th>Typ</th>
            <td><span class="badge badge-active"><?= e($log['type']) ?></span></td>
        </tr>
        <tr>
            <th>Person</th>
            <td>
                <?php if ($log['person_id']): ?>
                    <?= e($log['person_vorname'] ?? 'ID: ' . $log['person_id']) ?>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Nachricht</th>
            <td><?= e($log['message'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Payload</th>
            <td>
                <?php if ($log['payload']): ?>
                    <pre style="background:#f8fafc;padding:0.75rem;border-radius:6px;font-size:0.8125rem;overflow-x:auto;"><?= e(json_encode(json_decode($log['payload']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>
