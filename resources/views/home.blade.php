@extends('components.home-layout')
@section('title', 'AquaLok Store | Temukan Ikan Hias Favoritmu')
@section('content')
<div class="page-shell">
    <section class="hero" aria-labelledby="hero-title">
        <div class="hero-copy"><span class="eyebrow"><span class="eyebrow-dot"></span> AQUATIC LIVING, MADE EASY</span>
            <h1 id="hero-title">Temukan Ikan Hias <span>Favoritmu 🐟</span></h1>
            <p>Koleksi ikan hias dan perlengkapan aquarium pilihan untuk membuat sudut rumahmu lebih hidup.</p>
            <a class="primary-button" href="#produk">Lihat Produk <span>↗</span></a>
            <div class="hero-note"><span class="note-icon">✓</span> Pilihan produk untuk pecinta ikan hias</div>
        </div>
        <div class="hero-art"><div class="hero-glow"></div><div class="hero-image-wrap"><img src="https://images.unsplash.com/photo-1524704654690-b56c05c78a00?auto=format&fit=crop&w=1000&q=85" alt="Ikan hias berwarna-warni di dalam aquarium" class="hero-image"></div><div class="floating-chip chip-top">✦ Koleksi pilihan</div><div class="floating-chip chip-bottom"><span class="chip-icon">💧</span><span><b>Nuansa aquarium</b><small>Segarkan ruangmu</small></span></div><span class="bubble bubble-one"></span><span class="bubble bubble-two"></span></div>
    </section>
    <section class="benefits" aria-label="Keunggulan AquaLok Store"><div class="benefit"><span class="benefit-icon">🐠</span><div><strong>10+ Pilihan Produk</strong><small>Ikan hias dan pakan</small></div></div><div class="benefit"><span class="benefit-icon">🚚</span><div><strong>Pengiriman Aman</strong><small>Utamakan kondisi ikan</small></div></div><div class="benefit"><span class="benefit-icon">💎</span><div><strong>Produk Berkualitas</strong><small>Pilihan untuk hobimu</small></div></div></section>
    <section id="produk" class="products-section">
        <div class="section-heading"><div><span class="section-kicker">KOLEKSI KAMI</span><h2>Temukan produk <span>pilihan</span></h2><p>Pilih teman baru untuk aquarium kamu.</p></div><span class="product-total" id="visible-count">8 produk</span></div>
        <div class="filter-bar"><label class="search-box"><span>⌕</span><input id="product-search" type="search" placeholder="Cari ikan atau pakan..." aria-label="Cari produk"></label><label class="category-box"><span>☷</span><select id="category-filter" aria-label="Filter kategori"><option value="all">Semua Kategori</option><option value="ikan">Ikan Hias</option><option value="pakan">Pakan Ikan</option></select></label></div>
        <p id="product-status" class="empty-state" role="status">Memuat katalog produk AquaLok Store...</p>
        <div class="product-grid" id="product-grid" aria-live="polite"></div>
        <div id="empty-state" class="empty-state" hidden>Produk tidak ditemukan. Coba kata kunci atau kategori lain.</div>
    </section>
    <section class="bottom-cta"><div><span class="section-kicker">MULAI DARI SINI</span><h2>Aquarium impianmu dimulai di AquaLok.</h2><p>Jelajahi koleksi kami dan temukan pilihan yang cocok untukmu.</p></div><a href="#produk" class="light-button">Jelajahi Produk <span>↗</span></a></section>
</div>
<script>
(() => {
    const apiUrl = @json(url('/api/products'));
    const grid = document.getElementById('product-grid');
    const status = document.getElementById('product-status');
    const empty = document.getElementById('empty-state');
    const counter = document.getElementById('visible-count');
    const currency = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[char]));

    function localImage(product) {
        const slug = String(product.slug || '').toLowerCase();
        const image = slug.includes('cupang') ? 'cupang.jpg'
            : slug.includes('arwana') ? 'arwana.jpg'
            : slug.includes('mas-koki') ? 'mas koki.jpg'
            : slug.includes('guppy') ? 'guppy.jpg'
            : slug.includes('betok') ? 'king betok.jpg'
            : slug.includes('toman') ? 'toman.jpg'
            : slug.includes('akari') ? 'akari.jpg'
            : slug.includes('cacing') ? 'cacing.jpg' : 'cupang.jpg';
        return @json(asset('images/products')) + '/' + encodeURIComponent(image);
    }

    function categoryInfo(product) {
        const categoryName = String(product.category?.name || product.category?.category || '').toLowerCase();
        if (categoryName.includes('pakan')) return { key: 'pakan', label: 'Pakan Ikan' };
        if (categoryName.includes('ikan')) return { key: 'ikan', label: 'Ikan Hias' };
        return { key: 'lainnya', label: product.category?.name || product.category?.category || 'Produk' };
    }

    function makeCard(product) {
        const category = categoryInfo(product);
        const img = product.img_url || localImage(product);
        const stock = Number(product.stock || 0);
        const stockLabel = stock > 0 ? `Stok: ${stock}` : 'Stok habis';
        const article = document.createElement('article');
        article.className = 'product-card';
        article.dataset.name = String(product.name || '').toLowerCase();
        article.dataset.category = category.key;
        article.innerHTML = `
            <div class="product-image-wrap">
                <img class="product-image" src="${escapeHtml(img)}" alt="${escapeHtml(product.name)}" loading="lazy">
                <span class="category-pill">${escapeHtml(category.label)}</span>
                <span class="product-tag">${escapeHtml(stockLabel)}</span>
            </div>
            <div class="product-info">
                <div class="product-title-row"><h3>${escapeHtml(product.name)}</h3></div>
                <div class="rating"><span>✓</span> ${stock > 0 ? 'Tersedia' : 'Tidak tersedia'} <span class="rating-note">· ${stock > 0 ? 'Siap dipilih' : 'Hubungi toko'}</span></div>
                <div class="product-bottom">
                    <strong class="product-price">${currency.format(Number(product.price || 0))}</strong>
                    <button class="add-button" type="button" aria-label="Tambah ke keranjang">＋</button>
                </div>
                <button class="add-full" type="button" ${stock <= 0 ? 'disabled' : ''}>+ Keranjang</button>
            </div>`;

        const addButtons = article.querySelectorAll('.add-button, .add-full');
        addButtons.forEach((button) => {
            if (stock <= 0) {
                button.disabled = true;
                button.title = 'Stok produk habis';
            }
            button.addEventListener('click', () => {
                if (typeof window.addToCart === 'function') {
                    window.addToCart(Number(product.id), String(product.name), Number(product.price));
                }
            });
        });
        article.querySelector('.product-image').addEventListener('error', (event) => {
            event.currentTarget.onerror = null;
            event.currentTarget.src = @json(asset('images/products/cupang.jpg'));
        });
        return article;
    }

    function filterProducts() {
        const query = (document.getElementById('product-search')?.value || '').trim().toLowerCase();
        const category = document.getElementById('category-filter')?.value || 'all';
        const cards = [...grid.querySelectorAll('.product-card')];
        let shown = 0;
        cards.forEach((card) => {
            const matchesName = card.dataset.name.includes(query);
            const matchesCategory = category === 'all' || card.dataset.category === category;
            card.hidden = !(matchesName && matchesCategory);
            if (!card.hidden) shown++;
        });
        if (counter) counter.textContent = `${shown} produk`;
        if (empty) empty.hidden = shown > 0;
    }

    document.getElementById('product-search')?.addEventListener('input', filterProducts);
    document.getElementById('category-filter')?.addEventListener('change', filterProducts);

    fetch(apiUrl, { headers: { 'Accept': 'application/json' } })
        .then((response) => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then((payload) => {
            const products = Array.isArray(payload.data) ? payload.data : [];
            grid.replaceChildren(...products.map(makeCard));
            if (status) status.hidden = true;
            if (products.length === 0 && status) {
                status.textContent = 'Belum ada produk di database. Jalankan migrasi dan seeder terlebih dahulu.';
                status.hidden = false;
            }
            filterProducts();
        })
        .catch((error) => {
            console.error('Gagal memuat katalog API:', error);
            if (status) {
                status.textContent = 'Katalog belum dapat dimuat dari REST API. Pastikan server aktif dan database sudah dimigrasikan serta di-seed.';
                status.hidden = false;
            }
            if (counter) counter.textContent = 'API belum terhubung';
        });
})();
</script>

@endsection
