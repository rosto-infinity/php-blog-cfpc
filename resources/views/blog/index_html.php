<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Stage Professionnel en Génie Logiciel et Développement Web/Mobile</h1>
        <p>L'objectif est de pérenniser le parcours d'un développeur fullstack en mettant en lumière les projets, les compétences et les réalisations des étudiants dans les domaines du développement web et mobile. Découvrez comment ils maîtrisent les technologies clés et construisent des solutions innovantes pour l'avenir.</p>
        <div class="technologies">
            <span>HTML</span>
            <span>CSS</span>
            <span>TailwindCSS</span>
            <span>JavaScript</span>
            <span>Dart</span>
            <span>Flutter</span>
            <span>Laravel</span>
            <span>NodeJS</span>
            <span>ReactJS</span>
        </div>
        <a href="#projects" class="cta-button">Voir les Projets</a>
    </div>
</section>

<!-- Section Articles -->
<section id="projects" class="articles-section">
    <h2>Derniers Articles</h2>
    <div class="articles-grid">
        <?php foreach ($articles as $article): ?>
            <div class="article-card">
                <?php if (!empty($article['image'])): ?>
                    <a href="user-article-show.php?id=<?= urlencode((string)$article['id']); ?>">
                        <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="article-image">
                    </a>
                <?php else: ?>
                    <a href="user-article-show.php?id=<?= urlencode((string)$article['id']); ?>">
                        <img src="/resources/images/default-article.jpeg" alt="Image par défaut" class="article-image">
                    </a>
                <?php endif; ?>

                <div class="article-content">
                    <h3 class="article-title"><?= htmlspecialchars($article['title']) ?></h3>
                    <div class="article-meta">
                        <span class="article-category">
                            <i class='bx bx-category-alt'></i> Article
                        </span>
                        <span class="article-date">
                            <i class='bx bx-calendar'></i> <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                        </span>
                    </div>
                    <p class="article-excerpt"><?= htmlspecialchars(substr($article['introduction'], 0, 120)) ?>...</p>
                    <div class="article-footer">
                       
                        <a href="user-article-show.php?id=<?= urlencode((string)$article['id']); ?>" class="read-more">
                            Lire <i class='bx bx-chevron-right'></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination Manuelle -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination-wrapper">
            <ul class="pagination">
                <!-- Précédent -->
                <li class="<?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                    <a href="?page=<?= max(1, $currentPage - 1) ?>">
                        <i class='bx bx-chevron-left'></i>
                    </a>
                </li>

                <!-- Pages -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="<?= ($i === $currentPage) ? 'active' : '' ?>">
                        <a href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Suivant -->
                <li class="<?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                    <a href="?page=<?= min($totalPages, $currentPage + 1) ?>">
                        <i class='bx bx-chevron-right'></i>
                    </a>
                </li>
            </ul>
        </div>
    <?php endif; ?>
</section>
<style>
    /* Style supplémentaire pour les cartes d'articles pour un look premium */
    .article-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .article-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .article-excerpt {
        flex-grow: 1;
    }

    .pagination-wrapper {
        margin-top: 40px;
    }
</style>


