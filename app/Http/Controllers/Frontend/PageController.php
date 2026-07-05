<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Traits\NotifyTrait;
use Illuminate\Support\Fluent;

class PageController extends Controller
{
    use NotifyTrait;

    public function __invoke()
    {
        $url = request()->segment(1);

        $page = Page::currentTheme()->where('code', $url)->where('locale', app()->getLocale())->first();

        if (! $page) {
            $page = Page::currentTheme()->where('url', $url)->where('locale', defaultLocale())->firstOrFail();
        }

        // Check if it's a service page
        if ($page->type == 'service') {
            $data = new Fluent(json_decode($page->data, true));

            // Load service page contents
            $features = \App\Models\LandingContent::currentTheme()
                ->where('type', 'service-'.$page->code.'-features')
                ->where('locale', app()->getLocale())
                ->get();

            if ($features->isEmpty() && app()->getLocale() != defaultLocale()) {
                $features = \App\Models\LandingContent::currentTheme()
                    ->where('type', 'service-'.$page->code.'-features')
                    ->where('locale', defaultLocale())
                    ->get();
            }

            $steps = \App\Models\LandingContent::currentTheme()
                ->where('type', 'service-'.$page->code.'-steps')
                ->where('locale', app()->getLocale())
                ->get();

            if ($steps->isEmpty() && app()->getLocale() != defaultLocale()) {
                $steps = \App\Models\LandingContent::currentTheme()
                    ->where('type', 'service-'.$page->code.'-steps')
                    ->where('locale', defaultLocale())
                    ->get();
            }

            $faqs = \App\Models\LandingContent::currentTheme()
                ->where('type', 'service-'.$page->code.'-faqs')
                ->where('locale', app()->getLocale())
                ->get();

            if ($faqs->isEmpty() && app()->getLocale() != defaultLocale()) {
                $faqs = \App\Models\LandingContent::currentTheme()
                    ->where('type', 'service-'.$page->code.'-faqs')
                    ->where('locale', defaultLocale())
                    ->get();
            }

            return view('frontend::pages.service-page', [
                'data' => $data,
                'title' => $page->title,
                'features' => $features,
                'steps' => $steps,
                'faqs' => $faqs,
            ]);
        }

        $data = new Fluent(json_decode($page->data, true));

        return view('frontend::pages.'.$page->url, ['data' => $data]);
    }

    public function getPage($section)
    {
        $page = Page::currentTheme()->where('code', $section)->where('type', 'dynamic')->where('status', true)->where('locale', app()->getLocale())->first();

        if (! $page) {
            $page = Page::currentTheme()->where('code', $section)->where('type', 'dynamic')->where('status', true)->where('locale', defaultLocale())->firstOrFail();
        }

        $title = $page->title;
        $data = new Fluent(json_decode($page->data, true));

        return view('frontend::pages.dynamic_page', ['data' => $data, 'title' => $title]);
    }
}
