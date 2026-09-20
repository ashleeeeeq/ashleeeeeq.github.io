<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Venusia | About Us – Elegance Woven into Every Thread</title>
    <!-- Google Fonts + Bootstrap + Font Awesome (matching reference style) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/creatrix_logo.png') ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/creatrix_logo.png') ?>">
    
    <style>
        /* ----- VENUSIA COLOR PALETTE (aligned with CREATRIX reference but softened for elegance) ----- */
        :root {
            --bg: #F8F7F5;        /* warm neutral background like the reference’s #F0F4F1 but softer */
            --card-bg: #FFFFFF;
            --primary: #7A5C4E;    /* warm taupe/earthy brown — sophisticated, feminine alternative to #1F4529 */
            --primary-light: #9C7B68;
            --accent: #B88B6B;     /* soft terracotta / warm sand — elegant accent */
            --accent-light: #EFE3DA;
            --accent-hover: #9E6A4A;
            --text: #3A2C28;
            --text-light: #7A6A62;
            --white: #FFFFFF;
            --shadow-sm: 0 4px 12px rgba(90, 65, 55, 0.05);
            --shadow-md: 0 12px 24px rgba(90, 65, 55, 0.08);
            --shadow-hover: 0 20px 30px rgba(122, 92, 78, 0.12);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* header style exactly matching reference but using venusia brand colors */
        header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--white);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo img {
            height: 45px;
            filter: brightness(0) invert(1);
            transition: transform 0.3s;
        }

        .logo:hover img {
            transform: scale(1.05);
        }

        .logo h2 {
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
            letter-spacing: 1px;
        }

        nav {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        nav a {
            color: rgba(255, 255, 255, 0.88);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 18px;
            border-radius: 40px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        nav a:hover {
            background: rgba(255, 255, 255, 0.18);
            color: white;
        }

        nav a.active {
            background: var(--white);
            color: var(--primary);
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        /* main container padding, consistent with reference spacing */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 28px 60px;
        }

        /* About Header section - elegant hero */
        .about-header {
            background: linear-gradient(105deg, rgba(122, 92, 78, 0.05) 0%, rgba(184, 139, 107, 0.08) 100%), url('https://images.unsplash.com/photo-1539109136881-3be0616acf4b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center 40%;
            border-radius: 32px;
            margin: 20px 24px 0 24px;
            padding: 90px 30px;
            position: relative;
            box-shadow: var(--shadow-sm);
        }

        .about-header-content {
            max-width: 700px;
            text-align: center;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(6px);
            border-radius: 48px;
            padding: 48px 32px;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(255,255,240,0.6);
        }

        .about-header-content h1 {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
            letter-spacing: -0.3px;
        }

        .about-header-content p {
            font-size: 1.2rem;
            color: var(--text);
            font-weight: 400;
            line-height: 1.5;
            opacity: 0.85;
        }

        /* section titles identical to reference but with brand colors */
        .section-title {
            font-size: 2.4rem;
            font-weight: 700;
            color: var(--primary);
            margin: 60px 0 30px 0;
            text-align: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 70px;
            height: 3px;
            background: var(--accent);
            margin: 16px auto 0;
            border-radius: 4px;
        }

        .sub-title {
            font-size: 1.9rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sub-title i {
            color: var(--accent);
            font-size: 1.8rem;
        }

        .content {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--text-light);
        }

        /* Story highlight (cards) */
        .story-highlight {
            display: flex;
            align-items: center;
            gap: 40px;
            margin: 55px 0;
            background: var(--card-bg);
            border-radius: 32px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            flex-wrap: wrap;
        }
        .story-highlight:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-3px);
        }
        .story-highlight.reverse {
            flex-direction: row-reverse;
        }
        .story-highlight-img {
            flex: 1.2;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        .story-highlight-img img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            transition: transform 0.5s ease;
            display: block;
        }
        .story-highlight:hover .story-highlight-img img {
            transform: scale(1.02);
        }
        .story-highlight-content {
            flex: 1;
            padding: 20px;
        }
        .story-highlight-content h3 {
            font-size: 1.9rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 18px;
        }
        .story-highlight-content p {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--text-light);
        }

        /* Mission & Vision (two-column boxes) */
        #mission-vision {
            display: flex;
            gap: 35px;
            margin: 60px 0 50px;
            flex-wrap: wrap;
        }
        .mission-box, .vision-box {
            flex: 1;
            background: var(--card-bg);
            padding: 38px 30px;
            border-radius: 32px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            border: 1px solid rgba(184, 139, 107, 0.2);
        }
        .mission-box:hover, .vision-box:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        .mission-box .content p, .vision-box .content p {
            margin-bottom: 18px;
        }

        /* Values grid (3 cards) */
        .values-container {
            margin: 40px 0;
        }
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 35px;
            margin-top: 35px;
        }
        .value-card {
            background: var(--card-bg);
            border-radius: 28px;
            padding: 35px 25px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            border-bottom: 4px solid transparent;
        }
        .value-card:hover {
            transform: translateY(-8px);
            border-bottom-color: var(--accent);
            box-shadow: var(--shadow-hover);
        }
        .value-icon {
            width: 80px;
            height: 80px;
            background: var(--accent-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.4rem;
            color: var(--primary);
            transition: var(--transition);
        }
        .value-card:hover .value-icon {
            background: var(--accent);
            color: white;
        }
        .value-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary);
        }
        .value-card p {
            color: var(--text-light);
            line-height: 1.5;
        }

        /* Team section */
        .team {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 35px;
            margin: 45px 0 20px;
        }
        .member {
            background: var(--card-bg);
            border-radius: 28px;
            padding: 28px 20px 32px;
            text-align: center;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }
        .member:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
        }
        .member-img {
            width: 140px;
            height: 140px;
            margin: 0 auto 18px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--accent-light);
            transition: transform 0.3s;
        }
        .member-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .member h4 {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 12px 0 5px;
            color: var(--primary);
        }
        .member span {
            font-size: 0.85rem;
            color: var(--accent);
            font-weight: 500;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 12px;
        }
        .member p {
            font-size: 0.9rem;
            color: var(--text-light);
            padding: 0 8px;
            margin: 15px 0;
        }
        .social-links {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 15px;
        }
        .social-links a {
            color: var(--primary-light);
            background: var(--accent-light);
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: var(--transition);
            font-size: 1rem;
            text-decoration: none;
        }
        .social-links a:hover {
            background: var(--accent);
            color: white;
            transform: translateY(-3px);
        }

        /* CTA section matching reference card-like style */
        .cta-section {
            background: linear-gradient(125deg, var(--primary-light) 0%, var(--primary) 100%);
            border-radius: 48px;
            padding: 65px 40px;
            text-align: center;
            margin: 70px 0 50px;
            color: white;
            box-shadow: var(--shadow-md);
        }
        .cta-section h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .cta-section p {
            font-size: 1.1rem;
            max-width: 550px;
            margin: 0 auto 28px;
            opacity: 0.9;
        }
        .cta-button {
            background: var(--white);
            color: var(--primary);
            padding: 14px 36px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
            font-size: 1rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .cta-button:hover {
            transform: translateY(-3px);
            background: var(--accent-light);
            color: var(--primary);
            box-shadow: 0 12px 20px rgba(0,0,0,0.2);
        }

        /* Statistics section similar to reference metrics */
        .stats-section {
            background: var(--card-bg);
            border-radius: 48px;
            padding: 45px 30px;
            margin: 40px 0 20px;
            box-shadow: var(--shadow-sm);
        }
        .stats-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 35px;
            text-align: center;
        }
        .stat-item {
            flex: 1;
            min-width: 140px;
        }
        .stat-number {
            font-size: 2.6rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .stat-label {
            font-size: 1rem;
            font-weight: 500;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* footer matching reference styling */
        footer {
            background: linear-gradient(135deg, var(--primary) 0%, #5A3E32 100%);
            color: rgba(255,255,255,0.85);
            padding: 45px 5% 30px;
            margin-top: 70px;
            text-align: center;
            font-size: 0.9rem;
        }
        footer p {
            margin: 8px 0;
        }

        /* Responsive touches */
        @media (max-width: 992px) {
            .story-highlight, .story-highlight.reverse {
                flex-direction: column;
            }
            .about-header-content h1 {
                font-size: 2.4rem;
            }
            .section-title {
                font-size: 2rem;
            }
            .sub-title {
                font-size: 1.6rem;
            }
            .stats-container {
                gap: 20px;
            }
        }
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 15px;
            }
            nav {
                justify-content: center;
            }
            .container {
                padding: 20px 20px;
            }
            .about-header {
                margin: 10px 16px;
                padding: 40px 20px;
            }
            .cta-section {
                padding: 40px 25px;
            }
        }
    </style>
</head>
<body>

<!-- ==================== HEADER (consistent with reference's navigation style, adapted for Venusia) ================= -->
<header>
    <div class="logo">
        <!-- placeholder elegant logo icon / using venusia-inspired -->
        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='46' fill='none' stroke='white' stroke-width='3'/%3E%3Cpath fill='white' d='M50,30 L62,50 L50,70 L38,50 Z'/%3E%3C/svg%3E" alt="Venusia Icon">
        <h2>VENUSIA</h2>
    </div>
    <nav>
        <a href="#"><i class="fas fa-home"></i> Home</a>
        <a href="#" class="active"><i class="fas fa-leaf"></i> About</a>
        <a href="#"><i class="fas fa-shopping-bag"></i> Collection</a>
        <a href="#"><i class="far fa-heart"></i> Journal</a>
        <a href="#"><i class="fas fa-user"></i> Account</a>
    </nav>
</header>

<div class="main-container">
    <!-- About Header (hero) -->
    <section class="about-header">
        <div class="about-header-content">
            <h1>Elegance Woven into Every Thread</h1>
            <p>
                Discover the story behind Venusia, where timeless design meets modern femininity and every stitch tells a story of craftsmanship.
            </p>
        </div>
    </section>

    <main class="container">
        <!-- Our Story -->
        <section id="our-story">
            <h1 class="section-title">Our Story</h1>
            <div class="content" style="text-align: center; max-width: 800px; margin: 0 auto 30px;">
                <p>
                    Founded in 2023, Venusia emerged from a passion for refined elegance in women's fashion. Rooted in the belief that every woman deserves clothing that feels as beautiful as it looks, our journey began with a small atelier and a big dream: to bring softness and sophistication to everyday wardrobes.
                </p>
            </div>

            <div class="story-highlight">
                <div class="story-highlight-img">
                    <img src="https://i.pinimg.com/736x/4a/ed/93/4aed93e9e963b60532832701c85a1b82.jpg" alt="Venusia Atelier" />
                </div>
                <div class="story-highlight-content">
                    <h3>The Beginning</h3>
                    <p>
                        What started as a humble collection of carefully crafted pieces has blossomed into a celebrated brand known for its attention to detail, luxurious fabrics, and timeless silhouettes. Each Venusia garment is designed to celebrate the feminine form while providing unparalleled comfort.
                    </p>
                </div>
            </div>

            <div class="story-highlight reverse">
                <div class="story-highlight-img">
                    <!-- modern team / group image from unsplash elegance -->
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80" alt="Venusia Team Collaboration" />
                </div>
                <div class="story-highlight-content">
                    <h3>Today & Beyond</h3>
                    <p>
                        Today, we continue to honor our founding principles while innovating for the modern woman who values both tradition and progress in her personal style. Our collections blend classic elegance with contemporary touches, creating pieces that transcend seasons.
                    </p>
                </div>
            </div>
        </section>

        <!-- Mission & Vision -->
        <section id="mission-vision">
            <div class="mission-box">
                <h2 class="sub-title"><i class="fas fa-bullseye"></i> Our Mission</h2>
                <div class="content">
                    <p>
                        To craft timeless pieces that celebrate femininity, empowering women to express their unique style with grace and confidence. We commit to sustainable practices and ethical production while maintaining the highest standards of quality and craftsmanship.
                    </p>
                    <p>
                        Every Venusia garment is designed with intention, created to become a cherished part of your wardrobe for years to come.
                    </p>
                </div>
            </div>
            <div class="vision-box">
                <h2 class="sub-title"><i class="fas fa-eye"></i> Our Vision</h2>
                <div class="content">
                    <p>
                        To become the leading destination for women seeking elegance and comfort in every stitch, inspiring a community of self-assured, organized, and graceful individuals.
                    </p>
                    <p>
                        We envision a world where every woman feels beautiful, confident, and empowered in clothing that reflects her inner strength and personal aesthetic.
                    </p>
                </div>
            </div>
        </section>

        <!-- Values -->
        <section id="values">
            <div class="values-container">
                <h1 class="section-title">Our Values</h1>
                <div class="values-grid">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-heart"></i></div>
                        <h3>Quality Craftsmanship</h3>
                        <p>Every stitch, seam, and detail is meticulously crafted to ensure longevity and beauty in every piece we create.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-leaf"></i></div>
                        <h3>Sustainable Practices</h3>
                        <p>We prioritize eco-friendly materials and ethical production methods to minimize our environmental impact.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-female"></i></div>
                        <h3>Feminine Empowerment</h3>
                        <p>Our designs celebrate the female form, enhancing confidence and personal expression through elegant fashion.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team (using updated assets from reference but with local fallback if needed) -->
        <section id="team">
            <h1 class="section-title">Meet the Team</h1>
            <p class="content" style="text-align: center; max-width: 600px; margin: 0 auto 30px;">The passionate individuals behind Venusia's digital presence and innovative solutions</p>
            <div class="team">
                <div class="member">
                    <div class="member-img">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Kim Shin E. Del Amen" />
                    </div>
                    <h4>Kim Shin E. Del Amen</h4>
                    <span>Researcher & Frontend Developer</span>
                    <p>Kim specializes in user experience research and implements beautiful, functional interfaces that bring the Venusia brand to life online.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="far fa-envelope"></i></a>
                    </div>
                </div>
                <div class="member">
                    <div class="member-img">
                        <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Tara Victoria E. Gerones" />
                    </div>
                    <h4>Tara Victoria E. Gerones</h4>
                    <span>UI/UX Designer & Frontend Developer</span>
                    <p>Tara combines market research with frontend development skills to create digital experiences that resonate with Venusia customers.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="far fa-envelope"></i></a>
                    </div>
                </div>
                <div class="member">
                    <div class="member-img">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Racelle H. Millagracia" />
                    </div>
                    <h4>Racelle H. Millagracia</h4>
                    <span>Full Stack Developer</span>
                    <p>Racelle handles the robust systems that power Venusia's e-commerce platform and customer databases while making things look good.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="far fa-envelope"></i></a>
                    </div>
                </div>
                <div class="member">
                    <div class="member-img">
                        <img src="https://randomuser.me/api/portraits/women/79.jpg" alt="Janna Ashley H. Quiban" />
                    </div>
                    <h4>Janna Ashley H. Quiban</h4>
                    <span>Researcher & Frontend Developer</span>
                    <p>Ashley brings the Venusia brand to life online by conducting user experience research and developing engaging, functional interfaces.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="far fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="cta-section">
            <h2>Ready to Experience Venusia?</h2>
            <p>Discover our latest collection and find pieces that speak to your unique style and personality.</p>
            <a href="#" class="cta-button">Explore Our Collection <i class="fas fa-arrow-right ms-2"></i></a>
        </section>

        <!-- Statistics -->
        <section class="stats-section">
            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-number" data-target="2023">2023</div>
                    <div class="stat-label">Founded</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="500">350+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="150">120+</div>
                    <div class="stat-label">Unique Pieces</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="100">85%</div>
                    <div class="stat-label">Sustainable Materials</div>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Footer (matching reference style) -->
<footer>
    <p>&copy; 2026 VENUSIA. All rights reserved. | Timeless elegance, modern femininity — For educational inspiration only.</p>
    <p style="margin-top: 10px; font-size: 0.8rem;">Venusia Atelier — Where every thread tells a story of grace and craftsmanship.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- simple stats animation (optional gentle counter for stats numbers - similar reference style subtle) -->
<script>
    // simple fade-in stat counters to give interactive feel
    const statNumbers = document.querySelectorAll('.stat-number');
    const animateNumbers = () => {
        statNumbers.forEach(el => {
            const rawText = el.innerText;
            let targetVal = 0;
            if(rawText.includes('+')) targetVal = parseInt(rawText.replace(/[^0-9]/g, ''));
            else if(rawText.includes('%')) targetVal = parseInt(rawText.replace('%',''));
            else targetVal = parseInt(rawText);
            if(!isNaN(targetVal) && el.getAttribute('data- animated') !== 'true') {
                el.setAttribute('data-animated', 'true');
                let current = 0;
                const increment = Math.ceil(targetVal / 40);
                const update = () => {
                    current += increment;
                    if(current >= targetVal) {
                        el.innerText = rawText.includes('%') ? targetVal + '%' : (rawText.includes('+') ? targetVal + '+' : targetVal);
                        return;
                    }
                    if(rawText.includes('%')) el.innerText = current + '%';
                    else if(rawText.includes('+')) el.innerText = current + '+';
                    else el.innerText = current;
                    requestAnimationFrame(update);
                };
                update();
            }
        });
    };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                animateNumbers();
                observer.disconnect();
            }
        });
    }, { threshold: 0.3 });
    const statsSection = document.querySelector('.stats-section');
    if(statsSection) observer.observe(statsSection);
</script>
</body>
</html>