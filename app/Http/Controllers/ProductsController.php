<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    // GET /products
    public function index(Request $request)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        return $this->paginated(Product::query(), 'Daftar produk', $format);
    }

    // GET /products/{id}
    public function show(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        $product = Product::find($id);
        if (!$product) {
            return $this->notFound('Produk', $id, $format);
        }

        return $this->respond([
            'message' => 'Detail produk',
            'data'    => $product->toArray(),
        ], 200, $format);
    }

    // POST /products
    public function store(Request $request)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        if (!$this->isJsonContent($request)) {
            return response('Unsupported Media Type', 415);
        }

        if ($error = $this->validationFailed($request, $this->rules(), $format)) {
            return $error;
        }

        $product = Product::create($request->only((new Product)->getFillable()));

        return $this->respond([
            'message' => 'Produk berhasil dibuat',
            'data'    => $product->fresh()->toArray(),
        ], 201, $format);
    }

    // PUT /products/{id}
    public function update(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        if (!$this->isJsonContent($request)) {
            return response('Unsupported Media Type', 415);
        }

        $product = Product::find($id);
        if (!$product) {
            return $this->notFound('Produk', $id, $format);
        }

        if ($error = $this->validationFailed($request, $this->rules($id), $format)) {
            return $error;
        }

        $product->update($request->only($product->getFillable()));

        return $this->respond([
            'message' => 'Produk berhasil diperbarui',
            'data'    => $product->fresh()->toArray(),
        ], 200, $format);
    }

    // DELETE /products/{id}
    public function destroy(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        $product = Product::find($id);
        if (!$product) {
            return $this->notFound('Produk', $id, $format);
        }

        $product->delete();

        return $this->respond([
            'message' => 'Produk berhasil dihapus',
        ], 200, $format);
    }

    // Aturan validasi produk
    private function rules($id = null)
    {
        return [
            'sku'          => 'required|string|max:30|unique:products,sku' . ($id ? ',' . $id : ''),
            'name'         => 'required|string|min:5|max:100',
            'category'     => 'required|in:Makanan,Minuman,Elektronik,Pakaian',
            'description'  => 'nullable|string|min:10',
            'price'        => 'required|integer|min:0',
            'stock'        => 'required|integer|min:0',
            'weight_gram'  => 'required|integer|min:1',
            'is_available' => 'boolean',
        ];
    }
}
