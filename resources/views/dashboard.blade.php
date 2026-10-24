<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-sky-900 leading-tight">Dashboard AquaLok Store</h2></x-slot>
    <div class="py-12" style="background:linear-gradient(135deg,#f0f9ff,#fff,#eef2ff);min-height:65vh">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"><div class="overflow-hidden rounded-2xl border border-sky-100 bg-white shadow-sm"><div class="p-8 text-slate-700">
            <div class="mb-4 text-4xl">🐟</div><h1 class="mb-2 text-2xl font-extrabold text-sky-900">Selamat datang di AquaLok Store!</h1><p class="mb-6 text-slate-500">Halo, {{ Auth::user()->name }}. Kamu berhasil masuk. Halaman ini menjadi area akun untuk pengembangan fitur toko selanjutnya.</p><a href="{{ route('home') }}#produk" class="inline-flex items-center rounded-xl bg-sky-600 px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-sky-700">Jelajahi Produk <span class="ml-3">↗</span></a>
        </div></div></div>
    </div>
</x-app-layout>