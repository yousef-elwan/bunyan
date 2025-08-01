<?php

namespace App\Http\Middleware;

use App\Data\DynamicFilterData;
use App\Enums\PaginationFormateEnum;
use App\Http\Resources\Web\PagesMetaResource;
use App\Models\Lang;
use App\Repositories\Contracts\BusinessSettingRepositoryInterface;
use App\Repositories\Contracts\PagesMetaRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetWebConfigMiddleware
{
    public function __construct(
        private BusinessSettingRepositoryInterface $bookingRepo,
        private PagesMetaRepositoryInterface $pagesMetaRepositoryInterface,
    ) {}
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $web_config = [];
        $language = [];

        try {
            if (Schema::hasTable('business_settings')) {

                $web_config = Cache::rememberForever('business_settings', function () {
                    return $this->bookingRepo->all();
                });
            }
            if (Schema::hasTable('pages_meta')) {

                $pages_meta = Cache::rememberForever('pages_meta', function () {
                    $result = $this->pagesMetaRepositoryInterface->getList(new DynamicFilterData(
                        paginationFormate: PaginationFormateEnum::none,
                        extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                            $query->with(['translations']);
                        },
                    ));
                    $data = $result['data'];
                    $data = collect($data)->map(function ($type) {
                        return (new PagesMetaResource($type))->toArray(request());
                    })->groupBy('name')->mapWithKeys(function ($value, $key) {
                        return [$key => collect($value)->values()->toArray()[0]];
                    });
                    return $data;
                });
            }

            Config::set('app.web_config', $web_config);
            Config::set('app.pages_meta', $pages_meta);

            View::share([
                'web_config' => $web_config,
                'pages_meta' => $pages_meta,
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }
        try {
            $language = Lang::active()->get();
        } catch (\Throwable $th) {
        }
        $web_config['company_name']="Bunyan";
        View::share([
            'web_config' => $web_config,
            'language' => $language,
            'assetsUrl' =>  Storage::disk('asset')->url(''),
            'defaultLang' => getDefaultLanguage()
        ]);

        return $next($request);
    }
}
