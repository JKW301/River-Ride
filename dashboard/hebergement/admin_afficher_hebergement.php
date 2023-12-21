  <!--  start dashboard_head.php   -->
  <?php
  include '../dashboard_head.php';
  require_once '../db.php';

  ?>  
  <!--  end dashboard_head.php   -->

<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL); 

    $sql = "SELECT * FROM hebergement";
    $stmt = $conn->query($sql);
    $hebergement = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Lister Hébergements</title>
        <style>
        .table img {
            max-width: 100px;
            max-height: 100px;
        }
        .table td {
            text-align: center;
        }
        .badge {
        padding: 6px 12px;
        }
        .badge-success {
            color: #155724; /* Dark green color */
            background-color: #d4edda; /* Light green background */
        }
        .badge-danger {
            color: #721c24; /* Dark red color */
            background-color: #f8d7da; /* Light red background */
        }
        </style>
    </head>


    <body class="g-sidenav-showbg- gray-100">
        
    <div class="min-height-300 bg-primary position-absolute w-100"></div>

        <!--  Alert suppression reussi   -->
        <!-- ... -->

        <?php
        if (isset($_GET['suppression_reussie']) && $_GET['suppression_reussie'] === 'true') {
            echo '<div class="alert alert-danger" role="alert" id="suppression-alert">
                    Hebergement supprimé avec succès !
                </div>';
        }
        ?>

        <main class="main-content position-relative border-radius-lg ">
            <!--  start dashboard_main.php   -->
            <?php include '../dashboard_mainnav.php'; ?>
            <!--  start dashboard_main.php   -->
            <div class="container-fluid py-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-body px-0 pt-0 pb-2">
                                <div class="table-responsive p-0">
                                    <table class="table mb-0">
                                        <thead>
                                            <div>
                                                <h3>Liste des hebergements</h3>
                                                <button type="button" class="btn btn-warning" style="background:yellow; color:black;">
                                                    <a href="admin_ajouter_hebergement.php">Ajouter un nouvel hebergement</a>
                                                </button>
                                            </div>

                                            <tr style="text-align: center; color:black; text-decoration:solid;">
                                                <th scope="col">#</th>
                                                <th scope="col">Nom</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Capacité maximum</th>
                                                <th scope="col">Statut</th>
                                                <th scope="col">Photos</th>
                                                <th scope="col">Point d'arret</th>

                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $numero = 1; // Initialisez la variable du numéro
                                                foreach ($hebergement as $hebergementItem) :
                                            ?>
                                            <tr class="align-middle">
                                                <td><?php echo $numero++; ?></td>
                                                <td><?php echo htmlspecialchars($hebergementItem['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($hebergementItem['description']); ?></td>
                                                <td><?php echo htmlspecialchars($hebergementItem['capacite_max']); ?></td>
                                                <td><?php
                                                        $statut = $hebergementItem['statut'];
                                                        $badgeClass = $statut === 'ouvert' ? 'badge badge-success' : 'badge badge-danger';
                                                        echo '<span class="' . $badgeClass . '">' . htmlspecialchars($statut) . '</span>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if (file_exists($hebergementItem['photo'])) : ?>
                                                        <img class="img-thumbnail" src="<?php echo $hebergementItem['photo']; ?>" alt="Image point d'arret">
                                                    <?php else : ?>
                                                        <span>Image not available</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        // Requête pour récupérer le nom du point d'arrêt associé à l'hébergement
                                                        $sql_select_point_arret = "SELECT nom FROM PointArret WHERE point_arret_id = :point_arret_id";
                                                        $stmt_select_point_arret = $conn->prepare($sql_select_point_arret);
                                                        $stmt_select_point_arret->bindParam(':point_arret_id', $hebergementItem['point_arret_id'], PDO::PARAM_INT);
                                                        $stmt_select_point_arret->execute();
                                                        $point_arret = $stmt_select_point_arret->fetch(PDO::FETCH_ASSOC);
                                                        echo htmlspecialchars($point_arret['nom']);
                                                    ?>
                                                </td>
                                                <td>
                                                    <button type="submit" class="btn btn-info btn-lg"><a href="admin_modifier_hebergement.php?id=<?php echo $hebergementItem['hebergement_id']; ?>">Modifier</a></button>
                                                    <button type="button" class="btn btn-warning btn-lg">
                                                        <a href="admin_supprimer_hebergement.php?id=<?php echo $hebergementItem['hebergement_id']; ?>" class="btn-supprimer" onclick="confirmerSuppression(this)">Supprimer</a>
                                                    </button>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- NAV CONTENT -->
        </main>

            <!-- ... -->
    <script>
        // Fonction de confirmation de suppression
        function confirmerSuppression(lien) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce hebergement ?")) {
                // Rediriger vers admin_supprimer_hebergement.php pour la suppression
                window.location.href = lien.getAttribute('href');
            } else {
                // Rediriger vers admin_afficher_hebergement.php si l'admin annule
                window.location.href = 'admin_afficher_hebergement.php';
            }
        }

        // Fonction pour cacher l'alerte après un certain délai
        function cacherAlerte() {
            var alerte = document.querySelector('.alert');
            if (alerte) {
                setTimeout(function() {
                    alerte.style.display = 'none';
                }, 5000); // 5000 millisecondes = 5 secondes
            }
        }

        // Appeler la fonction lors du chargement de la page
        window.onload = function() {
            cacherAlerte();

            // Attacher la fonction de confirmation aux liens de suppression
            const liensSuppression = document.querySelectorAll('.btn-supprimer');
            liensSuppression.forEach(lien => {
                lien.addEventListener('click', () => {
                    confirmerSuppression(lien);
                });
            });
        };

    </script>


            <!-- ... -->

    <!--  start dashboard_config.php   -->
    <?php include '../dashboard_config.php'; ?>
    <!--  end dashboard_config.php   -->

    <!--  start dashboard_js.php   -->
    <?php include '../dashboard_js.php'; ?>
    <!--  start dashboard_js.php   -->

    </body>

</html>