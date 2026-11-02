<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    // GET /customers
    public function index(Request $request)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        return $this->paginated(Customer::query(), 'Daftar pelanggan', $format);
    }

    // GET /customers/{id}
    public function show(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        $customer = Customer::find($id);
        if (!$customer) {
            return $this->notFound('Pelanggan', $id, $format);
        }

        return $this->respond([
            'message' => 'Detail pelanggan',
            'data'    => $customer->toArray(),
        ], 200, $format);
    }

    // POST /customers
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

        $customer = Customer::create($request->only((new Customer)->getFillable()));

        return $this->respond([
            'message' => 'Pelanggan berhasil dibuat',
            'data'    => $customer->fresh()->toArray(),
        ], 201, $format);
    }

    // PUT /customers/{id}
    public function update(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        if (!$this->isJsonContent($request)) {
            return response('Unsupported Media Type', 415);
        }

        $customer = Customer::find($id);
        if (!$customer) {
            return $this->notFound('Pelanggan', $id, $format);
        }

        if ($error = $this->validationFailed($request, $this->rules($id), $format)) {
            return $error;
        }

        $customer->update($request->only($customer->getFillable()));

        return $this->respond([
            'message' => 'Pelanggan berhasil diperbarui',
            'data'    => $customer->fresh()->toArray(),
        ], 200, $format);
    }

    // DELETE /customers/{id}
    public function destroy(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        $customer = Customer::find($id);
        if (!$customer) {
            return $this->notFound('Pelanggan', $id, $format);
        }

        $customer->delete();

        return $this->respond([
            'message' => 'Pelanggan berhasil dihapus',
        ], 200, $format);
    }

    // Aturan validasi pelanggan
    private function rules($id = null)
    {
        return [
            'name'  => 'required|string|min:3|max:100',
            'phone' => 'nullable|string|max:20',
        ];
    }
}
