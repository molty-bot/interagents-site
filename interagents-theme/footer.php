</main>

<footer class="site-footer" role="contentinfo">
	<div class="container footer-inner">
		<div class="footer-brand">
			<a href="<?php echo esc_url( ia_localized_url( '/' ) ); ?>" class="brand-logo-link" rel="home">
				<img class="brand-logo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/interagents-logo-transparent.png' ); ?>" width="1991" height="349" alt="interagents.ai">
			</a>
			<p class="footer-tagline"><?php echo esc_html( ia_t(
				'Agenci AI do pracy. Systemy dla ludzi i AI.',
				'AI workers for the job. Systems for humans and AI.'
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
				<p class="company-name">SKANDO AS</p>
				<p class="company-detail"><?php echo ia_t( 'Org. nr.', 'Reg. no.' ); ?> 927 866 129</p>
				<p class="company-detail">Møvikvegen 213<br>5357 Fjell</p>
				<p class="company-detail"><a href="tel:+4746702028"><?php echo ia_t( 'Tel.', 'Phone:' ); ?> +47 467 02 028</a></p>
			</div>
		</div>

		<div class="footer-bottom">
			<p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> interagents.ai. <?php echo esc_html( ia_t(
				'Wszelkie prawa zastrzeżone.',
				'All rights reserved.'
			) ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
