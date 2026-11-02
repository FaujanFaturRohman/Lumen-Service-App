<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    // GET /categories
    public function index(Request $request)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        return $this->paginated(Category::query(), 'Daftar kategori', $format);
    }

    // GET /categories/{id}
    public function show(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        $category = Category::find($id);
        if (!$category) {
            return $this->notFound('Kategori', $id, $format);
        }

        return $this->respond([
            'message' => 'Detail kategori',
            'data'    => $category->toArray(),
        ], 200, $format);
    }

    // POST /categories
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

        $category = Category::create($request->only((new Category)->getFillable()));

        return $this->respond([
            'message' => 'Kategori berhasil dibuat',
            'data'    => $category->fresh()->toArray(),
        ], 201, $format);
    }

    // PUT /categories/{id}
    public function update(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        if (!$this->isJsonContent($request)) {
            return response('Unsupported Media Type', 415);
        }

        $category = Category::find($id);
        if (!$category) {
            return $this->notFound('Kategori', $id, $format);
        }

        if ($error = $this->validationFailed($request, $this->rules($id), $format)) {
            return $error;
        }

        $category->update($request->only($category->getFillable()));

        return $this->respond([
            'message' => 'Kategori berhasil diperbarui',
            'data'    => $category->fresh()->toArray(),
        ], 200, $format);
    }

    // DELETE /categories/{id}
    public function destroy(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        $category = Category::find($id);
        if (!$category) {
            return $this->notFound('Kategori', $id, $format);
        }

        $category->delete();

        return $this->respond([
            'message' => 'Kategori berhasil dihapus',
        ], 200, $format);
    }

    // Aturan validasi kategori
    private function rules($id = null)
    {
        return [
            'name' => 'required|string|min:3|max:100',
        ];
    }
}
