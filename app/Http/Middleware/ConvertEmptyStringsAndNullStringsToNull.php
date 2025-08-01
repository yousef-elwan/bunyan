<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConvertEmptyStringsAndNullStringsToNull
{
    public function handle(Request $request, Closure $next)
    {
        $request->merge(
            $this->convertEmptyStringsToNull($request->all())
        );

        return $next($request);
    }

    protected function convertEmptyStringsToNull(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->convertEmptyStringsToNull($value);
            } elseif ($value === '' || $value === 'null') {
                $data[$key] = null;
            }
        }

        return $data;
    }
}
