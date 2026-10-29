<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <section class="stats-section">
        @if (Route::Is('blog.home'))
            <div class="section-header" style="margin: 0px">
                <h2>Our Stats</h2>
                <p>Let the numbers speak on our behalf</p>
                <div class="line"></div>
            </div>
        @endif
        <div class="section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">2M+</div>
                    <div class="stat-label">Monthly Readers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Published Articles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Expert Writers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Countries Reached</div>
                </div>
            </div>
        </div>
    </section>
</div>