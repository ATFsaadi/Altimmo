<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="page-panel p-4">
                <h1 class="h3">Nouveau message</h1>

                <?php if (!empty($erreurs)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($erreurs as $erreur): ?>
                            <div><?= e($erreur) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label" for="destinataire">Destinataire</label>
                        <select class="form-select" id="destinataire" name="destinataire" required>
                            <option value="">Choisir</option>
                            <?php foreach ($destinataires as $destinataire): ?>
                                <option value="<?= $destinataire['id_u'] ?>">
                                    <?= e(trim($destinataire['prenom'] . ' ' . $destinataire['nom'])) ?> - <?= e(roleLibelle($destinataire['role'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="message">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Envoyer</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="page-panel p-4">
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#recus" type="button">Recus</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#envoyes" type="button">Envoyes</button></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="recus">
                        <?php foreach ($messagesRecus as $message): ?>
                            <div class="border rounded p-3 mb-3 <?= empty($message['lu']) ? 'bg-light' : '' ?>">
                                <div class="d-flex justify-content-between gap-2">
                                    <strong>De : <?= e(trim(($message['exp_prenom'] ?? '') . ' ' . ($message['exp_nom'] ?? 'Contact'))) ?></strong>
                                    <span class="text-muted small"><?= date('d/m/Y H:i', strtotime($message['date_env'])) ?></span>
                                </div>
                                <p class="mb-0 mt-2"><?= nl2br(e($message['message'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($messagesRecus)): ?>
                            <div class="alert alert-info">Aucun message recu.</div>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="envoyes">
                        <?php foreach ($messagesEnvoyes as $message): ?>
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between gap-2">
                                    <strong>A : <?= e(trim(($message['dest_prenom'] ?? '') . ' ' . ($message['dest_nom'] ?? 'Destinataire'))) ?></strong>
                                    <span class="text-muted small"><?= date('d/m/Y H:i', strtotime($message['date_env'])) ?></span>
                                </div>
                                <p class="mb-0 mt-2"><?= nl2br(e($message['message'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($messagesEnvoyes)): ?>
                            <div class="alert alert-info">Aucun message envoye.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

