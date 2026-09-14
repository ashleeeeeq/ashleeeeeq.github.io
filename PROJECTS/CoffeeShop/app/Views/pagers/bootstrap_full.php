<style>
    :root {
        --primary-color: #8B4513;
        --primary-dark: #6F3609;
        --accent-color: #D4A574;
        --black: #2c2c2c;
        --white: #faf8f5;
        --grey: #c8b6a6;
        --light-bg: #f8f6f3;
    }

    .pagination-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin: 2.5rem 0;
        text-align: center;
        width: 100%;
    }

    .pagination {
        display: flex;
        list-style: none;
        gap: 8px;
        padding: 0;
        margin: 0 auto;
        justify-content: center;
        flex-wrap: wrap;
    }

    .page-item {
        display: inline-block;
    }

    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 45px;
        height: 45px;
        padding: 0 16px;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        color: var(--black);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .page-link:hover {
        background: var(--light-bg);
        border-color: var(--accent-color);
        color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 69, 19, 0.15);
    }

    .page-item.active .page-link {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
        transform: translateY(-2px);
        pointer-events: none;
    }

    .page-item.disabled .page-link {
        background: #f5f5f5;
        border-color: #e0e0e0;
        color: #ccc;
        cursor: not-allowed;
        pointer-events: none;
    }

    .results-info {
        margin-top: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #6F3609;
        font-family: 'Poppins', sans-serif;
    }

    @media (max-width: 768px) {
        .page-link {
            min-width: 40px;
            height: 40px;
            font-size: 0.9rem;
        }
    }
</style>

<?php 
$pager->setSurroundCount(1);

$currentPage = $pager->getCurrentPageNumber();
$perPage     = $pager->getPerPage();
$total       = $pager->getTotal();

// Stop rendering entirely if no results
if ($total == 0 || $pager->getPageCount() == 0) {
    return;
}

// Compute real-time positions
$start = ($currentPage - 1) * $perPage + 1;
$end   = min($start + $perPage - 1, $total);
?>

<div class="pagination-container">

    <ul class="pagination">

        <!-- Previous -->
        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPrevious() ?>">«</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link">«</span>
            </li>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach; ?>

        <!-- Next -->
        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNext() ?>">»</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link">»</span>
            </li>
        <?php endif; ?>

    </ul>

    <!-- Real-time results summary -->
    <div class="results-info">
        Showing <?= $start ?>–<?= $end ?> of <?= $total ?> results
    </div>

</div>