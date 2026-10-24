<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Menampilkan daftar, pencarian, dan filter produk.
     */
    public function index(Request $request)
    {
        // 1. Validasi parameter pencarian dan filter.
        $validated = $request->validate([
            'search' => 'sometimes|nullable|string|max:100',
            'category_id' => 'sometimes|integer|exists:categories,category_id',
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0',
            'stock_status' => 'sometimes|in:available,out_of_stock',
        ]);

        // 2. Periksa rentang harga.
        if (
            $request->filled('min_price') &&
            $request->filled('max_price') &&
            (float) $validated['min_price'] >
            (float) $validated['max_price']
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Harga minimum tidak boleh melebihi harga maksimum.',
            ], 422);
        }

        // 3. Mulai query produk beserta kategorinya.
        $query = Product::with('category');

        // 4. Cari berdasarkan nama, deskripsi, atau slug.
        if ($request->filled('search')) {
            $search = trim($validated['search']);

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // 5. Filter berdasarkan kategori.
        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $validated['category_id']
            );
        }

        // 6. Filter harga minimum.
        if ($request->filled('min_price')) {
            $query->where(
                'price',
                '>=',
                $validated['min_price']
            );
        }

        // 7. Filter harga maksimum.
        if ($request->filled('max_price')) {
            $query->where(
                'price',
                '<=',
                $validated['max_price']
            );
        }

        // 8. Filter ketersediaan stok.
        if ($request->filled('stock_status')) {
            if ($validated['stock_status'] === 'available') {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', '=', 0);
            }
        }

        // 9. Urutkan dan tampilkan 20 produk per halaman.
        $products = $query->latest()
            ->paginate(20)
            ->withQueryString();

        // 10. Kembalikan respons JSON.
        return ProductResource::collection($products)->additional([
            'success' => true,
            'message' => 'Daftar produk AquaLok Store.',
        ]);
    }

    /**
     * POST /api/products
     * Menambahkan produk baru.
     */
    public function store(StoreProductRequest $request)
    {
        // Validasi dilakukan oleh StoreProductRequest.
        $validated = $request->validated();

        // Upload gambar jika ada.
        $imagePath = null;

        if ($request->hasFile('img')) {
            $imagePath = $request->file('img')
                ->store('products', 'public');
        }

        // Simpan produk ke database.
        $product = Product::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'img' => $imagePath,
        ]);

        // Kembalikan produk yang berhasil dibuat.
        return (new ProductResource($product->load('category')))
            ->additional([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/products/{product}
     * Menampilkan detail produk.
     */
    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);

        return (new ProductResource($product))->additional([
            'success' => true,
            'message' => 'Detail produk.',
        ]);
    }

    /**
     * PUT/PATCH /api/products/{product}
     * Memperbarui produk.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);

        // Validasi dilakukan oleh UpdateProductRequest.
        $validated = $request->validated();

        // Ganti gambar jika ada gambar baru.
        if ($request->hasFile('img')) {
            if ($product->img) {
                Storage::disk('public')->delete($product->img);
            }

            $validated['img'] = $request->file('img')
                ->store('products', 'public');
        } elseif (
            ($validated['remove_img'] ?? false) &&
            $product->img
        ) {
            // Hapus gambar lama jika diminta.
            Storage::disk('public')->delete($product->img);
            $validated['img'] = null;
        }

        // remove_img hanya instruksi, bukan kolom produk.
        unset($validated['remove_img']);

        // Perbarui slug jika nama produk berubah.
        if (array_key_exists('name', $validated)) {
            $validated['slug'] = $this->uniqueSlug(
                $validated['name'],
                $product->product_id
            );
        }

        // Simpan perubahan.
        $product->update($validated);

        return (new ProductResource(
            $product->fresh()->load('category')
        ))->additional([
            'success' => true,
            'message' => 'Produk berhasil diperbarui.',
        ]);
    }

    /**
     * DELETE /api/products/{product}
     * Menghapus produk.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        // Hapus gambar produk dari penyimpanan.
        if ($product->img) {
            Storage::disk('public')->delete($product->img);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus.',
        ], 200);
    }

    /**
     * Membuat slug produk yang unik.
     */
    private function uniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (
            Product::where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) => $query->where(
                        'product_id',
                        '!=',
                        $ignoreId
                    )
                )
                ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}