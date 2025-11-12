 <nav class="bg-blue-600 text-white shadow-lg" role="navigation" aria-label="Navigation principale">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Logo et titre -->
                <div class="flex items-center">
                    <a href="/bibliotheque/index.php" class="text-2xl font-bold hover:text-blue-200 transition">
                        📚 Bibliothèque
                    </a>
                </div>

                <!-- Menu de navigation -->
                <ul class="flex items-center space-x-6">
                    <!-- Lien vers l'accueil -->
                    <li>
                        <a href="/bibliotheque/index.php" class="hover:text-blue-200 transition font-medium">
                            Accueil
                        </a>
                    </li>

                    <!-- Liens visibles uniquement pour les administrateurs connectés -->
                    <li>
                        <a href="/bibliotheque/admin/dashboard.php" class="hover:text-blue-200 transition font-medium">
                            Tableau de bord
                        </a>
                    </li>
                    <li>
                        <a href="/bibliotheque/admin/livres.php" class="hover:text-blue-200 transition font-medium">
                            Gérer les livres
                        </a>
                    </li>
                    <li>
                        <span class="text-blue-200">
                            Bonjour, Jean-Michel
                        </span>
                    </li>
                    <?php if (isset($_SESSION['admin_id'])): ?>
                    <li>
                        <!-- Bouton de déconnexion -->
                        <a href="/bibliotheque/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition font-medium">
                            Déconnexion
                        </a>
                    
                    </li>
                    <?php else: ?>
                    <!-- Lien visible uniquement pour les visiteurs non connectés -->
                    <li>
                        <a href="/bibliotheque/login.php" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded transition font-medium">
                            Connexion
                        </a>
                    </li>
                    <li>
                        <a href="inscription.php" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded transition font-medium">
                            Inscription
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>