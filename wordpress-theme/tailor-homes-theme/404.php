<?php get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
?>
<script>document.body.classList.add('th-solid-header');</script>

<section style="padding:220px 0 120px;text-align:center;">
  <div class="w">
    <div style="font-size:clamp(80px,10vw,160px);font-weight:800;color:var(--th-bg-dark);letter-spacing:-.04em;line-height:1;">404</div>
    <h1 style="font-size:clamp(28px,3vw,42px);font-weight:800;letter-spacing:-.02em;margin:20px 0 16px;"><?php echo $it ? 'Pagina non trovata.' : 'Page not found.'; ?></h1>
    <p style="font-size:16px;font-weight:300;color:var(--th-ink-mid);margin-bottom:40px;max-width:400px;margin-left:auto;margin-right:auto;line-height:1.75;"><?php echo $it ? 'La pagina che stai cercando non esiste o è stata spostata.' : 'The page you\'re looking for doesn\'t exist or has been moved.'; ?></p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="th-btn"><?php echo $it ? 'Torna alla Home →' : 'Back to Home →'; ?></a>
  </div>
</section>

<?php get_footer(); ?>
