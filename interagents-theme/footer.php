</main>

<footer class="site-footer" role="contentinfo">
	<div class="container footer-inner">
		<div class="footer-brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title" rel="home">
				inter<span class="brand-accent">agents</span>.ai
			</a>
			<p class="footer-tagline"><?php esc_html_e( 'Inteligentne rozwiązania AI dla Twojego biznesu', 'interagents' ); ?></p>
		</div>
		<div class="footer-links">
			<a href="#uslugi"><?php esc_html_e( 'Usługi', 'interagents' ); ?></a>
			<a href="#jak-dzialamy"><?php esc_html_e( 'Jak działamy', 'interagents' ); ?></a>
			<a href="#dlaczego-my"><?php esc_html_e( 'Dlaczego my', 'interagents' ); ?></a>
			<a href="#kontakt"><?php esc_html_e( 'Kontakt', 'interagents' ); ?></a>
		</div>
		<div class="footer-bottom">
			<p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> InterAgents.ai. <?php esc_html_e( 'Wszelkie prawa zastrzeżone.', 'interagents' ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
