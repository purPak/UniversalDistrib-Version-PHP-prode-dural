<?php
session_start();
require_once("../_BDDconnect.php");
$a=4.1;
// Vérification habilitation
$user_id=liste($_SESSION["user_id"]);
$groupe_id=liste($_SESSION["groupe_id"]);
$login=liste($_SESSION["email"]);
if($user_id&&$groupe_id==="1") :
    $supp_id=verif("supp_id");
    $supp_id_bdd=bdd("supp_id");
    $nom = verif("nom");
    $supprimer = verif("supprimer");
    // Confirmation de la suppression
    $choixproduit = verif("choixproduit");
    // Requête de suppression
    if ($supprimer === "valider" && $choixproduit === "oui") {
        mysqli_query($connect, "DELETE FROM produits WHERE produit_id='$supp_id_bdd'");
        header("location:produits.php");
    }
    // les requêtes SQL
    $count=mysqli_query($connect,"SELECT COUNT(*) AS nb_enr FROM produits;");
    $produits=mysqli_query($connect,"SELECT produit_id,pu,devise,produits.actif,produits.nom,categories.nom AS categorie_nom,souscategories.nom AS souscategorie_nom FROM souscategories INNER JOIN categories ON souscategories.categorie_id = categories.categorie_id INNER JOIN produits ON souscategories.souscategorie_id = produits.souscategorie_id");
    // Compte le nbre d'enregistrement(s)
    $unique=mysqli_fetch_array($count);
    if ($unique["nb_enr"]<=1) {$texte=NULL;} else {$texte="s";}
    // Gestion "actif"
    $coche=verif("coche");
    $actualise=verif("actualise");
    if($actualise==1) {header("Location:produits.php?coche=$coche");}
?>
<!DOCTYPE HTML>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Gestion des produits - BackOffice - Universal Distrib</title>
        <?php include("_metasecurise.php"); ?>
    </head>
    <body class="backoffice-body">
        <div class="container">
            <?php include("_ban.php"); ?>

            <header class="row">
                <div class="col-lg-12">
                    <h1 class="text-center">BackOffice</h1>
                </div>
            </header>

            <hr>

            <section class="row">
                <div class="col-sm-8">
                    <h2 class="text-center">Produits</h2>
                    <div class="col-sm-12">
                        <?php if($supprimer=="valider") : ?>
                            <div class="modal" style="display: block; padding-top: 10%; background-color: rgba(0,0,0,0.5);">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><a href="produits.php">&times;</a></button>
                                            <h3 class="modal-title">Suppression d'un produit</h3>
                                        </div>
                                        <div class="modal-body">
                                            <blockquote class="alert-danger text-center">Attention, en confirmant, vous effacerez définitivement le produit: <strong><?php echo $nom; ?></strong></blockquote>
                                            <p class="text-center">
                                                <a href="produits.php?supp_id=<?php echo $supp_id; ?>&amp;supprimer=valider&amp;choixproduit=oui" class="btn btn-danger">Oui, je supprime le produit</a>&nbsp;
                                                <a href="produits.php" class="btn btn-primary">Non, je conserve le produit</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <table class="table table-bordered table-striped table-condensed text-center">
                            <thead>
                                <tr class="info">
                                    <th colspan="8">
                                        Interface de gestion (<?php echo $unique["nb_enr"]."&nbsp;produit".$texte; ?>)
                                        <span style="float:right;">
                                            <a data-toggle="tooltip" href="categories.php" class="btn btn-xs btn-success" title="Gérer une catégorie">
                                                <span class="glyphicon glyphicon-book"></span>
                                            </a>&nbsp;
                                            <a data-toggle="tooltip" href="produits_ajout.php" class="btn btn-xs btn-primary" title="Ajouter un produit">
                                                <span class="glyphicon glyphicon-plus-sign"></span>
                                            </a>
                                        </span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prix Unitaire</th>
                                    <th>Devise</th>
                                    <th>Categorie</th>
                                    <th>Sous-Categorie</th>
                                    <th>Modifier</th>
                                    <th>Supprimer</th>
                                    <?php if ($coche==1) {mysqli_query($connect,"UPDATE produits SET actif='0' WHERE produit_id<>1");?>
                                        <th><a href="produits.php?coche=2&amp;actualise=1">Tous</a></th>
                                    <?php } elseif ($coche==2) {mysqli_query($connect,"UPDATE produits SET actif='1'");?>
                                        <th><a href="produits.php?coche=1&amp;actualise=1">Aucun</a></th>
                                    <?php } elseif ($coche<1 || $coche>2) { ?>
                                        <th><a href="produits.php?coche=1&amp;actualise=1">Actif</a></th>
                                    <?php } ?>
                                </tr>
                            </thead>

                            <tbody>
                            <?php while ($row=mysqli_fetch_array($produits)) {if($row["actif"]==1) {$etat_actif=1;} else {$etat_actif=-1;}?>
                                <tr<?php if($etat_actif===-1) {echo' class="danger"';} ?>>
                                    <td><?php echo $row["nom"]; ?></td>

                                    <td><?php echo $row["pu"]; ?></td>

                                    <td><?php echo $row["devise"]; ?></td>

                                    <td><?php echo $row["categorie_nom"]; ?></td>

                                    <td><?php echo $row["souscategorie_nom"]; ?></td>

                                    <td>
                                        <a href="produits_modif.php?id_aff=<?php echo $row["produit_id"]; ?>" class="btn btn-primary" title="Modifier le produit"><span class="glyphicon glyphicon-pencil"></span></a>
                                    </td>

                                    <td>
                                        <a href="produits.php?supp_id=<?php echo $row["produit_id"]; ?>&amp;nom=<?php echo $row["nom"]; ?>&amp;supprimer=valider" class="btn btn-danger" title="Supprimer le produit"><span class="glyphicon glyphicon-trash"></span></a>
                                    </td>

                                    <td>
                                        <a href="modifactif.php?id_etat=<?php echo $row['produit_id']; ?>&amp;produit_actif=<?php echo $etat_actif; ?>">
                                            <img src="images/case<?php echo $row["actif"]; ?>.gif" <?php if ($row["actif"]==1) {echo 'alt="activée" title="activée"';} else {echo 'alt="désactivée" title="désactivée"';} ?> width="17" height="17" />
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php include("_session.php"); ?>
            </section>
        </div><!-- container -->
        <br>
        <?php
        include("_scripts.php");
        mysqli_close($connect);
        ?>
    </body>
</html>
<?php
//début de la permission
else :
    header ("location:login.php");
endif;
?>
