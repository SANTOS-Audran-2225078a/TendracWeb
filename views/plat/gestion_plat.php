<!DOCTYPE html>
<html>
<head>
    <title><?= isset($plat) ? 'Modifier un Plat' : 'Ajouter un Plat' ?></title>
    <meta name="description" content="Vous êtes ici sur la page qui vous permet de consulter les différents plats, vous pourrez aussi en rajouter, les modifier ou en supprimer.">
    <link rel="stylesheet" href="/_assets/styles/stylesheet_accueil.css">
    <style>
        /* Ingrédients affichés en petit */
        .ingredients-text {
            font-size: small;
            color: black;
        }

        /* Liste des plats */
        .plat {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<header>
    <a href="../views/accueil.php"><button>Accueil</button></a>
    <a href="/club"><button>Gérer les Clubs</button></a>
    <a href="/repas"><button>Gérer les Repas</button></a>
    <a href="/tenrac"><button>Les tenrac</button></a>
    <a href='/tenrac/deconnecter'>Se déconnecter</a>
</header>

<h1><?= isset($plat) ? 'Modifier un Plat' : 'Ajouter un Plat' ?></h1>

<form action="<?= isset($plat) ? '/plat/modifierPlat' : '/plat/ajouterPlat' ?>" method="POST" class="boxForm">
    <?php if (isset($plat)): ?>
        <input type="hidden" name="id" value="<?= $plat['id'] ?>">
    <?php endif; ?>
    <label>Nom du plat :</label>
    <input type="text" name="nom" value="<?= isset($plat['nom']) ? htmlspecialchars($plat['nom']) : '' ?>" required><br>
    <label>Club :</label>
    <select name="club_id" required>
        <?php foreach ($clubs as $club): ?>
            <option value="<?= $club['id'] ?>" <?= isset($plat) && $club['id'] == $plat['club_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($club['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    <label>Ingrédients :</label>
    <div id="ingredients-container">
        <?php if (isset($plat['ingredients'])): ?>
            <?php foreach ($plat['ingredients'] as $ingredient): ?>
                <div class="ingredient-row">
                    <select name="ingredient_ids[]">
                        <option value="">Sélectionnez un ingrédient</option>
                        <?php foreach ($ingredients as $ing): ?>
                            <option value="<?= $ing['id'] ?>" <?= $ing['id'] == $ingredient['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ing['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" onclick="supprimerIngredient(this)">Supprimer</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="ingredient-row">
                <select name="ingredient_ids[]" required>
                    <option value="">Sélectionnez un ingrédient</option>
                    <?php foreach ($ingredients as $ingredient): ?>
                        <option value="<?= $ingredient['id'] ?>"><?= htmlspecialchars($ingredient['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" onclick="supprimerIngredient(this)">Supprimer</button>
            </div>
        <?php endif; ?>
    </div>
    <button type="button" onclick="ajouterIngredient()">Ajouter un ingrédient</button><br>

    <label>Sauces :</label>
    <div id="sauces-container">
        <?php foreach ($sauces as $sauce): ?>
            <input type="checkbox" name="sauce_ids[]" value="<?= $sauce['id'] ?>"
                <?= isset($platSauces) && in_array($sauce['id'], array_column($platSauces, 'id')) ? 'checked' : '' ?>>
            <?= htmlspecialchars($sauce['nom']) ?><br>
        <?php endforeach; ?>
    </div><br>

    <button type="submit"><?= isset($plat) ? 'Modifier' : 'Ajouter' ?></button>
</form>

<!-- Liste des plats existants par club -->
<h2>Plats par Club</h2>
<div class="Liste">
<?php if (isset($plats) && is_array($plats)): ?>
    <?php foreach ($clubs as $club): ?>
        <div class="box">
        <h3><?= htmlspecialchars($club['nom']) ?></h3>
        <ul>
            <?php foreach ($plats as $plat): ?>
                <?php if ($plat['club_id'] == $club['id']): ?>
                    <li class="plat">
                        <?= htmlspecialchars($plat['nom']) ?>
                        <a href="/plat/editer/<?= $plat['id'] ?>">Modifier</a> | 
                        <a href="/plat/supprimer/<?= $plat['id'] ?>">Supprimer</a>
                        <div class="ingredients-text">
                            Ingrédients : 
                            <?php $platIngredients = $platModel->getIngredientsByPlat($plat['id']); ?>
                            <?= !empty($platIngredients) ? implode(', ', array_map(fn($ing) => htmlspecialchars($ing['nom']), $platIngredients)) : 'Aucun ingrédient' ?>
                        </div>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul> 
        <?php if (empty(array_filter($plats, fn($p) => $p['club_id'] == $club['id']))): ?>
            <p>Aucun plat pour ce club.</p>
        <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucun plat trouvé.</p>
<?php endif; ?>
</div>

<script>
    function ajouterIngredient() {
        var container = document.getElementById('ingredients-container');
        var newRow = document.createElement('div');
        newRow.classList.add('ingredient-row');
        newRow.innerHTML = `
            <select name="ingredient_ids[]" required>
                <option value="">Sélectionnez un ingrédient</option>
                <?php foreach ($ingredients as $ingredient): ?>
                    <option value="<?= htmlspecialchars($ingredient['id']) ?>"><?= htmlspecialchars($ingredient['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" onclick="supprimerIngredient(this)">Supprimer</button>
        `;
        container.appendChild(newRow);
    }

    function supprimerIngredient(button) {
        var row = button.parentElement;
        row.remove();
    }
</script>
</body>
</html>
