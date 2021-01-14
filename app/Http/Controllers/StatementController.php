<?php


namespace App\Http\Controllers;


use App\Components\Pdf\StatementPdf;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StatementController extends Controller
{
    public function download(Customer $customer, Request $request)
    {
        $path = CreatePdf::dispatchNow((new StatementPdf($customer)), $customer);
        $disk = config('filesystems.default');
        $content = Storage::disk($disk)->get($path);
        $response = ['data' => base64_encode($content)];

        return response()->json($response);
    }
}