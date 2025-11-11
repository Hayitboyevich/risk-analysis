<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;

class InformationController extends BaseController
{
    public function organization()
    {
        try {
            $cadNumber = request('stir');
            $response = Http::withBasicAuth('orgapi-v1', '*@org-apiv_*ali')
                ->get('https://api-sert.mc.uz/api/orginfoapi/' . $cadNumber);


            if ($response->successful()) {

                return response()->json($response->json());

            }
            return $this->sendError('Xatolik yuz berdi');
        } catch (\Exception $exception) {
            return $this->sendError("xatolik aniqlandi", $exception->getMessage());
        }
    }
}
