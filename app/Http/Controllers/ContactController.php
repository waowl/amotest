<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Services\AmoCRM\ContactService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * @var ContactService
     */
    private $service;

    public function __construct(ContactService $service)
    {
        $this->service = $service;
    }

    public function create(){
        return view('form');
    }

    public function store(ContactCreateRequest $request){

        if ( $this->service->create($request)){
            return redirect()->back()->with('success', 'Информация сохранена.');
        }

        return redirect()->back()->with('error', 'Произошла ошибка.');
    }
}
