@extends('front.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : '')
@section('meta_tags')
  {!! \Artesaos\SEOTools\Facades\SEOTools::generate() !!}
@endsection
@push('stylesheets')
  <link rel="stylesheet" type="text/css" href="{{ asset('back/vendors/styles/icon-font.min.css') }}" />
  <style>
    .copy-tooltip {
      position: absolute;
      bottom: 125%;
      left: 50%;
      transform: translateX(-50%);
      background: #333;
      color: #fff;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      white-space: nowrap;
      z-index: 10;
    }
  </style>
@endpush
@section('content')

  <!-- ===== ARTICLE HERO ===== -->
  <div class="article-hero">
    <img src="{{ asset('/images/posts/' . $post->featured_image) }}" alt="{{ $post->title }}" />
    <div class="article-hero-overlay">
      <span class="tag">
        <a href="{{ route('blog.category_posts', $post->post_category->slug) }}">{{ $post->post_category->name }}</a>
      </span>
      <h1>
        {{ $post->title }}
      </h1>
      <div class="article-meta">
        <img class="author-avatar" src="{{ $post->author->picture }}" alt="{{ $post->author->name }}" />
        <strong>
          <a href="{{ route('blog.author_posts', $post->author->username) }}">{{ $post->author->name }}</a>
        </strong>
        <span class="separator">|</span>
        <span><i class="icon-copy ion-calendar"></i> {{ date_formatter($post->created, 'long') }}</span>
        <span class="separator">|</span>
        <span><i class="icon-copy ion-clock"></i> {{ readingDuration($post->content) }}
          @choice('min|mins', readingDuration($post->title, $post->content)) read</span>
      </div>
    </div>
  </div>

  <!-- ===== ARTICLE LAYOUT ===== -->
  <div class="article-layout">
    <!-- Main Content -->
    <article class="article-content">
      <p>
        {!! $post->content !!}
      </p>

      <!-- Article Footer: Tags & Share -->
      <div class="article-footer">
        <div class="article-tags">
          @if ($tags)
            Tags:
            @foreach ($tags as $tag)
              <span class="tag"><a href="{{ route('blog.tag_posts', $tag) }}">{{ $tag }}</a></span>
            @endforeach
          @endif
        </div>
        <div class="nextPrev-article-section">
          @if ($previousPost)
            <div class="prev-article">
              <a href="{{ route('blog.read_post', $previousPost->slug) }}" class="nav-btn" rel="prev">
                ← Previous
                <br />
                {{ $previousPost->title }}
              </a>
            </div>
          @endif
          <div class="next-article">
            @if ($nextPost)
              <a href="{{ route('blog.read_post', $nextPost->slug) }}" class="nav-btn" rel="next">
                Next →
                <br />
                {{ $nextPost->title }}
              </a>
            @endif
          </div>

        </div>
        <div class="share-section">
          <span>Share this article:</span>

          <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.read_post', $post->slug)) }}&amp;text={{ urlencode($post->title) }}"
            rel="noopener noreferrer" class="share-btn" aria-label="Share on X">𝕏</a>

          <a href="#" class="share-btn" data-share="instagram" aria-label="Share on Instagram">
            <i class="ti-instagram"></i>
          </a>

          <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.read_post', $post->slug)) }}"
            rel="noopener noreferrer" class="share-btn" aria-label="Share on Facebook">
            <i class="ti-facebook"></i>
          </a>

          <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.read_post', $post->slug)) }}"
            rel="noopener noreferrer" class="share-btn" aria-label="Share on LinkedIn">
            <i class="ti-linkedin"></i>
          </a>

          <a href="https://www.pinterest.com/pin/create/button?url={{ urlencode(route('blog.read_post', $post->slug)) }}&description={{ urlencode($post->title) }}"
            rel="noopener noreferrer" class="share-btn" aria-label="Share on Pinterest">
            <i class="ti-pinterest"></i>
          </a>

          <a href="mailto:?subject={{ urlencode('Check out this post: ' . $post->title) }}&amp;body={{ urlencode('I found this interesting post: ' . route('blog.read_post', $post->slug)) }}"
            class="share-btn" aria-label="Share on Mail">
            <i class="ti-email"></i>
          </a>

          <a href="#" class="share-btn" data-share="copy" aria-label="Copy link" title="Copy link">🔗</a>
        </div>
      </div>

      <!-- Author Box -->
      <div class="author-box">
        <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&h=200&fit=crop&crop=face"
          alt="Alex Johnson" />
        <div>
          <h4>Alex Johnson</h4>
          <div class="role">Senior Technology Writer</div>
          <p>
            Alex has been covering technology for over a decade. He
            specializes in AI, machine learning, and the intersection of
            technology and society. His work has appeared in major tech
            publications worldwide.
          </p>
        </div>
      </div>
    </article>

    <!-- Sidebar -->
    <aside class="sidebar">
      <!-- Table of Contents -->
      <div class="sidebar-card">
        <h3>Article Search</h3>
        <form action="{{ route('blog.search_posts') }}">
          <div class="search-form-box">
            <input type="text" name="articleSearch" placeholder="Search ...." />

            <button class="btn-submit" type="submit">Search</button>
          </div>
        </form>
      </div>

      <!-- Related Articles -->
      @if ($relatedPosts?->isNotEmpty())
        <div class="sidebar-card">
          <h3>📚 Related Articles</h3>
          @foreach ($relatedPosts as $relatedPost)
            <div class="related-item">
              <a href="{{ route('blog.read_post', $relatedPost->slug) }}">
                <img src="{{ asset('images/posts/' . $relatedPost->featured_image) }}" alt="{{ $relatedPost->title }}" />
              </a>
              <div>
                <h4 class="text-center">
                  <a href="{{ route('blog.read_post', $relatedPost->slug) }}">{{ $relatedPost->title }}</a>
                </h4>
              </div>
            </div>
          @endforeach
        </div>
      @endif

      <!-- Newsletter -->
      <div class="sidebar-card" style="background: var(--gradient); border: none; color: #fff">
        <h3 style="color: #fff; border-bottom-color: rgba(255, 255, 255, 0.2)">
          📬 Newsletter
        </h3>
        <p style="font-size: 0.88rem; opacity: 0.85; margin-bottom: 16px">
          Get weekly insights delivered to your inbox.
        </p>
        <input type="email" placeholder="Your email" style="
                                    width: 100%;
                                    padding: 12px 16px;
                                    border: 2px solid rgba(255, 255, 255, 0.3);
                                    border-radius: 10px;
                                    background: rgba(255, 255, 255, 0.15);
                                    color: #fff;
                                    font-size: 0.9rem;
                                    outline: none;
                                    margin-bottom: 12px;
                                  " />
        <button style="
                                    width: 100%;
                                    padding: 12px;
                                    background: #fff;
                                    color: var(--primary);
                                    border: none;
                                    border-radius: 10px;
                                    font-weight: 700;
                                    font-size: 0.9rem;
                                    cursor: pointer;
                                    transition: all 0.3s;
                                  ">
          Subscribe
        </button>
      </div>
    </aside>
  </div>

  <!-- ===== COMMENTS ===== -->
  <section class="comments-section">
    <h2>💬 Comments (3)</h2>

    <!-- Comment Form -->
    <div class="comment-form" style="margin-bottom: 40px">
      <div class="form-row">
        <input type="text" placeholder="Your name" />
        <input type="email" placeholder="Your email" />
      </div>
      <textarea placeholder="Write your comment..."></textarea>
      <button class="btn-submit">Post Comment</button>
    </div>

    <!-- Comment 1 -->
    <div class="comment">
      <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=face"
        alt="Sarah" />
      <div class="comment-content">
        <h4>Sarah Chen</h4>
        <div class="comment-date">December 16, 2025 at 2:34 PM</div>
        <p>
          Fantastic overview! The section on healthcare AI was particularly
          insightful. I've been working in medical imaging for the past few
          years and can confirm that AI tools are genuinely making a
          difference in diagnostic accuracy.
        </p>
      </div>
    </div>

    <!-- Comment 2 -->
    <div class="comment">
      <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=face"
        alt="David" />
      <div class="comment-content">
        <h4>David Kim</h4>
        <div class="comment-date">December 16, 2025 at 4:12 PM</div>
        <p>
          Great article! I'd love to see a follow-up piece on AI ethics. The
          challenges section touched on it briefly, but there's so much more
          to explore regarding bias, fairness, and accountability in AI
          systems.
        </p>
      </div>
    </div>

    <!-- Comment 3 -->
    <div class="comment">
      <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face" alt="Emma" />
      <div class="comment-content">
        <h4>Emma Wilson</h4>
        <div class="comment-date">December 17, 2025 at 9:45 AM</div>
        <p>
          As an educator, the AI in education section really resonated with
          me. We've started using an adaptive learning platform in our school
          and the results have been remarkable. Students who were struggling
          are finally catching up!
        </p>
      </div>
    </div>
  </section>

  <!-- ===== RELATED ARTICLES ===== -->
  <section class="related-section">
    <div class="related-section-header">
      <h2>More Articles You May Like</h2>
      <div class="line"></div>
    </div>
    <div class="related-grid">
      @foreach ($moreArticles as $article)
        <div class="related-card">
          <a href="{{ route('blog.read_post', $article->slug) }}">
            <img src="{{ asset('images/posts/' . $article->featured_image) }}" alt="{{ $article->title }}" />
          </a>
          <div class="related-card-body">
            <span class="tag"><a
                href="{{ route('blog.category_posts', $article->post_category->slug) }}">{{ $article->post_category->name }}</a></span>
            <h3>
              <a href="{{ route('blog.read_post', $article->slug) }}">{{ $article->title }}</a>
            </h3>
            <span class="date">{{ date_formatter($article->created_at, 'long') }}</span>
          </div>
        </div>
      @endforeach
    </div>
  </section>

@endsection
@push('scripts')
  <script>
    (function () {
      const shareUrl = @json(route('blog.read_post', $post->slug));
      const shareSection = document.querySelector('.share-section');

      if (!shareSection) return;

      function openPopup(url) {
        const width = 550;
        const height = 450;
        const left = (window.screen.width / 2) - (width / 2);
        const top = (window.screen.height / 2) - (height / 2);
        window.open(
          url,
          'share-popup',
          `height=${height},width=${width},top=${top},left=${left},toolbar=0,location=0,menubar=0,directories=0,scrollbars=0`
        );
      }

      function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text);
        } else {
          const temp = document.createElement('input');
          temp.value = text;
          document.body.appendChild(temp);
          temp.select();
          document.execCommand('copy');
          document.body.removeChild(temp);
        }
      }

      function showCopiedTooltip(el, message) {
        const tooltip = document.createElement('span');
        tooltip.className = 'copy-tooltip';
        tooltip.textContent = message;
        el.style.position = 'relative';
        el.appendChild(tooltip);
        setTimeout(() => {
          tooltip.style.transition = 'opacity 0.2s';
          tooltip.style.opacity = '0';
          setTimeout(() => tooltip.remove(), 200);
        }, 1500);
      }

      shareSection.addEventListener('click', function (e) {
        const link = e.target.closest('a.share-btn');
        if (!link || !shareSection.contains(link)) return;

        e.preventDefault();

        const shareType = link.dataset.share;
        const href = link.getAttribute('href');

        if (shareType === 'instagram') {
          copyToClipboard(shareUrl);
          openPopup('https://www.instagram.com/');
          showCopiedTooltip(link, 'Link copied — paste it into Instagram');
          return;
        }

        if (shareType === 'copy') {
          copyToClipboard(shareUrl);
          showCopiedTooltip(link, 'Link copied!');
          return;
        }

        if (href && href.startsWith('mailto:')) {
          window.location.href = href;
          return;
        }

        openPopup(href);
      });
    })();
  </script>
@endpush