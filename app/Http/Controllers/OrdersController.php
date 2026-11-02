<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    // GET /orders
    public function index(Request $request)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        return $this->paginated(Order::query(), 'Daftar pesanan', $format);
    }

    // GET /orders/{id}
    public function show(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        $order = Order::find($id);
        if (!$order) {
            return $this->notFound('Pesanan', $id, $format);
        }

        return $this->respond([
            'message' => 'Detail pesanan',
            'data'    => $order->toArray(),
        ], 200, $format);
    }

    // POST /orders
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

        $order = Order::create($request->only((new Order)->getFillable()));

        return $this->respond([
            'message' => 'Pesanan berhasil dibuat',
            'data'    => $order->fresh()->toArray(),
        ], 201, $format);
    }

    // PUT /orders/{id}
    public function update(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        if (!$this->isJsonContent($request)) {
            return response('Unsupported Media Type', 415);
        }

        $order = Order::find($id);
        if (!$order) {
            return $this->notFound('Pesanan', $id, $format);
        }

        if ($error = $this->validationFailed($request, $this->rules($id), $format)) {
            return $error;
        }

        $order->update($request->only($order->getFillable()));

        return $this->respond([
            'message' => 'Pesanan berhasil diperbarui',
            'data'    => $order->fresh()->toArray(),
        ], 200, $format);
    }

    // DELETE /orders/{id}
    public function destroy(Request $request, $id)
    {
        $format = $this->getFormat($request);
        if (!$format) {
            return response('Not Acceptable!', 406);
        }

        $order = Order::find($id);
        if (!$order) {
            return $this->notFound('Pesanan', $id, $format);
        }

        $order->delete();

        return $this->respond([
            'message' => 'Pesanan berhasil dihapus',
        ], 200, $format);
    }

    // Aturan validasi pesanan
    private function rules($id = null)
    {
        return [
            'customer_name' => 'required|string|min:3|max:100',
            'product_name'  => 'required|string|min:3|max:100',
            'quantity'      => 'required|integer|min:1',
            'total_price'   => 'required|integer|min:0',
            'order_date'    => 'required|date',
            'status'        => 'nullable|in:pending,paid,shipped,cancelled',
        ];
    }
}
