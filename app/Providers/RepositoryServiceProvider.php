<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\BusinessSettingRepositoryInterface::class,
            \App\Repositories\Eloquent\BusinessSettingRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\TypeRepositoryInterface::class,
            \App\Repositories\Eloquent\TypeRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\AmenityRepositoryInterface::class,
            \App\Repositories\Eloquent\AmenityRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\CategoryRepositoryInterface::class,
            \App\Repositories\Eloquent\CategoryRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PropertyRepositoryInterface::class,
            \App\Repositories\Eloquent\PropertyRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\CustomAttributeRepositoryInterface::class,
            \App\Repositories\Eloquent\CustomAttributeRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PropertyConditionRepositoryInterface::class,
            \App\Repositories\Eloquent\PropertyConditionRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\CurrencyRepositoryInterface::class,
            \App\Repositories\Eloquent\CurrencyRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\FavoriteRepositoryInterface::class,
            \App\Repositories\Eloquent\FavoriteRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\ShowingRequestRepositoryInterface::class,
            \App\Repositories\Eloquent\ShowingRequestRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\UserBlackListRepositoryInterface::class,
            \App\Repositories\Eloquent\UserBlackListRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\UserPrivateCommentRepositoryInterface::class,
            \App\Repositories\Eloquent\UserPrivateCommentRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\CustomAttributeValueRepositoryInterface::class,
            \App\Repositories\Eloquent\CustomAttributeValueRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PropertyFAQRepositoryInterface::class,
            \App\Repositories\Eloquent\PropertyFAQRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PropertyStatusRepositoryInterface::class,
            \App\Repositories\Eloquent\PropertyStatusRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PropertyGalleryRepositoryInterface::class,
            \App\Repositories\Eloquent\PropertyGalleryRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PropertyReportRepositoryInterface::class,
            \App\Repositories\Eloquent\PropertyReportRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PropertyAmenityRepositoryInterface::class,
            \App\Repositories\Eloquent\PropertyAmenityRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PropertyAvailableTimeRepositoryInterface::class,
            \App\Repositories\Eloquent\PropertyAvailableTimeRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AboutUsRepositoryInterface::class,
            \App\Repositories\Eloquent\AboutUsRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\ContactUsRepositoryInterface::class,
            \App\Repositories\Eloquent\ContactUsRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\ContractTypeRepositoryInterface::class,
            \App\Repositories\Eloquent\ContractTypeRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\FloorRepositoryInterface::class,
            \App\Repositories\Eloquent\FloorRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\OrientationRepositoryInterface::class,
            \App\Repositories\Eloquent\OrientationRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PrivacyPolicyRepositoryInterface::class,
            \App\Repositories\Eloquent\PrivacyPolicyRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\CityRepositoryInterface::class,
            \App\Repositories\Eloquent\CityRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\SliderRepositoryInterface::class,
            \App\Repositories\Eloquent\SliderRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\ReportTypeRepositoryInterface::class,
            \App\Repositories\Eloquent\ReportTypeRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\PagesMetaRepositoryInterface::class,
            \App\Repositories\Eloquent\PagesMetaRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\ChatRepositoryInterface::class,
            \App\Repositories\Eloquent\ChatRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
