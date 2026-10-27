<?php

use App\Models\Category;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\User;
use App\UserType;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

new class extends Component {
    protected function postsQuery(): Builder
    {
        return Post::query()->when(
            ! $this->isSuperAdmin(),
            fn (Builder $query) => $query->where('author_id', auth()->id())
        );
    }

    protected function isSuperAdmin(): bool
    {
        return auth()->user()->type === UserType::SuperAdmin;
    }

    public function dashboardStats(): array
    {
        $posts = $this->postsQuery();
        $postCount = (clone $posts)->count();
        $publishedCount = (clone $posts)->where('visibility', 1)->count();
        $draftCount = (clone $posts)->where('visibility', 0)->count();
        $views = (clone $posts)->where('visibility', 1)->sum('number_of_views');
        $thisMonthCount = (clone $posts)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $thisMonthPublished = (clone $posts)
            ->where('visibility', 1)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        $thisMonthPublishedCount = (clone $thisMonthPublished)->count();
        $thisMonthViews = (clone $thisMonthPublished)->sum('number_of_views');
        $thisWeekCount = (clone $posts)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $topPostViews = (clone $posts)
            ->where('visibility', 1)
            ->max('number_of_views') ?? 0;

        return [
            'posts' => $postCount,
            'published' => $publishedCount,
            'drafts' => $draftCount,
            'views' => $views,
            'this_month' => $thisMonthCount,
            'this_month_published' => $thisMonthPublishedCount,
            'this_month_views' => $thisMonthViews,
            'this_month_views_share' => $views > 0 ? (int) round(($thisMonthViews / $views) * 100) : 0,
            'this_week' => $thisWeekCount,
            'average_views' => $publishedCount > 0 ? (int) round($views / $publishedCount) : 0,
            'monthly_share' => $postCount > 0 ? (int) round(($thisMonthCount / $postCount) * 100) : 0,
            'published_rate' => $postCount > 0 ? (int) round(($publishedCount / $postCount) * 100) : 0,
            'draft_rate' => $postCount > 0 ? (int) round(($draftCount / $postCount) * 100) : 0,
            'top_post_rate' => $views > 0 ? (int) round(($topPostViews / $views) * 100) : 0,
        ];
    }

    public function monthlyActivity(): array
    {
        $end = now()->endOfMonth();
        $start = $end->copy()->subMonths(11)->startOfMonth();
        $months = collect(range(0, 11))->map(fn (int $offset) => $start->copy()->addMonths($offset));
        $published = array_fill(0, 12, 0);
        $drafts = array_fill(0, 12, 0);

        $this->postsQuery()
            ->whereBetween('created_at', [$start, $end])
            ->get(['created_at', 'visibility'])
            ->each(function (Post $post) use ($start, &$published, &$drafts) {
                $index = (($post->created_at->year - $start->year) * 12)
                    + ($post->created_at->month - $start->month);

                if ($index >= 0 && $index < 12) {
                    if ($post->visibility == 1) {
                        $published[$index]++;
                    } else {
                        $drafts[$index]++;
                    }
                }
            });

        return [
            'labels' => $months->map(fn ($month) => $month->format('M'))->all(),
            'published' => $published,
            'drafts' => $drafts,
            'max' => max(array_merge($published, $drafts)),
        ];
    }

    public function topCategories()
    {
        return Category::query()
            ->withCount(['posts' => function (Builder $query) {
                $query->where('visibility', 1)
                    ->when(
                        auth()->user()->type !== UserType::SuperAdmin,
                        fn (Builder $posts) => $posts->where('author_id', auth()->id())
                    );
            }])
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->take(5)
            ->get();
    }

    public function topPosts()
    {
        return $this->postsQuery()
            ->where('visibility', 1)
            ->with(['post_category', 'author'])
            ->orderByDesc('number_of_views')
            ->latest('created_at')
            ->take(5)
            ->get();
    }

    public function recentDrafts()
    {
        return $this->postsQuery()
            ->where('visibility', 0)
            ->with(['post_category', 'author'])
            ->latest('updated_at')
            ->take(4)
            ->get();
    }

    public function applicationStats(): array
    {
        abort_unless($this->isSuperAdmin(), 403);

        return [
            'accounts' => User::query()->count(),
            'admins' => User::query()->where('type', UserType::Admin->value)->count(),
            'subscribers' => NewsletterSubscriber::query()->count(),
            'categories' => Category::query()->count(),
        ];
    }
};

?>
@php
    $isSuperAdmin = auth()->user()->type === UserType::SuperAdmin;
    $stats = $this->dashboardStats();
    $activity = $this->monthlyActivity();
    $topCategories = $this->topCategories();
    $topPosts = $this->topPosts();
    $recentDrafts = $this->recentDrafts();
    $applicationStats = $isSuperAdmin ? $this->applicationStats() : [];
    $chartMax = max(5, (int) ceil($activity['max'] / 5) * 5);
@endphp

<div class="blog-dashboard">
    <style>
        .blog-dashboard {
            --dash-ink: #203143;
            --dash-muted: #5f7185;
            --dash-line: #e7edf2;
            --dash-blue: #2f67dc;
            color: var(--dash-ink);
            padding: 8px 0 26px;
        }

        .blog-dashboard * {
            box-sizing: border-box;
        }

        .blog-dashboard__heading {
            align-items: flex-end;
            background: linear-gradient(115deg, #fff 0%, #fff 55%, #eef4ff 100%);
            border: 1px solid rgba(220, 228, 236, .8);
            border-radius: 13px;
            box-shadow: 0 8px 28px rgba(37, 56, 76, .045);
            display: flex;
            justify-content: space-between;
            margin: 0 0 20px;
            min-height: 150px;
            overflow: hidden;
            padding: 26px 30px;
            position: relative;
            gap: 18px;
        }

        .blog-dashboard__heading-copy,
        .blog-dashboard__heading-actions {
            position: relative;
            z-index: 1;
        }

        .blog-dashboard__heading-actions {
            align-items: center;
            display: flex;
            flex: 0 0 auto;
            gap: 18px;
        }

        .blog-dashboard__heading-image {
            align-items: center;
            background: #e7edff;
            border: 5px solid rgba(47, 103, 220, .12);
            border-radius: 50%;
            display: flex;
            height: 112px;
            justify-content: center;
            overflow: hidden;
            width: 112px;
        }

        .blog-dashboard__heading-image img {
            height: 100%;
            object-fit: cover;
            object-position: 70% center;
            width: 100%;
        }

        .blog-dashboard__eyebrow {
            color: var(--dash-blue);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            margin: 0 0 6px;
            text-transform: uppercase;
        }

        .blog-dashboard__title {
            color: var(--dash-ink);
            font-size: 27px;
            font-weight: 700;
            letter-spacing: -.04em;
            line-height: 1.2;
            margin: 0;
        }

        .blog-dashboard__subtitle {
            color: var(--dash-muted);
            font-size: 13px;
            margin: 7px 0 0;
        }

        .blog-dashboard__button {
            align-items: center;
            background: #203143;
            border: 0;
            border-radius: 9px;
            color: #fff !important;
            display: inline-flex;
            font-size: 13px;
            font-weight: 600;
            gap: 9px;
            padding: 11px 15px;
            text-decoration: none !important;
            transition: background .18s ease, transform .18s ease;
            white-space: nowrap;
        }

        .blog-dashboard__button:hover {
            background: #142233;
            transform: translateY(-1px);
        }

        .blog-dashboard__button:focus-visible {
            outline: 3px solid rgba(47, 103, 220, .4);
            outline-offset: 3px;
        }

        .blog-dashboard__button svg {
            height: 16px;
            width: 16px;
        }

        .blog-dashboard__metrics {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 20px;
        }

        .blog-dashboard__metric,
        .blog-dashboard__panel {
            border: 1px solid rgba(220, 228, 236, .8);
            border-radius: 13px;
            box-shadow: 0 8px 28px rgba(37, 56, 76, .045);
        }

        .blog-dashboard__metric {
            align-items: center;
            background: #fff;
            display: flex;
            justify-content: space-between;
            min-height: 112px;
            overflow: hidden;
            padding: 18px 19px;
        }

        .blog-dashboard__metric-content {
            min-width: 0;
        }

        .blog-dashboard__metric-label {
            color: #5f7185;
            font-size: 12px;
            font-weight: 500;
            margin: 0 0 9px;
        }

        .blog-dashboard__metric-value {
            color: var(--dash-ink);
            font-size: 25px;
            font-weight: 700;
            letter-spacing: -.04em;
            line-height: 1;
            margin: 0;
        }

        .blog-dashboard__metric-note {
            color: #5f7185;
            font-size: 11px;
            margin: 8px 0 0;
        }

        .blog-dashboard__metric-icon {
            align-items: center;
            background: conic-gradient(var(--ring-color) 0%, var(--ring-color) var(--ring-value), #edf0f4 var(--ring-value), #edf0f4 100%);
            border-radius: 50%;
            color: var(--dash-ink);
            display: grid;
            flex: 0 0 62px;
            font-size: 11px;
            font-weight: 700;
            height: 62px;
            justify-content: center;
            margin-left: 12px;
            place-items: center;
            position: relative;
            width: 62px;
        }

        .blog-dashboard__metric-icon::before {
            background: #fff;
            border-radius: 50%;
            content: "";
            inset: 5px;
            position: absolute;
        }

        .blog-dashboard__metric-icon span {
            position: relative;
            z-index: 1;
        }

        .blog-dashboard__main-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: minmax(0, 2.15fr) minmax(245px, 1fr);
        }

        .blog-dashboard__panel {
            background: #fff;
            padding: 21px 22px;
        }

        .blog-dashboard__panel-heading {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin-bottom: 13px;
        }

        .blog-dashboard__panel-title {
            color: var(--dash-ink);
            font-size: 16px;
            font-weight: 650;
            margin: 0;
        }

        .blog-dashboard__panel-caption {
            color: var(--dash-muted);
            font-size: 11px;
            margin: 5px 0 0;
        }

        .blog-dashboard__legend {
            align-items: center;
            color: #61738a;
            display: flex;
            font-size: 11px;
            gap: 15px;
        }

        .blog-dashboard__legend-item {
            align-items: center;
            display: inline-flex;
            gap: 6px;
            white-space: nowrap;
        }

        .blog-dashboard__legend-dot {
            border-radius: 50%;
            display: inline-block;
            height: 8px;
            width: 8px;
        }

        .blog-dashboard__chart {
            display: block;
            height: auto;
            margin-top: 6px;
            overflow: visible;
            width: 100%;
        }

        .blog-dashboard__chart-grid {
            stroke: var(--dash-line);
            stroke-width: 1;
        }

        .blog-dashboard__chart-label {
            fill: #5f7185;
            font-family: inherit;
            font-size: 10px;
        }

        .blog-dashboard__chart-bar {
            rx: 5px;
        }

        .blog-dashboard__side {
            display: grid;
            gap: 18px;
            grid-template-rows: auto 1fr;
        }

        .blog-dashboard__goal {
            align-items: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 255px;
            padding: 20px;
        }

        .blog-dashboard__goal-chart {
            height: 150px;
            margin: 6px auto 0;
            max-width: 200px;
            overflow: visible;
            width: 100%;
        }

        .blog-dashboard__goal-track,
        .blog-dashboard__goal-progress {
            fill: none;
            stroke-width: 9;
        }

        .blog-dashboard__goal-track {
            stroke: #edf0f4;
        }

        .blog-dashboard__goal-progress {
            stroke: var(--dash-blue);
            stroke-dasharray: 251.3;
            stroke-linecap: round;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }

        .blog-dashboard__goal-value {
            fill: var(--dash-ink);
            font-size: 18px;
            font-weight: 700;
            text-anchor: middle;
        }

        .blog-dashboard__goal-caption {
            color: var(--dash-muted);
            font-size: 11px;
            margin: -4px 0 0;
        }

        .blog-dashboard__category-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .blog-dashboard__category-row {
            align-items: center;
            border-bottom: 1px solid #f0f3f6;
            display: grid;
            gap: 10px;
            grid-template-columns: 28px minmax(0, 1fr) auto;
            padding: 12px 0;
        }

        .blog-dashboard__category-row:last-child {
            border-bottom: 0;
        }

        .blog-dashboard__category-rank {
            align-items: center;
            background: #edf3ff;
            border-radius: 8px;
            color: var(--dash-blue);
            display: inline-flex;
            font-size: 11px;
            font-weight: 700;
            height: 28px;
            justify-content: center;
            width: 28px;
        }

        .blog-dashboard__category-name {
            color: var(--dash-ink);
            font-size: 12px;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .blog-dashboard__category-count {
            color: var(--dash-muted);
            font-size: 11px;
            white-space: nowrap;
        }

        .blog-dashboard__recent {
            margin-top: 20px;
        }

        .blog-dashboard__snapshot {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 20px;
        }

        .blog-dashboard__application-overview {
            margin-top: 20px;
        }

        .blog-dashboard__application-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .blog-dashboard__scope {
            background: #edf3ff;
            border-radius: 30px;
            color: #2456b8;
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            margin: 0 0 8px;
            padding: 5px 10px;
        }

        .blog-dashboard__snapshot-item {
            background: #fff;
            border: 1px solid rgba(220, 228, 236, .8);
            border-radius: 12px;
            box-shadow: 0 8px 28px rgba(37, 56, 76, .045);
            padding: 17px 19px;
        }

        .blog-dashboard__snapshot-label {
            color: var(--dash-muted);
            font-size: 11px;
            margin: 0 0 8px;
        }

        .blog-dashboard__snapshot-value {
            color: var(--dash-ink);
            font-size: 21px;
            font-weight: 700;
            letter-spacing: -.03em;
            margin: 0;
        }

        .blog-dashboard__drafts {
            margin-top: 20px;
        }

        .blog-dashboard__draft-list {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .blog-dashboard__draft {
            align-items: center;
            background: #fbfcfe;
            border: 1px solid #e8edf2;
            border-radius: 10px;
            display: flex;
            gap: 12px;
            justify-content: space-between;
            min-width: 0;
            padding: 13px 15px;
        }

        .blog-dashboard__draft-copy {
            min-width: 0;
        }

        .blog-dashboard__draft-title {
            color: var(--dash-ink);
            display: block;
            font-size: 12px;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .blog-dashboard__draft-meta {
            color: var(--dash-muted);
            display: block;
            font-size: 10px;
            margin-top: 5px;
        }

        .blog-dashboard__draft-action {
            background: #edf3ff;
            border-radius: 7px;
            color: var(--dash-blue);
            flex: 0 0 auto;
            font-size: 10px;
            font-weight: 700;
            padding: 7px 10px;
            text-decoration: none;
        }

        .blog-dashboard__draft-action:hover {
            background: #dce8ff;
            color: #204fae;
        }

        .blog-dashboard__table-wrap {
            overflow-x: auto;
        }

        .blog-dashboard__table {
            border-collapse: collapse;
            margin: 0;
            min-width: 620px;
            width: 100%;
        }

        .blog-dashboard__table th {
            border-bottom: 1px solid var(--dash-line);
            color: #5f7185;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .06em;
            padding: 11px 10px;
            text-align: left;
            text-transform: uppercase;
        }

        .blog-dashboard__table td {
            border-bottom: 1px solid #f0f3f6;
            color: #546679;
            font-size: 12px;
            padding: 12px 10px;
            vertical-align: middle;
        }

        .blog-dashboard__table tr:last-child td {
            border-bottom: 0;
        }

        .blog-dashboard__post-cell {
            align-items: center;
            display: flex;
            gap: 11px;
            min-width: 230px;
        }

        .blog-dashboard__post-image {
            background: #edf2f7;
            border-radius: 8px;
            flex: 0 0 48px;
            height: 42px;
            object-fit: cover;
            width: 48px;
        }

        .blog-dashboard__post-title {
            color: var(--dash-ink);
            display: block;
            font-size: 12px;
            font-weight: 600;
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .blog-dashboard__post-author {
            color: var(--dash-muted);
            display: block;
            font-size: 10px;
            margin-top: 4px;
        }

        .blog-dashboard__edit-link {
            color: var(--dash-blue);
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
        }

        .blog-dashboard__edit-link:hover {
            text-decoration: underline;
        }

        .blog-dashboard__empty {
            color: var(--dash-muted);
            font-size: 13px;
            padding: 26px 10px !important;
            text-align: center;
        }

        .blog-dashboard__all-posts {
            color: var(--dash-blue);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }

        .blog-dashboard__all-posts:hover {
            color: #2456c4;
            text-decoration: underline;
        }

        @media (max-width: 1100px) {
            .blog-dashboard__metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .blog-dashboard__main-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .blog-dashboard__side {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                grid-template-rows: auto;
            }

            .blog-dashboard__draft-list {
                grid-template-columns: minmax(0, 1fr);
            }

            .blog-dashboard__application-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .blog-dashboard__heading {
                align-items: flex-start;
                flex-direction: column;
                padding: 22px;
            }

            .blog-dashboard__heading-actions {
                justify-content: space-between;
                width: 100%;
            }

            .blog-dashboard__heading-image {
                height: 82px;
                width: 82px;
            }

            .blog-dashboard__title {
                font-size: 24px;
            }

            .blog-dashboard__metrics,
            .blog-dashboard__side,
            .blog-dashboard__snapshot,
            .blog-dashboard__application-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .blog-dashboard__panel {
                padding: 17px 14px;
            }

            .blog-dashboard__panel-heading {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>

    <div class="blog-dashboard__heading">
        <div class="blog-dashboard__heading-copy">
            <p class="blog-dashboard__scope">{{ $isSuperAdmin ? 'Application-wide view' : 'Your personal workspace' }}</p>
            <p class="blog-dashboard__eyebrow">{{ $isSuperAdmin ? 'Application overview' : 'Your content overview' }}</p>
            <h1 class="blog-dashboard__title">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="blog-dashboard__subtitle">
                @if ($isSuperAdmin)
                    The application has {{ number_format($stats['published']) }} published {{ \Illuminate\Support\Str::plural('article', $stats['published']) }} and {{ number_format($stats['drafts']) }} {{ \Illuminate\Support\Str::plural('draft', $stats['drafts']) }} across all authors.
                @else
                    You have {{ number_format($stats['published']) }} published {{ \Illuminate\Support\Str::plural('article', $stats['published']) }} and {{ number_format($stats['drafts']) }} {{ \Illuminate\Support\Str::plural('draft', $stats['drafts']) }} in your workspace.
                @endif
            </p>
        </div>
        <div class="blog-dashboard__heading-actions">
            <div class="blog-dashboard__heading-image">
                <img src="{{ asset('back/vendors/images/banner-img.png') }}" alt="" aria-hidden="true">
            </div>
            <a class="blog-dashboard__button" href="{{ route('admin.add_post') }}">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                Write a post
            </a>
        </div>
    </div>

    <div class="blog-dashboard__metrics">
        <article class="blog-dashboard__metric">
            <div class="blog-dashboard__metric-content">
                <p class="blog-dashboard__metric-label">Total posts</p>
                <p class="blog-dashboard__metric-value">{{ number_format($stats['posts']) }}</p>
                <p class="blog-dashboard__metric-note">{{ number_format($stats['this_month']) }} {{ $isSuperAdmin ? 'added application-wide this month' : 'of yours added this month' }}</p>
            </div>
            <span class="blog-dashboard__metric-icon" style="--ring-value: {{ $stats['monthly_share'] }}%; --ring-color: #5b55e7" aria-label="{{ $stats['monthly_share'] }} percent of posts added this month">
                <span>{{ $stats['monthly_share'] }}%</span>
            </span>
        </article>
        <article class="blog-dashboard__metric">
            <div class="blog-dashboard__metric-content">
                <p class="blog-dashboard__metric-label">Published</p>
                <p class="blog-dashboard__metric-value">{{ number_format($stats['published']) }}</p>
                <p class="blog-dashboard__metric-note">{{ $stats['published_rate'] }}% {{ $isSuperAdmin ? 'of all posts' : 'of your posts' }}</p>
            </div>
            <span class="blog-dashboard__metric-icon" style="--ring-value: {{ $stats['published_rate'] }}%; --ring-color: #27a99a" aria-label="{{ $stats['published_rate'] }} percent published">
                <span>{{ $stats['published_rate'] }}%</span>
            </span>
        </article>
        <article class="blog-dashboard__metric">
            <div class="blog-dashboard__metric-content">
                <p class="blog-dashboard__metric-label">Drafts</p>
                <p class="blog-dashboard__metric-value">{{ number_format($stats['drafts']) }}</p>
                <p class="blog-dashboard__metric-note">{{ $stats['draft_rate'] }}% {{ $isSuperAdmin ? 'of all posts' : 'of your posts' }}</p>
            </div>
            <span class="blog-dashboard__metric-icon" style="--ring-value: {{ $stats['draft_rate'] }}%; --ring-color: #fa7b73" aria-label="{{ $stats['draft_rate'] }} percent drafts">
                <span>{{ $stats['draft_rate'] }}%</span>
            </span>
        </article>
        <article class="blog-dashboard__metric">
            <div class="blog-dashboard__metric-content">
                <p class="blog-dashboard__metric-label">Article views</p>
                <p class="blog-dashboard__metric-value">{{ number_format($stats['views']) }}</p>
                <p class="blog-dashboard__metric-note">Top post has {{ $stats['top_post_rate'] }}% of views</p>
            </div>
            <span class="blog-dashboard__metric-icon" style="--ring-value: {{ $stats['top_post_rate'] }}%; --ring-color: #4386ee" aria-label="{{ $stats['top_post_rate'] }} percent of views on top post">
                <span>{{ $stats['top_post_rate'] }}%</span>
            </span>
        </article>
    </div>

    <div class="blog-dashboard__main-grid">
        <section class="blog-dashboard__panel" aria-labelledby="activity-title">
            <div class="blog-dashboard__panel-heading">
                <div>
                    <h2 class="blog-dashboard__panel-title" id="activity-title">Publishing activity</h2>
                    <p class="blog-dashboard__panel-caption">{{ $isSuperAdmin ? 'Monthly activity across all authors' : 'Your monthly activity' }} · Last 12 months</p>
                </div>
                <div class="blog-dashboard__legend" aria-label="Chart legend">
                    <span class="blog-dashboard__legend-item">
                        <span class="blog-dashboard__legend-dot" style="background:#fa7b73"></span>
                        Published
                    </span>
                    <span class="blog-dashboard__legend-item">
                        <span class="blog-dashboard__legend-dot" style="background:#2f67dc"></span>
                        Drafts
                    </span>
                </div>
            </div>

            <svg class="blog-dashboard__chart" viewBox="0 0 770 250" role="img" aria-label="Published and draft post activity over the last 12 months">
                @foreach (range(0, 4) as $step)
                    @php
                        $y = 38 + ($step * 42);
                        $label = (int) round($chartMax * (4 - $step) / 4);
                    @endphp
                    <line class="blog-dashboard__chart-grid" x1="42" y1="{{ $y }}" x2="755" y2="{{ $y }}" />
                    <text class="blog-dashboard__chart-label" x="30" y="{{ $y + 3 }}" text-anchor="end">{{ $label }}</text>
                @endforeach

                @foreach ($activity['published'] as $index => $value)
                    @php
                        $x = 48 + ($index * 62.5);
                        $publishedHeight = ($value / $chartMax) * 168;
                        $draftHeight = ($activity['drafts'][$index] / $chartMax) * 168;
                    @endphp
                    <rect class="blog-dashboard__chart-bar" x="{{ $x - 13 }}" y="{{ 206 - $publishedHeight }}" width="12" height="{{ max(1, $publishedHeight) }}" fill="#fa7b73" />
                    <rect class="blog-dashboard__chart-bar" x="{{ $x + 2 }}" y="{{ 206 - $draftHeight }}" width="12" height="{{ max(1, $draftHeight) }}" fill="#2f67dc" />
                    <text class="blog-dashboard__chart-label" x="{{ $x }}" y="232" text-anchor="middle">{{ $activity['labels'][$index] }}</text>
                @endforeach
            </svg>
        </section>

        <aside class="blog-dashboard__side" aria-label="Blog insights">
            <section class="blog-dashboard__panel blog-dashboard__goal" aria-labelledby="monthly-views-title">
                <div class="blog-dashboard__panel-heading">
                    <div>
                        <h2 class="blog-dashboard__panel-title" id="monthly-views-title">Views on this month’s articles</h2>
                        <p class="blog-dashboard__panel-caption">Lifetime views on {{ $isSuperAdmin ? 'application-wide' : 'your' }} posts published this month</p>
                    </div>
                </div>
                <svg class="blog-dashboard__goal-chart" viewBox="0 0 100 100" role="img" aria-label="{{ number_format($stats['this_month_views']) }} lifetime views on articles published this month">
                    <circle class="blog-dashboard__goal-track" cx="50" cy="50" r="40" />
                    <circle class="blog-dashboard__goal-progress" cx="50" cy="50" r="40" style="stroke-dashoffset: {{ 251.3 - (251.3 * $stats['this_month_views_share'] / 100) }}" />
                    <text class="blog-dashboard__goal-value" x="50" y="55">{{ $stats['this_month_views_share'] }}%</text>
                </svg>
                <p class="blog-dashboard__goal-caption">{{ number_format($stats['this_month_views']) }} views across {{ number_format($stats['this_month_published']) }} published {{ \Illuminate\Support\Str::plural('article', $stats['this_month_published']) }}</p>
            </section>

            <section class="blog-dashboard__panel" aria-labelledby="categories-title">
                <div class="blog-dashboard__panel-heading">
                    <div>
                        <h2 class="blog-dashboard__panel-title" id="categories-title">Top categories</h2>
                        <p class="blog-dashboard__panel-caption">Published articles {{ $isSuperAdmin ? 'across the application' : 'from your posts' }}</p>
                    </div>
                </div>
                @if ($topCategories->isNotEmpty())
                    <ol class="blog-dashboard__category-list">
                        @foreach ($topCategories as $index => $category)
                            <li class="blog-dashboard__category-row">
                                <span class="blog-dashboard__category-rank">{{ $index + 1 }}</span>
                                <span class="blog-dashboard__category-name" title="{{ $category->name }}">{{ $category->name }}</span>
                                <span class="blog-dashboard__category-count">{{ number_format($category->posts_count) }} {{ \Illuminate\Support\Str::plural('post', $category->posts_count) }}</span>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="blog-dashboard__empty">No published categories yet.</p>
                @endif
            </section>
        </aside>
    </div>

    @if ($isSuperAdmin)
        <section class="blog-dashboard__panel blog-dashboard__application-overview" aria-labelledby="application-overview-title">
            <div class="blog-dashboard__panel-heading">
                <div>
                    <h2 class="blog-dashboard__panel-title" id="application-overview-title">Application overview</h2>
                    <p class="blog-dashboard__panel-caption">Platform-wide accounts, subscribers, and content structure</p>
                </div>
            </div>
            <div class="blog-dashboard__application-grid">
                <article class="blog-dashboard__snapshot-item">
                    <p class="blog-dashboard__snapshot-label">Registered accounts</p>
                    <p class="blog-dashboard__snapshot-value">{{ number_format($applicationStats['accounts']) }}</p>
                </article>
                <article class="blog-dashboard__snapshot-item">
                    <p class="blog-dashboard__snapshot-label">Admin accounts</p>
                    <p class="blog-dashboard__snapshot-value">{{ number_format($applicationStats['admins']) }}</p>
                </article>
                <article class="blog-dashboard__snapshot-item">
                    <p class="blog-dashboard__snapshot-label">Newsletter subscribers</p>
                    <p class="blog-dashboard__snapshot-value">{{ number_format($applicationStats['subscribers']) }}</p>
                </article>
                <article class="blog-dashboard__snapshot-item">
                    <p class="blog-dashboard__snapshot-label">Categories</p>
                    <p class="blog-dashboard__snapshot-value">{{ number_format($applicationStats['categories']) }}</p>
                </article>
            </div>
        </section>
    @endif

    <section class="blog-dashboard__snapshot" aria-label="{{ $isSuperAdmin ? 'Application publishing snapshot' : 'Your publishing snapshot' }}">
        <article class="blog-dashboard__snapshot-item">
            <p class="blog-dashboard__snapshot-label">{{ $isSuperAdmin ? 'Application posts this month' : 'Your posts this month' }}</p>
            <p class="blog-dashboard__snapshot-value">{{ number_format($stats['this_month']) }}</p>
        </article>
        <article class="blog-dashboard__snapshot-item">
            <p class="blog-dashboard__snapshot-label">{{ $isSuperAdmin ? 'Application posts this week' : 'Your posts this week' }}</p>
            <p class="blog-dashboard__snapshot-value">{{ number_format($stats['this_week']) }}</p>
        </article>
        <article class="blog-dashboard__snapshot-item">
            <p class="blog-dashboard__snapshot-label">{{ $isSuperAdmin ? 'Average views per published post (all authors)' : 'Average views per published post of yours' }}</p>
            <p class="blog-dashboard__snapshot-value">{{ number_format($stats['average_views']) }}</p>
        </article>
    </section>

    <section class="blog-dashboard__panel blog-dashboard__drafts" aria-labelledby="drafts-title">
        <div class="blog-dashboard__panel-heading">
            <div>
                <h2 class="blog-dashboard__panel-title" id="drafts-title">{{ $isSuperAdmin ? 'Application drafts in progress' : 'Your drafts in progress' }}</h2>
                <p class="blog-dashboard__panel-caption">{{ $isSuperAdmin ? 'Recently updated drafts from all authors' : 'Pick up where you left off' }}</p>
            </div>
            <a class="blog-dashboard__all-posts" href="{{ route('admin.posts', ['visibility' => 'private']) }}">Manage drafts <span aria-hidden="true">→</span></a>
        </div>

        @if ($recentDrafts->isNotEmpty())
            <div class="blog-dashboard__draft-list">
                @foreach ($recentDrafts as $draft)
                    <article class="blog-dashboard__draft" wire:key="dashboard-draft-{{ $draft->id }}">
                        <div class="blog-dashboard__draft-copy">
                            <span class="blog-dashboard__draft-title" title="{{ $draft->title }}">{{ $draft->title }}</span>
                            <span class="blog-dashboard__draft-meta">
                                {{ $draft->post_category?->name ?? 'Uncategorized' }}
                                @if ($isSuperAdmin)
                                    &middot; {{ $draft->author?->name ?? 'Unknown author' }}
                                @endif
                                &middot; Updated {{ $draft->updated_at?->diffForHumans() ?? 'recently' }}
                            </span>
                        </div>
                        <a class="blog-dashboard__draft-action" href="{{ route('admin.edit_post', $draft->id) }}">Continue</a>
                    </article>
                @endforeach
            </div>
        @else
            <p class="blog-dashboard__empty">No drafts waiting. Start a new post whenever inspiration strikes.</p>
        @endif
    </section>

    <section class="blog-dashboard__panel blog-dashboard__recent" aria-labelledby="recent-posts-title">
        <div class="blog-dashboard__panel-heading">
            <div>
                <h2 class="blog-dashboard__panel-title" id="recent-posts-title">Top performing articles</h2>
                <p class="blog-dashboard__panel-caption">Published articles {{ $isSuperAdmin ? 'across the application' : 'from your posts' }}, ranked by reader views</p>
            </div>
            <a class="blog-dashboard__all-posts" href="{{ route('admin.posts') }}">View all posts <span aria-hidden="true">→</span></a>
        </div>

        <div class="blog-dashboard__table-wrap">
            <table class="blog-dashboard__table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Category</th>
                        <th>Views</th>
                        <th>Published</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topPosts as $post)
                        <tr wire:key="dashboard-post-{{ $post->id }}">
                            <td>
                                <div class="blog-dashboard__post-cell">
                                    @php
                                        $imagePath = 'images/posts/' . $post->featured_image;
                                        if (! file_exists(public_path($imagePath))) {
                                            $imagePath = 'back/vendors/images/img.jpg';
                                        }
                                    @endphp
                                    <img class="blog-dashboard__post-image" src="{{ asset($imagePath) }}" alt="{{ $post->title }}" loading="lazy">
                                    <span>
                                        <span class="blog-dashboard__post-title" title="{{ $post->title }}">{{ $post->title }}</span>
                                        <span class="blog-dashboard__post-author">{{ $post->author?->name ?? 'Unknown author' }}</span>
                                    </span>
                                </div>
                            </td>
                            <td>{{ $post->post_category?->name ?? 'Uncategorized' }}</td>
                            <td>{{ number_format($post->number_of_views ?? 0) }}</td>
                            <td>
                                {{ $post->created_at?->format('M j, Y') ?? '—' }}
                            </td>
                            <td><a class="blog-dashboard__edit-link" href="{{ route('admin.edit_post', $post->id) }}">Edit</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td class="blog-dashboard__empty" colspan="5">No published articles yet. Publish a post to see how it performs.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
