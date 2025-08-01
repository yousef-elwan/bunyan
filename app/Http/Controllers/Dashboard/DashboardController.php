<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\ReportResource;
use App\Http\Resources\Web\PropertyResource;
use App\Models\Lang;
use App\Models\Property\Property;
use App\Models\Property\PropertyReport;

use App\Models\Appointment;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\ReportStatus\ReportStatus;
use App\Models\User;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\PropertyReportRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // ... [الـ constructor الخاص بك لا يحتاج إلى تغيير] ...
    public function __construct(
        protected PropertyRepositoryInterface $propertyRepositoryInterface,
        protected PropertyReportRepositoryInterface $propertyReportRepositoryInterface,
        // ... [بقية الـ repositories]
    ) {}


    public function home(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // ===============================================
        // 1. بناء الاستعلامات الأساسية مع فلتر المستخدم
        // ===============================================
        $userQuery = User::query()->where('type', 'user');
        $basePropertiesQuery = Property::query();
        $baseReportsQuery = PropertyReport::query();
        $baseMessagesQuery = Message::query();
        $baseAppointmentsQuery = Appointment::query();

        if (!$user->is_admin) {
            $basePropertiesQuery->where('user_id', $user->id);

            $userPropertyIds = (clone $basePropertiesQuery)->pluck('id');
            $baseReportsQuery->whereIn('property_id', $userPropertyIds);

            // فلترة الرسائل والمواعيد للمستخدم الحالي
            $baseMessagesQuery->where('recipient_id', $user->id);
            $baseAppointmentsQuery->where('user_id', $user->id);
        }

        // ===============================================
        // 2. حساب بيانات "البطاقات الذهبية الخمسة"
        // ===============================================

        $unreadMessagesCount = Conversation::query()
            // 1. ضم جدول المشاركين للوصول إلى المستخدم وتاريخ القراءة
            ->join('conversation_participants', 'conversations.id', '=', 'conversation_participants.conversation_id')
            // 2. ضم جدول آخر رسالة للوصول إلى تاريخها
            ->join('messages', 'conversations.last_message_id', '=', 'messages.id')
            // 3. الشرط الأول: نريد فقط المحادثات الخاصة بالمستخدم الحالي
            ->where('conversation_participants.user_id', $userId)
            // 4. الشرط الثاني (الأهم): حيث تاريخ آخر رسالة أحدث من تاريخ قراءة المستخدم لهذه المحادثة
            ->whereColumn('messages.created_at', '>', 'conversation_participants.read_at')
            // 5. عد النتائج
            ->count();

        $stats = (!$user->is_admin) ? [
            'total_properties' => [
                'title' => __('dashboard/dashboard.stats.total_properties'),
                'metric' => (clone $basePropertiesQuery)->count(),
                'trend_text' => __('dashboard/dashboard.stats.trend.property_available'),
                'color' => 'blue',
                'icon' => 'fas fa-home'
            ],
            // 'monthly_sales' => [
            //     'title' => __('dashboard/dashboard.monthly_sales'),
            //     'metric' => (clone $basePropertiesQuery)->where('report_status_id', 'sold')->whereMonth('updated_at', now()->month)->count(),
            //     'trend_text' => __('dashboard/dashboard.monthly_sales_trend'),
            //     'color' => 'green',
            //     'icon' => 'fas fa-handshake'
            // ],
            'unread_messages' => [
                'title' => __('dashboard/dashboard.stats.unread_messages'),
                'metric' => $unreadMessagesCount,
                'trend_text' => __('dashboard/dashboard.stats.trend.reply_required'),
                'color' => 'purple',
                'icon' => 'fas fa-envelope'
            ],
            'today_appointments' => [
                'title' => __('dashboard/dashboard.stats.today_appointments'),
                'metric' => (clone $baseAppointmentsQuery)->whereDate('date', today())->count(),
                'trend_text' => __('dashboard/dashboard.stats.trend.important'),
                'color' => 'yellow',
                'icon' => 'fas fa-calendar-check'
            ],
        ] : [
            'total_properties' => [
                'title' => __('dashboard/dashboard.stats.total_properties'),
                'metric' => (clone $basePropertiesQuery)->count(),
                'trend_text' => __('dashboard/dashboard.stats.trend.property_available'),
                'color' => 'blue',
                'icon' => 'fas fa-home'
            ],
            'total_users' => [
                'title' => __('dashboard/dashboard.stats.total_users'),
                'metric' => (clone $userQuery)->count(),
                'trend_text' => __('dashboard/dashboard.stats.trend.user'),
                'color' => 'green',
                'icon' => 'fas fa-user'
            ],
            'unread_messages' => [
                'title' => __('dashboard/dashboard.stats.unread_messages'),
                'metric' => $unreadMessagesCount,
                'trend_text' => __('dashboard/dashboard.stats.trend.reply_required'),
                'color' => 'purple',
                'icon' => 'fas fa-envelope'
            ],
            'pending_reports' => [
                'title' => __('dashboard/dashboard.stats.pending_reports'),
                'metric' => (clone $baseReportsQuery)->where('report_status_id', 'pending')->count(),
                'trend_text' => __('dashboard/dashboard.stats.trend.action_required'),
                'color' => 'red',
                'icon' => 'fas fa-flag'
            ],
        ];


        // ===============================================
        // 3. حساب بيانات أشرطة التقدم (Progress Bars)
        // ===============================================
        $totalPropertiesCount = $stats['total_properties']['metric'] > 0 ? $stats['total_properties']['metric'] : 1;

        $propertyTypesData = (clone $basePropertiesQuery)
            ->join('types', 'property.type_id', '=', 'types.id') // تأكد من اسم الجدول `types`
            ->join('types_translations', 'types.id', '=', 'types_translations.type_id') // تأكد من اسم الجدول `types_translations`
            ->where('types_translations.locale', app()->getLocale())
            ->selectRaw('types.id as id, types_translations.name as type_name, COUNT(property.id) as count')
            ->groupBy('type_name')
            ->orderBy('count', 'desc')
            ->get()
            ->map(fn($item) => tap($item, fn($i) => $i->percentage = round(($i->count / $totalPropertiesCount) * 100)));

        $totalReportsCount = (clone $baseReportsQuery)->count() > 0 ? (clone $baseReportsQuery)->count() : 1;

        $reportStatusesData = (clone $baseReportsQuery)
            ->selectRaw('reports.id as id, reports.report_status_id as status, COUNT(*) as count')
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get()
            ->map(fn($item) => tap($item, fn($i) => $i->percentage = round(($i->count / $totalReportsCount) * 100)));


        // ===============================================
        // 4. جلب أحدث البيانات للجداول والإشعارات
        // ===============================================
        $latestProperties = (clone $basePropertiesQuery)
            ->with(['translations', 'city.translations', 'owner'])
            ->latest()
            ->take(5)
            ->get();

        $latestReports = (clone $baseReportsQuery)
            ->with([
                'reporter',
                'type',
                'status',
                'property.owner',
                'property.translations',
                // 'reporter:id,first_name,last_name,email',
                'property.type.translations',
                'property.city.translations',
                'property.category.translations',
                'property.floor.translations',
            ])
            ->latest()
            ->take(5)
            ->get();

        // جلب المواعيد القادمة الحقيقية
        $upcomingAppointments = (clone $baseAppointmentsQuery)
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();


        // ===============================================
        // 5. إرسال كل البيانات إلى الواجهة
        // ===============================================
        $langs = Lang::active()->get();

        // return response()->json(ReportResource::collection($latestReports)->toArray($request));
        return view('dashboard.pages.home.page', [
            'user' => $user,
            'langs' => $langs,

            // بيانات البطاقات العلوية
            'stats' => $stats,

            // بيانات الجداول
            'latestProperties' => PropertyResource::collection($latestProperties)->toArray($request),
            'latestReports' => ReportResource::collection($latestReports)->toArray($request),
            'report_statuses' => ReportStatus::all(['id']),
            // بيانات الشريط الجانبي
            'upcomingAppointments' => $upcomingAppointments, // الآن أصبحت بيانات حقيقية
            'propertyTypesData' => $propertyTypesData,
            'reportStatusesData' => $reportStatusesData,
        ]);
    }
}
