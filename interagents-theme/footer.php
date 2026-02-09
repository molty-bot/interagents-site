</main>

<footer class="site-footer" role="contentinfo">
	<div class="container footer-inner">
		<div class="footer-brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title" rel="home">
				inter<span class="brand-accent">agents</span>.ai
			</a>
			<p class="footer-tagline"><?php echo esc_html( ia_t(
				'Inteligentne rozwiązania AI dla Twojego biznesu',
				'Intelligent AI solutions for your business'
			) ); ?></p>
		</div>

		<div class="footer-companies">
			<div class="footer-company">
				<h4><?php echo esc_html( ia_t( 'Polska', 'Poland' ) ); ?></h4>
				<p class="company-name">SKANDO Sp. z o.o.</p>
				<p class="company-detail"><?php echo ia_t( 'NIP:', 'Tax ID:' ); ?> 7831870581</p>
				<p class="company-detail">Wierzbięcice 44A/40A<br>61-568 Poznań</p>
				<p class="company-detail"><a href="tel:+48570914134"><?php echo ia_t( 'Tel.', 'Phone:' ); ?> +48 570 914 134</a></p>
			</div>
			<div class="footer-company">
				<h4><?php echo esc_html( ia_t( 'Norwegia', 'Norway' ) ); ?></h4>
				<p class="company-name">Demring Consult AS</p>
				<p class="company-detail"><?php echo ia_t( 'Org. nr.', 'Reg. no.' ); ?> 932 394 235</p>
				<p class="company-detail">Tellnes Næringspark 1<br>5357 Fjell</p>
				<p class="company-detail"><a href="tel:+4746702028"><?php echo ia_t( 'Tel.', 'Phone:' ); ?> +47 467 02 028</a></p>
			</div>
		</div>

		<div class="footer-bottom">
			<p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> InterAgents.ai. <?php echo esc_html( ia_t(
				'Wszelkie prawa zastrzeżone.',
				'All rights reserved.'
			) ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
