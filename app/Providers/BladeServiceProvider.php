<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::if('user', function () {
            return Auth::check() && Auth::user()->role == 'user';
        });

        Blade::directive('removeimg', function ($expression) {
            [$isHidden, $img_field] = explode(',', $expression);
            $isHidden = trim($isHidden);
            $img_field = trim($img_field);

            return "<?php \$isHidden = {$isHidden}; \$img_field = '{$img_field}'; ?>
            <div data-des=\"<?php echo \$img_field; ?>\" <?php if(!\$isHidden) echo 'hidden'; ?> class=\"close remove-img <?php echo \$img_field; ?>\"><i data-lucide=\"x\"></i></div>";
        });
    }
}
