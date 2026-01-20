<?php
$pageTitle = "Trang chủ";
?>

<!-- Hero Slider -->
<?php render_component('home/hero_slider'); ?>

<!-- Statistics Counter Section - Redesigned -->
<?php render_component('home/statistics', ['statistics' => $statistics ?? []]); ?>

<div class="container">
    <!-- Featured Categories -->
    <?php render_component('home/featured_categories', ['categories' => $categories ?? []]); ?>

    <!-- Featured Books -->
    <?php render_component('home/featured_books', ['featuredBooks' => $featuredBooks ?? []]); ?>

    <!-- Deals of the Day -->
    <?php render_component('home/deals', ['dealsOfTheDay' => $dealsOfTheDay ?? []]); ?>

    <!-- Bestsellers -->
    <?php render_component('home/bestsellers', ['topSellingBooks' => $topSellingBooks ?? []]); ?>

    <!-- Author Spotlight -->
    <?php render_component('home/author_spotlight', ['featuredAuthors' => $featuredAuthors ?? []]); ?>

    <!-- New Arrivals -->
    <?php render_component('home/new_arrivals', ['newArrivals' => $newArrivals ?? []]); ?>

    <!-- Customer Testimonials -->
    <?php render_component('home/testimonials'); ?>

    <!-- Newsletter Subscription -->
    <?php render_component('home/newsletter'); ?>

    <!-- Promotional Banner -->
    <?php render_component('home/promo_banner'); ?>

    <!-- Why Choose Us -->
    <?php render_component('home/why_choose_us'); ?>
</div>

<!-- Home Page Scripts -->
<?php render_component('home/scripts'); ?>