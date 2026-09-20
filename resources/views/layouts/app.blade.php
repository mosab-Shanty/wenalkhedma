<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'وين الخدمة - دليل الخدمات المحلي والذكاء الاصطناعي')</title>

    <!-- Google Fonts: IBM Plex Sans Arabic for subtexts/body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fonts Definition: Thmanyah (ثمانية) for Headings -->
    <style>
        @font-face {
            font-family: 'Thmanyah';
            src: url('{{ asset("fonts/Thmanyah-Regular.woff2") }}') format('woff2'),
                 url('https://framerusercontent.com/assets/x6EBzvXf1Fi35XhsRoHxePDVo.woff2') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Thmanyah';
            src: url('{{ asset("fonts/Thmanyah-Medium.woff2") }}') format('woff2'),
                 url('https://framerusercontent.com/assets/oE98mOPE28KTzUeptOLRIaqW1I.woff2') format('woff2');
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Thmanyah';
            src: url('{{ asset("fonts/Thmanyah-Bold.woff2") }}') format('woff2'),
                 url('https://framerusercontent.com/assets/LZvgFRUsP7pGWYj3tKxMUrKuhSY.woff2') format('woff2');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Thmanyah';
            src: url('{{ asset("fonts/Thmanyah-Black.woff2") }}') format('woff2'),
                 url('https://framerusercontent.com/assets/ulQLGTktcl2Qq4AmD6RuVgELZKg.woff2') format('woff2');
            font-weight: 800 900;
            font-style: normal;
            font-display: swap;
        }

        body { 
            font-family: 'IBM Plex Sans Arabic', sans-serif; 
        }
        h1, h2, h3, h4, h5, h6, .font-heading { 
            font-family: 'Thmanyah', 'IBM Plex Sans Arabic', sans-serif; 
            letter-spacing: -0.01em;
        }
        [x-cloak] { display: none !important; }
    </style>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"IBM Plex Sans Arabic"', 'sans-serif'],
                        heading: ['"Thmanyah"', '"IBM Plex Sans Arabic"', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>
<body class="flex flex-col min-h-full text-slate-800 antialiased" x-data="{ aiOpen: false }">

    <!-- Header / Navbar -->
    @include('components.navbar')

    <!-- Flash Alerts -->
    @include('components.alert')

    <!-- Main Page Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Floating AI Assistant Trigger -->
    <button @click="aiOpen = true" 
            class="fixed bottom-6 left-6 z-40 flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-700 text-white px-4 py-3 rounded-full shadow-lg hover:shadow-emerald-600/30 hover:scale-105 transition-all duration-300 group">
        <i class="fa-solid fa-wand-magic-sparkles text-lg animate-pulse text-yellow-300"></i>
        <span class="font-bold text-sm">المساعد الذكي</span>
    </button>

    <!-- AI Assistant Modal Component -->
    @include('components.ai-modal')

    <!-- Footer -->
    @include('components.footer')

    <!-- Global Geolocation & Distance Calculation Script -->
    <script>
        (function () {
            window.WenAlKhedmaGeo = {
                storageKey: 'wenalkhedma_user_coords',

                getUserCoords: function () {
                    try {
                        const data = localStorage.getItem(this.storageKey);
                        return data ? JSON.parse(data) : null;
                    } catch (e) {
                        return null;
                    }
                },

                setUserCoords: function (lat, lng) {
                    const coords = { lat: parseFloat(lat), lng: parseFloat(lng), timestamp: Date.now() };
                    try {
                        localStorage.setItem(this.storageKey, JSON.stringify(coords));
                    } catch (e) {}
                    window.dispatchEvent(new CustomEvent('wenalkhedma:location-updated', { detail: coords }));
                    this.updateAllCards(coords);
                    return coords;
                },

                calculateDistance: function (lat1, lon1, lat2, lon2) {
                    if (!lat1 || !lon1 || !lat2 || !lon2) return null;
                    const R = 6371; // Earth's radius in km
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = 
                        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return R * c; // in kilometers
                },

                formatDistance: function (distKm) {
                    if (distKm === null || isNaN(distKm)) return '';
                    if (distKm < 1) {
                        const meters = Math.round(distKm * 1000);
                        return meters + ' م';
                    }
                    return distKm.toFixed(1) + ' كم';
                },

                requestLocation: function (onSuccess, onError) {
                    if (!navigator.geolocation) {
                        if (onError) onError(new Error('Geolocation not supported'));
                        return;
                    }
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const coords = this.setUserCoords(position.coords.latitude, position.coords.longitude);
                            if (onSuccess) onSuccess(coords);
                        },
                        (error) => {
                            if (onError) onError(error);
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 }
                    );
                },

                updateAllCards: function (coords) {
                    const userCoords = coords || this.getUserCoords();
                    if (!userCoords) return;

                    // Update all service cards
                    const cards = document.querySelectorAll('[data-service-card]');
                    cards.forEach(card => {
                        const lat = parseFloat(card.getAttribute('data-lat'));
                        const lng = parseFloat(card.getAttribute('data-lng'));

                        if (!isNaN(lat) && !isNaN(lng)) {
                            const dist = this.calculateDistance(userCoords.lat, userCoords.lng, lat, lng);
                            const formatted = this.formatDistance(dist);

                            // Update floating badge
                            const floatingBadge = card.querySelector('.service-distance-badge');
                            if (floatingBadge) {
                                const valEl = floatingBadge.querySelector('.distance-value');
                                if (valEl) valEl.textContent = 'تبعد ' + formatted;
                                floatingBadge.classList.remove('hidden');
                                floatingBadge.classList.add('flex');
                            }

                            // Update inline distance bar
                            const inlineBar = card.querySelector('.service-distance-inline');
                            if (inlineBar) {
                                const inlineValEl = inlineBar.querySelector('.distance-inline-value');
                                if (inlineValEl) inlineValEl.textContent = 'تبعد عنك ' + formatted;
                                inlineBar.classList.remove('hidden');
                                inlineBar.classList.add('flex');
                            }
                        }
                    });

                    // Update detail page distance if present
                    const detailDistanceEl = document.getElementById('service-detail-distance');
                    if (detailDistanceEl) {
                        const lat = parseFloat(detailDistanceEl.getAttribute('data-lat'));
                        const lng = parseFloat(detailDistanceEl.getAttribute('data-lng'));
                        if (!isNaN(lat) && !isNaN(lng)) {
                            const dist = this.calculateDistance(userCoords.lat, userCoords.lng, lat, lng);
                            const formatted = this.formatDistance(dist);
                            detailDistanceEl.innerHTML = `<i class="fa-solid fa-person-walking text-emerald-600"></i> تبعد عن موقعك الحالي: <span class="font-extrabold text-emerald-700">${formatted}</span>`;
                            detailDistanceEl.classList.remove('hidden');
                        }
                    }
                }
            };

            // Auto-check on load
            document.addEventListener('DOMContentLoaded', function () {
                const existingCoords = window.WenAlKhedmaGeo.getUserCoords();
                if (existingCoords) {
                    window.WenAlKhedmaGeo.updateAllCards(existingCoords);
                } else if (navigator.permissions && navigator.permissions.query) {
                    navigator.permissions.query({ name: 'geolocation' }).then(function (result) {
                        if (result.state === 'granted') {
                            window.WenAlKhedmaGeo.requestLocation();
                        }
                    }).catch(function () {});
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
