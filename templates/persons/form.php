<div class="page-header">
    <h1><?= $isEdit ? 'Person bearbeiten' : 'Neue Person' ?></h1>
</div>

<div class="card">
    <form method="POST" action="<?= base_url($isEdit ? '/admin/persons/update' : '/admin/persons/store') ?>">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int) ($person['id'] ?? 0) ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="vorname">Vorname *</label>
                <input type="text" id="vorname" name="vorname" required
                       value="<?= e($person['vorname'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="rolle_beziehung">Rolle / Beziehung</label>
                <input type="text" id="rolle_beziehung" name="rolle_beziehung"
                       placeholder="z.B. Tochter, Enkel"
                       value="<?= e($person['rolle_beziehung'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="bevorzugte_anrede">Bevorzugte Anrede</label>
                <input type="text" id="bevorzugte_anrede" name="bevorzugte_anrede"
                       value="<?= e($person['bevorzugte_anrede'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="gespraechslaenge">Gespraechslaenge</label>
                <select id="gespraechslaenge" name="gespraechslaenge">
                    <?php foreach (['kurz' => 'Kurz', 'mittel' => 'Mittel', 'lang' => 'Lang'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= ($person['gespraechslaenge'] ?? 'mittel') === $val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="greeting_text">Begruessung</label>
            <textarea id="greeting_text" name="greeting_text"
                      placeholder="z.B. Hoi Anna. Schoen bisch du da."><?= e($person['greeting_text'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="themen">Themen</label>
            <textarea id="themen" name="themen"
                      placeholder="Gespraechsthemen (Freitext)"><?= e($person['themen'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="aktiv" value="1"
                       <?= !empty($person['aktiv']) ? 'checked' : '' ?>>
                Aktiv
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Speichern' : 'Erstellen' ?></button>
            <a href="<?= base_url('/admin/persons') ?>" class="btn btn-outline" style="color:#1a1a1a;border-color:#d1d5db;">Abbrechen</a>
        </div>
    </form>
</div>
