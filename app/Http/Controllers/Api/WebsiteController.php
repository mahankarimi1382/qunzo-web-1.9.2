<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Http\Resources\LandingSectionResource;
use App\Http\Resources\NavigationMenuResource;
use App\Models\Blog;
use App\Models\LandingContent;
use App\Models\LandingPage;
use App\Models\Navigation;
use App\Models\Page;
use App\Models\Social;
use App\Traits\ApiResponseTrait;
use App\Traits\NotifyTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class WebsiteController extends Controller
{
    use ApiResponseTrait, NotifyTrait;

    public function index(Request $request)
    {
        $theme = $request->get('theme', site_theme());
        $locale = $request->get('locale', app()->getLocale());

        $landingSections = LandingPage::where('theme', $theme)
            ->where('status', true)
            ->where('locale', $locale)
            ->whereNot('code', 'footer')
            ->orderBy('sort')
            ->get();

        if ($landingSections->isEmpty() && $locale !== 'en') {
            $locale = 'en';

            $landingSections = LandingPage::where('theme', $theme)
                ->where('status', true)
                ->where('locale', $locale)
                ->whereNot('code', 'footer')
                ->orderBy('sort')
                ->get();
        }

        return response()->json([
            'status' => true,
            'data' => LandingSectionResource::collection($landingSections),
            'metadata' => [
                'title' => setting('site_title', 'global'),
                'description' => setting('meta_description'),
                'keywords' => setting('meta_keywords'),
                'favicon' => asset(setting('site_favicon', 'global')),
                'logo' => asset(setting('site_logo', 'global')),
            ],
        ]);
    }

    public function getNavigationsData(Request $request)
    {
        $locale = $request->get('locale', defaultLocale() ?? app()->getLocale());
        app()->setLocale($locale);

        $headerNavigations = Navigation::select('id', 'name', 'url', 'page_id', 'translate', 'megamenu_name', 'has_megamenu', 'megamenu_type')
            ->where('status', 1)
            ->whereJsonContains('type', 'header')
            ->with('activeMegamenuItems')
            ->orderBy('header_position')
            ->get();

        $widgetOneMenus = Navigation::select('id', 'name', 'page_id', 'url', 'translate', 'megamenu_name', 'has_megamenu')
            ->where('status', 1)
            ->whereJsonContains('type', 'widget_one')
            ->with('activeMegamenuItems')
            ->orderBy('footer_position')
            ->get();

        $widgetTwoMenus = Navigation::select('id', 'name', 'page_id', 'url', 'translate', 'megamenu_name', 'has_megamenu')
            ->where('status', 1)
            ->whereJsonContains('type', 'widget_two')
            ->with('activeMegamenuItems')
            ->orderBy('footer_position')
            ->get();

        $widgetThreeMenus = Navigation::select('id', 'name', 'page_id', 'url', 'translate', 'megamenu_name', 'has_megamenu')
            ->where('status', 1)
            ->whereJsonContains('type', 'widget_three')
            ->with('activeMegamenuItems')
            ->orderBy('footer_position')
            ->get();

        $socials = Social::select('url', 'icon')->orderBy('position')->get()->map(function ($social) {
            $social->icon = asset($social->icon);

            return $social;
        });

        $footerContentData = json_decode(LandingPage::where('code', 'footer')->where('locale', $locale)->first()?->data, true);

        return response()->json([
            'status' => true,
            'data' => [
                'header' => [
                    'menus' => NavigationMenuResource::collection($headerNavigations),
                ],
                'footer' => [
                    'copyright_text' => data_get($footerContentData, 'copyright_text'),
                    'bottom_text' => data_get($footerContentData, 'footer_bottom_text'),
                    'newsletter' => [
                        'title' => data_get($footerContentData, 'newsletter_title'),
                        'description' => data_get($footerContentData, 'newsletter_description'),
                    ],
                    'menus' => [
                        'widget_one' => [
                            'title' => data_get($footerContentData, 'widget_title_1'),
                            'data' => NavigationMenuResource::collection($widgetOneMenus),
                        ],
                        'widget_two' => [
                            'title' => data_get($footerContentData, 'widget_title_2'),
                            'data' => NavigationMenuResource::collection($widgetTwoMenus),
                        ],
                        'widget_three' => [
                            'title' => data_get($footerContentData, 'widget_title_3'),
                            'data' => NavigationMenuResource::collection($widgetThreeMenus),
                        ],
                    ],
                    'socials' => $socials,
                ],
            ],
        ]);
    }

    public function getPageData(Request $request, $code)
    {
        $theme = $request->get('theme', site_theme());
        $locale = $request->get('locale', defaultLocale() ?? app()->getLocale());

        $page = Page::where('theme', $theme)->where('url', $code)->where('locale', $locale)->first();

        // fallback to en
        if (! $page && $locale !== 'en') {
            $locale = 'en';

            $page = Page::where('theme', $theme)
                ->where('url', $code)
                ->where('locale', $locale)
                ->first();
        }

        if (! $page) {
            return response()->json([
                'status' => false,
                'message' => 'Page not found',
            ], 404);
        }

        $pageData = json_decode($page->data, true) ?? [];

        // Convert image paths to asset URLs for service pages
        if ($page->type === 'service') {
            $imageFields = ['hero_image', 'highlight_image', 'faq_image'];
            foreach ($imageFields as $field) {
                if (isset($pageData[$field]) && ! empty($pageData[$field])) {
                    $imagePath = $pageData[$field];
                    if (file_exists(public_path($imagePath)) && is_file(public_path($imagePath))) {
                        $pageData[$field] = asset($imagePath);
                    }
                }
            }
        } else {
            // For other page types, convert all file paths
            $pageData = collect($pageData)->map(function ($item) {
                // Check if value is a file
                if (is_string($item) && file_exists(public_path($item)) && is_file(public_path($item))) {
                    return asset($item);
                }

                return $item;
            })->toArray();
        }

        $extraSectionIds = json_decode(Arr::get($pageData, 'section_id', '[]'), true);

        $extraSections = LandingPage::whereIn('id', $extraSectionIds)->get();

        return response()->json([
            'status' => true,
            'data' => [
                'page' => [
                    'title' => $page->title . ' | ' . setting('site_title', 'global'),
                    'name' => $page->title,
                    'type' => $page->type,
                    'data' => $pageData,
                    'contents' => $this->getContents($page->code, $locale, $page->type, $theme),
                ],
                'extra_sections' => LandingSectionResource::collection($extraSections),
            ],
        ]);
    }

    public function blogs(Request $request)
    {
        $locale = $request->get('locale', app()->getLocale());
        $limit = $request->integer('limit', 8);

        $blogs = Blog::where('locale', $locale)->latest()->paginate($limit);

        return response()->json([
            'blogs' => BlogResource::collection($blogs),
            'meta' => [
                'current_page' => $blogs->currentPage(),
                'last_page' => $blogs->lastPage(),
                'per_page' => $blogs->perPage(),
                'total' => $blogs->total(),
            ],
        ]);
    }

    public function blogDetails(Request $request, $id)
    {
        $locale = $request->get('locale', app()->getLocale());

        $blog = Blog::where('locale', $locale)->where('id', $id)->firstOrFail();
        $recentBlogs = Blog::where('locale', $locale)->whereNot('id', $id)->latest()->take(6)->get();

        return response()->json([
            'status' => true,
            'data' => new BlogResource($blog),
            'recent_blogs' => BlogResource::collection($recentBlogs),
        ]);
    }

    private function getContents($code, $locale, $pageType = null, $theme = null)
    {
        // Service page content types
        if ($pageType === 'service') {
            $theme = $theme ?? site_theme();

            $features = LandingContent::where('theme', $theme)
                ->select('icon', 'title', 'description')
                ->where('type', 'service-' . $code . '-features')
                ->where('locale', $locale)
                ->get();

            // Fallback to English if no content found
            if ($features->isEmpty() && $locale !== 'en') {
                $features = LandingContent::where('theme', $theme)
                    ->select('icon', 'title', 'description')
                    ->where('type', 'service-' . $code . '-features')
                    ->where('locale', 'en')
                    ->get();
            }

            $steps = LandingContent::where('theme', $theme)
                ->select('icon', 'title', 'description')
                ->where('type', 'service-' . $code . '-steps')
                ->where('locale', $locale)
                ->get();

            // Fallback to English if no content found
            if ($steps->isEmpty() && $locale !== 'en') {
                $steps = LandingContent::where('theme', $theme)
                    ->select('icon', 'title', 'description')
                    ->where('type', 'service-' . $code . '-steps')
                    ->where('locale', 'en')
                    ->get();
            }

            $faqs = LandingContent::where('theme', $theme)
                ->select('title', 'description')
                ->where('type', 'service-' . $code . '-faqs')
                ->where('locale', $locale)
                ->get();

            // Fallback to English if no content found
            if ($faqs->isEmpty() && $locale !== 'en') {
                $faqs = LandingContent::where('theme', $theme)
                    ->select('title', 'description')
                    ->where('type', 'service-' . $code . '-faqs')
                    ->where('locale', 'en')
                    ->get();
            }

            return [
                'features' => $features->map(function ($content) {
                    $content->icon = $content->icon !== null ? asset($content->icon) : null;

                    return $content;
                }),
                'steps' => $steps->map(function ($content) {
                    $content->icon = $content->icon !== null ? asset($content->icon) : null;

                    return $content;
                }),
                'faqs' => $faqs,
            ];
        }

        // gift-cards page content types
        if ($code === 'gift-cards') {
            return [
                'how_it_works' => LandingContent::select('icon', 'title', 'description')
                    ->where('type', 'gift-cards-how-it-works')
                    ->where('locale', $locale)
                    ->get(),
                'faqs' => LandingContent::select('title', 'description')
                    ->where('type', 'gift-cards-faqs')
                    ->where('locale', $locale)
                    ->get(),
            ];
        }

        // virtual-cards page content types
        if ($code === 'virtual-cards') {
            return [
                'how_it_works' => LandingContent::select('icon', 'title', 'description')
                    ->where('type', 'virtual-cards-how-it-works')
                    ->where('locale', $locale)
                    ->get()
                    ->map(function ($content) {
                        $content->icon = $content->icon !== null ? asset($content->icon) : null;

                        return $content;
                    }),
                'features' => LandingContent::select('icon', 'title', 'description')
                    ->where('type', 'virtual-cards-features')
                    ->where('locale', $locale)
                    ->get()
                    ->map(function ($content) {
                        $content->icon = $content->icon !== null ? asset($content->icon) : null;

                        return $content;
                    }),
                'faqs' => LandingContent::select('title', 'description')
                    ->where('type', 'virtual-cards-faqs')
                    ->where('locale', $locale)
                    ->get(),
            ];
        }

        // mobile-recharge page content types
        if ($code === 'mobile-recharge') {
            return [
                'how_it_works' => LandingContent::select('icon', 'title', 'description')
                    ->where('type', 'mobile-recharge-how-it-works')
                    ->where('locale', $locale)
                    ->get()
                    ->map(function ($content) {
                        $content->icon = $content->icon !== null ? asset($content->icon) : null;

                        return $content;
                    }),
                'features' => LandingContent::select('icon', 'title', 'description')
                    ->where('type', 'mobile-recharge-features')
                    ->where('locale', $locale)
                    ->get()
                    ->map(function ($content) {
                        $content->icon = $content->icon !== null ? asset($content->icon) : null;

                        return $content;
                    }),
                'faqs' => LandingContent::select('title', 'description')
                    ->where('type', 'mobile-recharge-faqs')
                    ->where('locale', $locale)
                    ->get(),
            ];
        }

        // bill-payment page content types
        if ($code === 'bill-payment') {
            return [
                'bill_categories' => LandingContent::select('icon', 'title', 'description')
                    ->where('type', 'bill-payment-categories')
                    ->where('locale', $locale)
                    ->get()
                    ->map(function ($content) {
                        $content->icon = $content->icon !== null ? asset($content->icon) : null;

                        return $content;
                    }),
                'features' => LandingContent::select('icon', 'title', 'description')
                    ->where('type', 'bill-payment-features')
                    ->where('locale', $locale)
                    ->get()
                    ->map(function ($content) {
                        $content->icon = $content->icon !== null ? asset($content->icon) : null;

                        return $content;
                    }),
                'faqs' => LandingContent::select('title', 'description')
                    ->where('type', 'bill-payment-faqs')
                    ->where('locale', $locale)
                    ->get(),
            ];
        }

        // p2p-trading page content types
        if ($code === 'p2p-trading') {
            return [
                'how_it_works' => LandingContent::select('title', 'description')
                    ->where('type', 'p2p-trading-how-it-works')
                    ->where('locale', $locale)
                    ->get(),
                'features' => LandingContent::select('icon', 'title', 'description')
                    ->where('type', 'p2p-trading-features')
                    ->where('locale', $locale)
                    ->get()
                    ->map(function ($content) {
                        $content->icon = $content->icon !== null ? asset($content->icon) : null;

                        return $content;
                    }),
                'faqs' => LandingContent::select('title', 'description')
                    ->where('type', 'p2p-trading-faqs')
                    ->where('locale', $locale)
                    ->get(),
            ];
        }

        // Modify code for landing content because some pages are not using the same code as landing content
        $modifiedCode = match ($code) {
            'how-it-works' => 'howitworks',
            'agent' => 'agent',
            'merchant' => 'merchant',
            default => 'unknown_code',
        };

        if ($modifiedCode === 'unknown_code') {
            return collect([]);
        }

        return LandingContent::select('icon', 'title', 'description')->where('type', $modifiedCode)->where('locale', $locale)->get()
            ->map(function ($content) {
                $content->icon = $content->icon !== null ? asset($content->icon) : null;

                return $content;
            });
    }

    public function contactForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'msg' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        try {

            $input = $request->all();

            $shortcodes = [
                '[[full_name]]' => $input['name'],
                '[[email]]' => $input['email'],
                '[[subject]]' => $input['subject'],
                '[[message]]' => $input['msg'],
                '[[site_title]]' => setting('site_title', 'global'),
                '[[site_url]]' => '/',
            ];

            $this->sendNotify(setting('support_email', 'global'), 'contact_mail', 'Admin', $shortcodes, null, null);

            return $this->successWithoutData('Message send successfully!');
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
