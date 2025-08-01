<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotImplementedException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'error' => 'Not Implemented',
                'message' => $this->getMessage()
            ], Response::HTTP_NOT_IMPLEMENTED);
        }

        return response()->view('app.errors.not_implemented', [
            'message' => $this->getMessage()
        ], Response::HTTP_NOT_IMPLEMENTED);
    }
}
