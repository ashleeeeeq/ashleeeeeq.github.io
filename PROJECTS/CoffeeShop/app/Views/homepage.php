<?php
$title = "Kape-Con Coffee Shop";
$hero_subtitle = "FRESHLY BREWED";
$hero_title = "Craft Coffee Made Daily";
$hero_description = "Artisan espresso, handcrafted beverages, and premium beans delivered fresh.";

$collections = [
    [ 'title' => 'Signature Coffees', 'image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800&fit=crop' ],
    [ 'title' => 'Cold Brew & Iced', 'image' => 'https://images.unsplash.com/photo-1511920170033-f8396924c348?w=800&fit=crop' ],
    [ 'title' => 'Pastries & Treats', 'image' => 'https://images.unsplash.com/photo-1483695028939-5bb13f8648b0?w=800&fit=crop' ]
];

$featured_products = [
    [ 'name' => 'Caramel Macchiato', 'image' => 'https://images.unsplash.com/photo-1579888071069-c107a6f79d82?auto=format&fit=crop&q=80&w=870', 'rating' => 5, 'price' => '₱180.00', 'colors' => ['#8B4513','#D4A574','#6F3609'] ],
    [ 'name' => 'Iced Spanish Latte', 'image' => 'https://images.unsplash.com/photo-1658646479124-bc31e6849497?auto=format&fit=crop&q=80&w=951', 'rating' => 4, 'price' => '₱165.00', 'colors' => ['#c49b63','#8b5a2b','#d7c0a8'] ],
    [ 'name' => 'Mocha Espresso', 'image' => 'https://media.istockphoto.com/id/2220401154/photo/coffee-cup-and-coffee-beans-on-wooden-table-espresso-crema-coffee-cup-aromatic.jpg?s=612x612&w=0&k=20&c=M71IHjkETXUNqz-Lda5kSIg1KVmpK_XFbf-GvIg_kIQ=', 'rating' => 5, 'price' => '₱150.00', 'colors' => ['#3e2723','#8d6e63','#5d4037'] ],
    [ 'name' => 'Classic Cappuccino', 'image' => 'https://media.istockphoto.com/id/1250721187/photo/cup-of-cappuccino-coffee-with-sugar-on-a-marble-table.jpg?s=612x612&w=0&k=20&c=dW_mIwvmx1fz6OV3KdGMIA-M_t_N6vzmLti0p-eqNBs=', 'rating' => 4, 'price' => '₱140.00', 'colors' => ['#402218','#D7A86E','#A47E3B'] ]
];

$latest_news = [
    [ 'title' => 'How to Brew the Perfect Cup', 'image' => 'https://media.istockphoto.com/id/959375940/photo/barista-prepare-coffee-at-bar-counter.jpg?s=612x612&w=0&k=20&c=PfzmApzaXXHVYzP_ESppxAgsrgkIsdw5CeHl30QbHXg=', 'date' => 'Nov 12, 2024', 'link' => '#' ],
    [ 'title' => 'Best Beans of the Year', 'image' => 'https://media.istockphoto.com/id/1889951697/photo/happy-new-year-2024-with-coffee-beans-with-concept-texture.jpg?s=612x612&w=0&k=20&c=2rYhpIuxI211j8GvIIbSCd3IE3rzMI8q-_bWz0dNghk=', 'date' => 'Dec 18, 2024', 'link' => '#' ],
    [ 'title' => 'Cold Brew Tips for Summer', 'image' => 'https://media.istockphoto.com/id/1151004220/photo/glass-of-iced-coffee-on-a-garden-table-outdoors-on-a-sunny-summers-day.jpg?s=612x612&w=0&k=20&c=mG7Ghwzk8jXb_9g0ZbEK6hOe1gtv_4o645rFTYVkdu4=', 'date' => 'Mar 15, 2025', 'link' => '#' ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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

    * {
        scroll-behavior: smooth;
    }

    body { 
        font-family: 'Poppins', sans-serif;
        background: var(--white);
        color: var(--black);
    }

    /* Enhanced Navbar */
    .navbar {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95) !important;
        box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        padding: 1rem 0;
        transition: all 0.3s ease;
    }

    .navbar-brand {
        color: var(--primary-color) !important;
        font-weight: 800;
        font-size: 1.5rem;
        letter-spacing: -0.5px;
    }

    .navbar-brand:hover {
        color: var(--primary-dark) !important;
    }

    .nav-link {
        color: var(--black) !important;
        font-weight: 500;
        margin: 0 0.5rem;
        position: relative;
        transition: color 0.3s ease;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: var(--primary-color);
        transition: width 0.3s ease;
    }

    .nav-link:hover {
        color: var(--primary-color) !important;
    }

    .nav-link:hover::after,
    .nav-link.active::after {
        width: 60%;
    }

    /* Hero Section */
    #hero {
        min-height: 90vh;
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, var(--white) 0%, var(--light-bg) 100%);
        position: relative;
        overflow: hidden;
    }

    #hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(212, 165, 116, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .hero-subtitle {
        color: var(--primary-color);
        font-weight: 700;
        letter-spacing: 3px;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        color: var(--black);
    }

    .hero-description {
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 2rem;
        line-height: 1.8;
    }

    .hero-image {
        position: relative;
        z-index: 1;
    }

    .hero-image img {
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        transition: transform 0.5s ease;
    }

    .hero-image img:hover {
        transform: scale(1.05) rotate(2deg);
    }

    /* Buttons */
    .btn-shop {
        background: var(--primary-color);
        color: white;
        padding: 14px 35px;
        font-weight: 600;
        border-radius: 50px;
        border: none;
        box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-shop:hover {
        background: var(--primary-dark);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(139, 69, 19, 0.4);
    }

    .btn-shop i {
        font-size: 1.3rem;
        transition: transform 0.3s ease;
    }

    .btn-shop:hover i {
        transform: translateX(5px);
    }

    /* Section Titles */
    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 3rem;
        position: relative;
        display: inline-block;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: var(--primary-color);
        border-radius: 2px;
    }

    /* Collections Section */
    #collections {
        background: var(--light-bg);
        padding: 5rem 0;
    }

    .collection-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.4s ease;
        background: white;
        height: 100%;
    }

    .collection-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .collection-card img {
        height: 250px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .collection-card:hover img {
        transform: scale(1.1);
    }

    .collection-card .card-body {
        padding: 1.5rem;
    }

    .collection-card .card-title {
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 1rem;
        color: var(--black);
    }

    /* Products Section */
    #products {
        padding: 5rem 0;
    }

    .product-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s ease;
        background: white;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .product-card img {
        height: 250px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover img {
        transform: scale(1.15);
    }

    .product-card .card-body {
        padding: 1.5rem;
    }

    .product-card h5 {
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: var(--black);
    }

    .product-card p {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .btn-order {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-order:hover {
        background: var(--primary-dark);
        color: white;
        transform: scale(1.05);
    }

    /* News Section */
    #news {
        background: var(--light-bg);
        padding: 5rem 0;
    }

    .news-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.4s ease;
        background: white;
        height: 100%;
    }

    .news-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .news-card img {
        height: 200px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .news-card:hover img {
        transform: scale(1.1);
    }

    .news-card .card-body {
        padding: 1.5rem;
    }

    .news-date {
        color: #999;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .news-card h5 {
        font-weight: 700;
        font-size: 1.2rem;
        margin: 1rem 0;
        color: var(--black);
    }

    .read-more {
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: gap 0.3s ease;
    }

    .read-more:hover {
        gap: 10px;
        color: var(--primary-dark);
    }

    /* Footer */
    footer {
        background: var(--black);
        color: var(--white);
        padding: 2rem 0;
    }

    footer p {
        margin: 0;
        opacity: 0.8;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .section-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 768px) {
        #hero {
            min-height: auto;
            padding: 3rem 0;
        }

        .hero-title {
            font-size: 2rem;
        }

        .hero-description {
            font-size: 1rem;
        }

        .hero-image {
            margin-top: 2rem;
        }
    }
</style>

</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand" href="#hero">☕ Kape-Con</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="#hero">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#collections">Menu</a></li>
        <li class="nav-item"><a class="nav-link" href="#products">Featured</a></li>
        <li class="nav-item"><a class="nav-link" href="#news">News</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/order') ?>">Order</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/profile') ?>">Profile</a></li>
      </ul>
    </div>
  </div>
</nav>

<section id="hero" class="container">
  <div class="row align-items-center">
    <div class="col-lg-6">
      <h5 class="hero-subtitle"><?= $hero_subtitle ?></h5>
      <h1 class="hero-title"><?= $hero_title ?></h1>
      <p class="hero-description"><?= $hero_description ?></p>
      <a href="<?= base_url('/order') ?>" class="btn-shop">
        Order Coffee Now <i class='bx bx-right-arrow-alt'></i>
      </a>
    </div>
    <div class="col-lg-6 hero-image text-center">
      <img src="https://images.pexels.com/photos/683039/pexels-photo-683039.jpeg" class="img-fluid" alt="Coffee Hero">
    </div>
  </div>
</section>

<section id="collections">
  <div class="container">
    <div class="text-center">
      <h2 class="section-title">Our Coffee Selection</h2>
    </div>
    <div class="row g-4">
      <?php foreach($collections as $c): ?>
      <div class="col-md-4">
        <div class="card collection-card shadow-sm">
          <img src="<?= $c['image'] ?>" class="card-img-top" alt="<?= $c['title'] ?>">
          <div class="card-body text-center">
            <h5 class="card-title"><?= $c['title'] ?></h5>
            <a href="<?= base_url('/order') ?>" class="btn btn-order">Explore Menu</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="products">
  <div class="container">
    <div class="text-center">
      <h2 class="section-title">Featured Drinks</h2>
    </div>
    <div class="row g-4">
      <?php foreach($featured_products as $p): ?>
      <div class="col-md-3 col-sm-6">
        <div class="card product-card">
          <img src="<?= $p['image'] ?>" class="card-img-top" alt="<?= $p['name'] ?>">
          <div class="card-body d-flex flex-column">
            <h5><?= $p['name'] ?></h5>
            <p><?= $p['price'] ?></p>
            <a href="<?= base_url('/order') ?>" class="btn btn-order mt-auto">Order Now</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="news">
  <div class="container">
    <div class="text-center">
      <h2 class="section-title">Coffee News & Tips</h2>
    </div>
    <div class="row g-4">
      <?php foreach($latest_news as $n): ?>
      <div class="col-md-4">
        <div class="card news-card shadow-sm">
          <img src="<?= $n['image'] ?>" class="card-img-top" alt="<?= $n['title'] ?>">
          <div class="card-body">
            <small class="news-date">
              <i class='bx bxs-calendar'></i> <?= $n['date'] ?>
            </small>
            <h5><?= $n['title'] ?></h5>
            <a href="<?= $n['link'] ?>" class="read-more">
              Read more <i class='bx bx-right-arrow-alt'></i>
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<footer>
  <div class="container text-center">
    <p>© <?= date('Y') ?> Kape-Con Coffee Shop. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Smooth scroll and active nav link
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        if (this.getAttribute('href').startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Update active link
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        }
    });
});

// Update active link on scroll
window.addEventListener('scroll', () => {
    let current = '';
    const sections = document.querySelectorAll('section[id]');
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        if (scrollY >= sectionTop) {
            current = section.getAttribute('id');
        }
    });
    
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
});
</script>
</body>
</html>