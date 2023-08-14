<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadRequest;
use App\Services\BitrixService;
use Illuminate\Http\JsonResponse;

/**
 * Class LeadController to handle lead related requests
 * @package App\Http\Controllers
 */
class LeadController extends Controller
{
  /**
   * Handles request to render home page
   * @return
   */
  public function index()
  {
    return view('home');
  }

  /**
   * Handles request to render page with form to create lead
   * @return
   */
  public function create()
  {
    return view('create');
  }

  /**
   * Stores lead and contact to Bitrix24
   * @param LeadRequest $request
   * @return JsonResponse object containing message and status code
   */
  public function store(LeadRequest $request)
  {
    $bx = new BitrixService(config('custom.BX_URI'));
    $full_name = explode(' ', $request->name);
    $data = [
      "FULL_NAME" => $request->name,
      "NAME" => $full_name[1] ?? '',
      "SECOND_NAME" => $full_name[2] ?? '',
      "LAST_NAME" => $full_name[0] ?? '',
      "BIRTHDATE" => $request->birth ?? '',
      "PHONE" => $request->phone ?? '',
      "EMAIL" => $request->email ?? '',
      "COMMENTS" => $request->comment ?? ''
    ];

    $data['CONTACT_ID'] = $bx->createContact($data);
    $lead = $bx->createLead($data);

    return new JsonResponse([
      'message' => $lead['message']
    ], $lead['statusCode']);
  }
}
