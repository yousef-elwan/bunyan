<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Report\UpdateReportStatusRequest;
use App\Http\Resources\ReportResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\Lang;
use App\Models\Property\PropertyReport;
use App\Models\ReportStatus\ReportStatus;
use App\Models\ReportType\ReportType;
use App\Models\User;
use App\Repositories\Contracts\PropertyReportRepositoryInterface;
use App\Services\AutoFIlterAndSortService;

class ReportController extends Controller
{

    public function __construct(
        protected  PropertyReportRepositoryInterface $repo
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $langs = Lang::active()->get();
        $report_statuses = ReportStatus::with('translations')->get();
        $report_types = ReportType::with('translations')->get();
        $owners = User::whereHas('properties')->get();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.reports'), 'url' => null],
        ];

        return view('dashboard.pages.report.list', compact('langs', 'user', 'owners', 'report_statuses', 'report_types','breadcrumbs'));
    }

    // public function search(Request $request)
    // {
    //     $query = Report::query();

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->input('status'));
    //     }

    //     $reports = $query->with(['property.translations', 'reporter:id,first_name,last_name,email'])
    //         ->latest()
    //         ->paginate($request->input('perPage', 10));

    //     return ReportResource::collection($reports);
    // }

    public function search(Request $request)
    {
        $result = AutoFIlterAndSortService::dynamicSearchFromRequest(
            getFunction: [$this->repo, 'getList'],
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {
                $query->with([
                    'reporter',
                    'type',
                    'status',
                    'property.owner',
                ]);
            },
        );
        // $data = collect($result['data'])->map(function ($orientation) {
        //     return (new OrientationResource($orientation))->toArray(request());
        // })->toArray();

        return successResponse(
            '',
            data: $result['data'],
            pagination: $result['pagination']
        );
    }


    public function updateStatus(UpdateReportStatusRequest  $request, PropertyReport $report)
    {

        try {
            $report->update([
                'report_status_id' => $request->validated('report_status_id'),
            ]);
            return successResponse(
                data: ['new_status' => $report->status],
                message: __('dashboard.reports.status_updated_successfully')
            );
        } catch (\Exception $e) {
            Log::error('Report Status Update Error: ' . $e->getMessage());
            return errorResponse(
                message: __('dashboard.reports.error_updating_status'),
            );
        }
    }
}
