<?php

namespace App\Http\Resources;

use App\Models\Blog;
use App\Models\Gateway;
use App\Models\LandingContent;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class LandingSectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $contents = match ($this->code) {
            'blog' => $this->getBlogs($request),
            'gateway' => $this->getActiveGateways(),
            'testimonial' => $this->getTestimonials($request),
            default => $this->getSectionData($this->code, $request),
        };

        $sectionData = $this->refineSectionData();

        return [
            'code' => $this->code,
            'order' => $this->sort,
            'section_data' => $sectionData,
            'section_contents' => $contents,
        ];
    }

    private function refineSectionData()
    {
        return collect(json_decode($this->data, true))->map(function ($value) {

            // Extract array values from json
            if (is_array($arrayValue = json_decode($value, true))) {
                return collect($arrayValue)->map(function ($item) {
                    if (file_exists(public_path($item)) && is_file(public_path($item))) {
                        return asset($item);
                    }

                    return $item;
                })->toArray();
            }

            // Check if value is a file
            if (file_exists(public_path($value)) && is_file(public_path($value))) {
                return asset($value);
            }

            return $value;
        });
    }

    private function getSectionData($section, $request)
    {
        $theme = $request->get('theme', site_theme());
        $locale = $request->get('locale', app()->getLocale());

        $contents = LandingContent::select('icon', 'title', 'description')->where('theme', $theme)
            ->where('locale', $locale)
            ->where('type', $section)
            ->get()
            ->map(function ($content) {
                $content->icon = $content->icon !== null ? asset($content->icon) : null;

                return $content;
            });

        // fallback to en
        if ($contents->isEmpty() && $locale !== 'en') {
            $locale = 'en';

            $contents = LandingContent::select('icon', 'title', 'description')->where('theme', $theme)
                ->where('locale', $locale)
                ->where('type', $section)
                ->get()
                ->map(function ($content) {
                    $content->icon = $content->icon !== null ? asset($content->icon) : null;

                    return $content;
                });
        }

        return $contents;
    }

    private function getTestimonials($request)
    {
        return Testimonial::select('picture', 'name', 'designation', 'message')
            ->latest()
            ->get()
            ->map(function ($testimonial) {
                $testimonial->picture = $testimonial->picture !== null ? asset($testimonial->picture) : null;

                return $testimonial;
            });
    }

    private function getBlogs($request)
    {
        $locale = $request->get('locale', app()->getLocale());
        $limit = $request->get('blog_limit');

        return Blog::select('id', 'cover', 'title', 'details')
            ->where('locale', $locale)
            ->when($limit, function ($query) use ($limit) {
                $query->limit($limit);
            })
            ->latest()
            ->get()
            ->map(function ($blog) {
                $blog->cover = asset($blog->cover);

                $shortDetails = Str::of($blog->details)
                    ->pipe(fn ($v) => html_entity_decode($v, ENT_QUOTES | ENT_HTML5, 'UTF-8'))
                    ->stripTags()
                    ->replaceMatches('/\R+/', ' ')
                    ->replaceMatches('/\s+/', ' ')
                    ->trim();

                $blog->short_details = $shortDetails;

                return $blog;
            });
    }

    private function getActiveGateways()
    {
        return Gateway::select('logo', 'name')->where('status', true)->get()->map(function ($gateway) {
            $gateway->logo = asset($gateway->logo);

            return $gateway;
        });
    }
}
