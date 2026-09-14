<x-layout title="Home">
    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center overflow-hidden -mt-20 md:-mt-24">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/HERO.jpg') }}" alt="Hero background" class="w-full h-full object-cover" id="parallax-bg">
            <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(28,33,67,0.85) 0%, rgba(22,33,114,0.7) 100%);"></div>
        </div>
        
        <!-- Text Content -->
        <div class="relative z-10 w-full max-w-7xl ml-0 md:ml-12 px-6 py-30">
            <div class="max-w-4xl ml-0 text-left flex flex-col items-start">
                <div class="animate-fade-in-up">
                    <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6 leading-tight" style="font-family: var(--font-header1); color: white; text-shadow: 2px 2px 12px rgba(0,0,0,0.3); letter-spacing: -0.02em;">
                        Help level the playing field for a child in Payatas today
                    </h1>
                    <p class="text-lg md:text-xl mb-10 max-w-2xl md:mx-0 leading-relaxed" style="font-family: var(--font-body1); color: rgba(255,255,255,0.95); line-height: 1.7; text-shadow: 1px 1px 4px rgba(0,0,0,0.2);">
                        At Fairplay, we believe everyone deserves a fair chance. We believe that where you're born should not determine your future. We believe in making the world a fairer place.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-start">
                        <a href="/donate" class="group relative overflow-hidden px-8 py-4 font-semibold transition-all duration-300 hover:scale-105 active:scale-95 rounded-full text-lg" style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1); box-shadow: 0 8px 20px rgba(0,0,0,0.2);">
                            <span class="relative z-10">Get Involved</span>
                            <div class="absolute inset-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300" style="background-color: rgba(255,255,255,0.2);"></div>
                        </a>
                        <a href="#watch-video" class="group px-8 py-4 font-semibold transition-all duration-300 hover:scale-105 active:scale-95 rounded-full flex items-center justify-center gap-2 text-lg" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.15); color: white; backdrop-filter: blur(10px); border: 1.5px solid rgba(255,255,255,0.3);">
                            <span>Learn More</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Statement Section -->
    <section class="py-24 md:py-32 px-6" style="background-color: white;">
        <div class="max-w-4xl mx-auto text-center">
            <p class="text-xl md:text-2xl lg:text-3xl leading-relaxed" style="font-family: var(--font-body1); color: var(--color-primary1); line-height: 1.6;">
                "At Fairplay, we believe no-one should be trapped in poverty just because of where they were born. This is why our mission is to level the playing field so everyone has opportunities to learn, play, and grow."
            </p>
        </div>
    </section>

    <!-- YouTube Video Section -->
    <section id="watch-video" class="py-24 md:py-32 px-6 scroll-mt-24" style="background-color: var(--color-primary1);">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12 md:mb-16">
                <h2 class="text-5xl md:text-6xl lg:text-7xl font-bold mb-6" style="font-family: var(--font-header1); color: var(--color-accent1); letter-spacing: -0.01em;">Watch Our Story</h2>
                <p class="text-xl md:text-2xl max-w-2xl mx-auto" style="font-family: var(--font-body1); color: rgba(255,255,255,0.85);">Discover how your support helps level the playing field for children in Payatas</p>
                <div class="w-24 h-1 mx-auto mt-8 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
            
            <div class="relative pb-[56.25%] h-0 rounded-2xl overflow-hidden shadow-2xl transition-all duration-500 hover:shadow-3xl hover:scale-[1.02]">
                <iframe 
                    class="absolute top-0 left-0 w-full h-full"
                    src="https://www.youtube.com/embed/U5WLbOHew-M?autoplay=0&rel=0" 
                    title="FAIRALL YouTube Video"
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </section>

    <!-- What We Do Section -->
    <section class="py-24 md:py-32 px-6" style="background-color: var(--color-neutral-light1);">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12 md:mb-16">
                <h2 class="text-5xl md:text-6xl font-bold mb-4" style="font-family: var(--font-header1); color: var(--color-primary1); letter-spacing: -0.01em;">What We Do</h2>
                <div class="w-20 h-1 mx-auto rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <!-- Program 1 - Education -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                    <img src="{{ asset('images/EDUC.jpg') }}" alt="Education" class="w-full h-56 object-cover">
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-3" style="font-family: var(--font-header1); color: var(--color-primary1);">Education</h3>
                        <p class="text-gray-600 leading-relaxed" style="font-family: var(--font-body1);">Providing quality education, tutoring, and academic support to help children excel in school and build a foundation for their future.</p>
                    </div>
                </div>
                
                <!-- Program 2 - Sports -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                    <img src="{{ asset('images/SPORTS.jpg') }}" alt="Sports" class="w-full h-56 object-cover">
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-3" style="font-family: var(--font-header1); color: var(--color-primary1);">Sports</h3>
                        <p class="text-gray-600 leading-relaxed" style="font-family: var(--font-body1);">Using sports to teach teamwork, discipline, and leadership while promoting physical health and mental well-being.</p>
                    </div>
                </div>
                
                <!-- Program 3 - Social Business -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                    <img src="{{ asset('images/SOCIAL.jpg') }}" alt="Social Business" class="w-full h-56 object-cover">
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-3" style="font-family: var(--font-header1); color: var(--color-primary1);">Social Business</h3>
                        <p class="text-gray-600 leading-relaxed" style="font-family: var(--font-body1);">Vocational training and social enterprise programs that create sustainable livelihoods and economic opportunities.</p>
                    </div>
                </div>
            </div>
            
            <!-- Description text -->
            <div class="max-w-4xl mx-auto text-center">
                <p class="text-lg md:text-xl leading-relaxed" style="font-family: var(--font-body1); color: var(--color-primary1); line-height: 1.7;">
                    To achieve our mission, we run the Fairplay Youth Center, Fairplay Cafe, and the Payatas Sports Center. Our mentoring program develops physical and mental health, provides social and emotional support, and creates financial and academic opportunities. Only through holistic support can we break the poverty cycle and truly empower our community.
                </p>
            </div>
        </div>
    </section>

    <!-- Where We Work Section -->
    <section class="py-24 md:py-32 px-6" style="background-color: white;">
        <div class="max-w-6xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Left side - Image -->
                <div class="rounded-2xl overflow-hidden shadow-2xl">
                    <img src="{{ asset('images/PAYATAS.jpg') }}" alt="Payatas Community" class="w-full h-full object-cover">
                </div>
                
                <!-- Right side - Content -->
                <div>
                    <h2 class="text-5xl md:text-6xl font-bold mb-6" style="font-family: var(--font-header1); color: var(--color-primary1); letter-spacing: -0.01em;">Where We Work</h2>
                    <div class="w-16 h-1 mb-6 rounded-full" style="background-color: var(--color-accent1);"></div>
                    <p class="text-lg mb-4 leading-relaxed" style="font-family: var(--font-body1); color: var(--color-primary1);">
                        We work in the community of <strong>Payatas in the Philippines</strong>.
                    </p>
                    <p class="text-gray-600 mb-4 leading-relaxed" style="font-family: var(--font-body1);">
                        Payatas is known for the largest dumpsite in Metro Manila. The garbage industry is the main source of income for many of the families living here, even since the dumpsite closed operations. Most work as garbage truck drivers, garbage collectors, and scavengers, and live near or below the poverty line.
                    </p>
                    <p class="text-gray-600 mb-6 leading-relaxed" style="font-family: var(--font-body1);">
                        Throughout the years, Fairplay has become embedded in our community. The kids we mentor have shown remarkable progress when provided with the opportunities and support most of us take for granted, they can become the best at what they do.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Donation Section -->
    <section class="py-24 md:py-32 px-6" style="background-color: #f9fafb;">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold mb-4" style="font-family: var(--font-header1); color: var(--color-primary1);">Support Our Mission</h2>
                <div class="w-20 h-1 mx-auto rounded-full" style="background-color: var(--color-accent1);"></div>
                <p class="text-gray-500 mt-4 max-w-2xl mx-auto" style="font-family: var(--font-body1);">Join us in making a difference in the lives of children in Payatas</p>
            </div>
            
            <div class="flex flex-col items-center text-center p-8 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300" style="background-color: white;">
                <h3 class="text-2xl md:text-3xl font-bold mb-3" style="font-family: var(--font-header1); color: var(--color-primary1);">Make a Donation</h3>
                <p class="mb-6 leading-relaxed max-w-md" style="font-family: var(--font-body1); color: #6b7280; line-height: 1.6;">
                    Your generous donation helps provide education, sports programs, and social enterprise opportunities for children and families in Payatas.
                </p>
                
                <!-- Payment Methods -->
                <div class="w-full mb-8">
                    <p class="text-sm text-gray-500 mb-4" style="font-family: var(--font-body1);">Secure payment methods</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <div class="w-16 h-10 flex items-center justify-center"><img src="{{ asset('images/VISA.png') }}" alt="Visa" class="max-w-full max-h-full object-contain"></div>
                        <div class="w-16 h-10 flex items-center justify-center"><img src="{{ asset('images/MASTERCARD.png') }}" alt="Mastercard" class="max-w-full max-h-full object-contain"></div>
                        <div class="w-16 h-10 flex items-center justify-center"><img src="{{ asset('images/MAESTRO.png') }}" alt="Maestro" class="max-w-full max-h-full object-contain"></div>
                        <div class="w-16 h-10 flex items-center justify-center"><img src="{{ asset('images/AMERICAN_EXPRESS.png') }}" alt="American Express" class="max-w-full max-h-full object-contain"></div>
                        <div class="w-16 h-10 flex items-center justify-center"><img src="{{ asset('images/DISCOVER.png') }}" alt="Discover" class="max-w-full max-h-full object-contain"></div>
                        <div class="w-16 h-10 flex items-center justify-center"><img src="{{ asset('images/UNIONPAY.png') }}" alt="UnionPay" class="max-w-full max-h-full object-contain"></div>
                    </div>
                </div>
                
                <a href="/donate" class="inline-block px-8 py-3 font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg rounded-full text-center" style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Donate Now →
                </a>
            </div>
        </div>
    </section>

    <!-- Parallax Effect -->
    <script>
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.getElementById('parallax-bg');
            if (parallax) {
                parallax.style.transform = 'translateY(' + scrolled * 0.5 + 'px)';
            }
        });
    </script>
</x-layout>