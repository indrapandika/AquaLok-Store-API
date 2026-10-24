<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AquaLok Store | Toko Ikan Hias')</title>
    <meta name="description" content="AquaLok Store menyediakan ikan hias dan pakan berkualitas untuk aquarium Anda.">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/aqualok.css') }}">
</head>
<body class="aqua-body">
    <header class="site-header"><nav class="navbar" aria-label="Navigasi utama">
        <a href="{{ route('home') }}" class="brand"><span class="brand-mark">🐟</span><span>AquaLok <b>Store</b></span></a>
        <button class="mobile-menu" type="button" aria-label="Buka menu" onclick="document.querySelector('.nav-links').classList.toggle('nav-open')">☰</button>
        <div class="nav-links">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
            <a class="nav-link" href="{{ route('home') }}#produk">Produk</a>
            <a class="nav-link" href="{{ route('home') }}#keranjang" onclick="return showCartMessage(event)">🛒 Keranjang <span id="cart-count" class="cart-count">0</span></a>
            @auth
                <a class="nav-cta" href="{{ route('dashboard') }}">Dashboard</a>
            @else
                <a class="nav-cta" href="{{ route('login') }}">Masuk</a>
            @endauth
        </div>
    </nav></header>
    <main>@yield('content')</main>
    <footer class="site-footer"><div class="footer-inner"><a href="{{ route('home') }}" class="brand footer-brand"><span class="brand-mark">🐟</span><span>AquaLok <b>Store</b></span></a><p>Koleksi ikan hias untuk aquarium yang lebih hidup.</p><span>© {{ date('Y') }} AquaLok Store</span></div></footer>
    <div id="toast" class="toast" role="status" aria-live="polite"></div>
    <script>
    function getCart(){ try{return JSON.parse(localStorage.getItem('aqualok-cart')||'[]')}catch(e){return []} }
    function updateCartCount(){const cart=getCart();const count=cart.reduce((n,p)=>n+p.qty,0);document.querySelectorAll('#cart-count').forEach(el=>el.textContent=count)}
    function showToast(message){const el=document.getElementById('toast');if(!el)return;el.textContent=message;el.classList.add('toast-show');clearTimeout(window.aquaToastTimer);window.aquaToastTimer=setTimeout(()=>el.classList.remove('toast-show'),2400)}
    function addToCart(id,name,price){let cart=getCart();const found=cart.find(p=>p.id===id);if(found)found.qty++;else cart.push({id,name,price,qty:1});localStorage.setItem('aqualok-cart',JSON.stringify(cart));updateCartCount();showToast(name+' ditambahkan ke keranjang 🛒')}
    function showCartMessage(event){event.preventDefault();const cart=getCart();if(!cart.length){showToast('Keranjang kamu masih kosong. Yuk pilih ikan favoritmu!');return false}const summary=cart.map(p=>p.name+' × '+p.qty).join(', ');showToast('Isi keranjang: '+summary);return false}
    document.addEventListener('DOMContentLoaded',()=>{updateCartCount()});
    </script>
</body></html>