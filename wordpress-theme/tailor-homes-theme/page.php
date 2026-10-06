<?php get_header(); ?>
<script>document.body.classList.add('th-solid-header');</script>

<section class="sv-hero"><div class="w"><div class="sv-hero__inner"><h1 class="sv-hero__heading"><?php the_title(); ?></h1></div></div></section>

<section style="padding:80px 0;">
  <div class="w">
    <div style="max-width:800px;margin:0 auto;font-size:16px;font-weight:300;color:var(--th-ink-mid);line-height:1.85;">
      <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
