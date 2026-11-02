<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    // Jumlah data per halaman untuk semua endpoint index
    protected const PER_PAGE = 2;

    // Mengembalikan 'application/json' / 'application/xml', atau null jika Accept tidak valid
    protected function getFormat(Request $request)
    {
        $accept = $request->header('Accept');

        if ($accept === 'application/json' || $accept === 'application/xml') {
            return $accept;
        }
        return null;
    }

    // Hanya menerima Content-Type: application/json
    protected function isJsonContent(Request $request)
    {
        return stripos((string) $request->header('Content-Type'), 'application/json') === 0;
    }

    // Kirim response sesuai format Accept (JSON / XML)
    protected function respond(array $data, $status, $format)
    {
        if ($format === 'application/xml') {
            $xml = new \SimpleXMLElement('<response/>');
            $this->arrayToXml($data, $xml);

            return response($xml->asXML(), $status)
                ->header('Content-Type', 'application/xml');
        }

        return response()->json($data, $status);
    }

    // Ubah array (bisa bersarang) menjadi elemen XML
    protected function arrayToXml(array $data, \SimpleXMLElement $xml)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item';          // tag XML tidak boleh berupa angka
            }

            if (is_array($value)) {
                $child = $xml->addChild($key);
                $this->arrayToXml($value, $child);
            } else {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                $xml->addChild($key, htmlspecialchars((string) $value));
            }
        }
    }

    // Response 404 untuk data yang tidak ditemukan
    protected function notFound($label, $id, $format)
    {
        return $this->respond([
            'message' => "{$label} dengan id {$id} tidak ditemukan",
        ], 404, $format);
    }

    // Response index dengan pagination
    protected function paginated($query, $message, $format)
    {
        $result = $query->orderBy('id', 'DESC')->paginate(static::PER_PAGE)->toArray();

        return $this->respond([
            'message'     => $message,
            'total_count' => $result['total'],
            'limit'       => $result['per_page'],
            'pagination'  => [
                'next_page'    => $result['next_page_url'],
                'current_page' => $result['current_page'],
            ],
            'data'        => $result['data'],
        ], 200, $format);
    }

    // Validasi manual: mengembalikan response 400 jika gagal, atau null jika lolos
    protected function validationFailed(Request $request, array $rules, $format)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respond([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()->toArray(),
            ], 400, $format);
        }

        return null;
    }
}
