<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Services\AmoCRM\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function authorize(): \Illuminate\Http\RedirectResponse
    {
        if($this->authService->authorize()) {
            return redirect()->route('contact.create');
        }
    }
}
