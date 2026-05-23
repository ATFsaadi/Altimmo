<div class="container py-5">
    <div class="page-panel p-4 mb-4">
        <h1 class="section-title h2">Administration</h1>
        <p class="text-muted mb-0">Vue d'ensemble des utilisateurs, biens et messages.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="admin-panel p-3">
                <div class="text-muted">Utilisateurs</div>
                <div class="display-6 fw-bold"><?= $stats['utilisateurs'] ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="admin-panel p-3">
                <div class="text-muted">Biens</div>
                <div class="display-6 fw-bold"><?= $stats['biens'] ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="admin-panel p-3">
                <div class="text-muted">Messages</div>
                <div class="display-6 fw-bold"><?= $stats['messages'] ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="admin-panel p-3">
                <div class="text-muted">Agents</div>
                <div class="display-6 fw-bold"><?= $stats['agents'] ?></div>
            </div>
        </div>
    </div>

    <div class="page-panel p-4">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link <?= $tabActive === 'utilisateurs' ? 'active' : '' ?>" href="<?= url('admin', ['tab' => 'utilisateurs']) ?>">Utilisateurs</a></li>
            <li class="nav-item"><a class="nav-link <?= $tabActive === 'biens' ? 'active' : '' ?>" href="<?= url('admin', ['tab' => 'biens']) ?>">Biens</a></li>
            <li class="nav-item"><a class="nav-link <?= $tabActive === 'messages' ? 'active' : '' ?>" href="<?= url('admin', ['tab' => 'messages']) ?>">Messages</a></li>
        </ul>

        <?php if ($tabActive === 'biens'): ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Bien</th>
                        <th>Prix</th>
                        <th>Agent</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($biens as $bien): ?>
                        <tr>
                            <td><img src="<?= e(imageBien($bien)) ?>" alt=""></td>
                            <td>
                                <strong><?= e($bien['ville']) ?></strong><br>
                                <span class="text-muted"><?= e($bien['type'] ?? 'Type non precise') ?> - <?= e($bien['cp']) ?></span>
                            </td>
                            <td><?= formatPrix($bien['prix']) ?></td>
                            <td><?= e(trim(($bien['agent_prenom'] ?? '') . ' ' . ($bien['agent_nom'] ?? ''))) ?: 'Non affecte' ?></td>
                            <td>
                                <span class="badge <?= !empty($bien['vendu']) ? 'text-bg-secondary' : 'text-bg-success' ?>">
                                    <?= !empty($bien['vendu']) ? 'Vendu' : 'Disponible' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="<?= url('bien', ['id' => $bien['id_b']]) ?>">Voir</a>
                                <a class="btn btn-sm btn-outline-danger" data-confirm="Supprimer ce bien ?" href="<?= url('admin', ['tab' => 'biens', 'action' => 'supprimer_bien', 'id' => $bien['id_b']]) ?>">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($tabActive === 'messages'): ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Expediteur</th>
                        <th>Destinataire</th>
                        <th>Message</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($message['date_env'])) ?></td>
                            <td><?= e(trim(($message['exp_prenom'] ?? '') . ' ' . ($message['exp_nom'] ?? 'Contact'))) ?></td>
                            <td><?= e(trim(($message['dest_prenom'] ?? '') . ' ' . ($message['dest_nom'] ?? ''))) ?></td>
                            <td><?= e(strlen($message['message']) > 120 ? substr($message['message'], 0, 120) . '...' : $message['message']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($utilisateurs as $utilisateur): ?>
                        <tr>
                            <td><?= e(trim($utilisateur['prenom'] . ' ' . $utilisateur['nom'])) ?></td>
                            <td><?= e($utilisateur['email']) ?></td>
                            <td>
                                <form method="post" class="d-flex gap-2">
                                    <input type="hidden" name="id_u" value="<?= $utilisateur['id_u'] ?>">
                                    <select name="role" class="form-select form-select-sm" <?= $utilisateur['id_u'] == utilisateurConnecte()['id_u'] ? 'disabled' : '' ?>>
                                        <option value="1" <?= (int) $utilisateur['role'] === 1 ? 'selected' : '' ?>>Client</option>
                                        <option value="2" <?= (int) $utilisateur['role'] === 2 ? 'selected' : '' ?>>Agent</option>
                                        <option value="3" <?= (int) $utilisateur['role'] === 3 ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary" type="submit" name="modifier_role" <?= $utilisateur['id_u'] == utilisateurConnecte()['id_u'] ? 'disabled' : '' ?>>OK</button>
                                </form>
                            </td>
                            <td class="text-end">
                                <?php if ($utilisateur['id_u'] != utilisateurConnecte()['id_u']): ?>
                                    <a class="btn btn-sm btn-outline-danger" data-confirm="Supprimer cet utilisateur ?" href="<?= url('admin', ['tab' => 'utilisateurs', 'action' => 'supprimer_user', 'id' => $utilisateur['id_u']]) ?>">Supprimer</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
