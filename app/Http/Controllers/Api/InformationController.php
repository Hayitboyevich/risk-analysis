<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PassportInfoRequest;
use App\Services\HistoryService;
use App\Services\UserService;
use Illuminate\Support\Facades\Http;

class InformationController extends BaseController
{

    public function __construct(
        protected UserService $userService,
    )
    {
        parent::__construct();
    }
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

    public function passportInfo(PassportInfoRequest $request)
    {
        try {
            $data = $this->userService->getInfo($request->pinfl, $request->birth_date);
            $meta = [
                'pinfl' => $data['current_pinpp'],
                'name' => $data['namelat'],
                'surname' => $data['surnamelat'],
                'middle_name' => $data['patronymlat'],
                'image' => $data['photo'],
                'passport_number' => $data['current_document']
            ];
            return $this->sendSuccess($meta, 'Passport Information Get Successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Xatolik aniqlandi', $exception->getMessage());
        }
    }
}
