<?php get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
?>
<script>document.body.classList.add('th-solid-header');</script>

<section class="sv-hero">
  <div class="w">
    <div class="sv-hero__inner">
      <h1 class="sv-hero__heading"><?php echo is_home() ? 'Blog.' : ($it ? 'Archivio.' : 'Archive.'); ?></h1>
      <p class="sv-hero__sub"><?php echo $it ? 'Approfondimenti, aggiornamenti e storie dal mondo della gestione immobiliare e degli affitti brevi.' : 'Insights, updates, and stories from the world of property management and short-term rentals.'; ?></p>
    </div>
  </div>
</section>

<section style="padding: 80px 0;">
  <div class="w">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:40px;">
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article class="r" style="background:var(--th-white);border:1px solid rgba(26,25,22,.06);transition:transform .3s,box-shadow .3s;">
          <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>" style="display:block;aspect-ratio:16/10;overflow:hidden;">
              <?php the_post_thumbnail('large', array('style' => 'width:100%;height:100%;object-fit:cover;')); ?>
            </a>
          <?php endif; ?>
          <div style="padding:32px;">
            <div style="font-size:10px;font-weight:500;letter-spacing:.2em;text-transform:uppercase;color:var(--th-red);margin-bottom:10px;"><?php echo get_the_date($it ? 'j F Y' : 'F j, Y'); ?></div>
            <h2 style="font-size:20px;font-weight:700;line-height:1.3;margin-bottom:14px;letter-spacing:-.01em;"><a href="<?php the_permalink(); ?>" style="color:var(--th-ink);"><?php the_title(); ?></a></h2>
            <p style="font-size:14px;font-weight:300;color:var(--th-ink-mid);line-height:1.75;"><?php echo wp_trim_words(get_the_excerpt(), 25); ?></p>
          </div>
        </article>
      <?php endwhile; endif; ?>
    </div>
    <div style="text-align:center;padding-top:60px;">
      <?php the_posts_pagination(array('mid_size' => 2, 'prev_text' => $it ? '← Precedente' : '← Previous', 'next_text' => $it ? 'Successivo →' : 'Next →')); ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
