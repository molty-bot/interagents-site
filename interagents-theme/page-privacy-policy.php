<?php
/**
 * Template Name: Privacy Policy
 * Template for the Privacy Policy page (required for App Store)
 *
 * @package InterAgents
 */

get_header();

$lang  = ia_get_lang();
$is_pl = $lang === 'pl';
?>

<main class="privacy-policy-page">
  <div class="privacy-container">

    <h1><?php echo $is_pl ? 'Polityka Prywatności' : 'Privacy Policy'; ?></h1>
    <p class="privacy-updated"><?php echo $is_pl ? 'Ostatnia aktualizacja: 7 marca 2026' : 'Last updated: March 7, 2026'; ?></p>

    <?php if ( $is_pl ) : ?>

    <section>
      <h2>1. Administrator danych</h2>
      <p>Administratorem danych osobowych jest InterAgents.ai, Adam Borkowski, z siedzibą w Norwegii. Kontakt: <a href="mailto:kontakt@interagents.ai">kontakt@interagents.ai</a></p>
    </section>

    <section>
      <h2>2. Jakie dane zbieramy</h2>
      <p>Zbieramy następujące dane:</p>
      <ul>
        <li><strong>Dane konta:</strong> adres e-mail i hasło (szyfrowane) przy rejestracji</li>
        <li><strong>Treści rozmów:</strong> wiadomości wysyłane do agentów AI w ramach korzystania z aplikacji</li>
        <li><strong>Dane techniczne:</strong> adres IP, typ urządzenia, wersja systemu operacyjnego, logi serwera</li>
        <li><strong>Dane analityczne:</strong> anonimowe statystyki użytkowania (Google Analytics 4)</li>
        <li><strong>Dane głosowe:</strong> nagrania głosowe (wyłącznie gdy użytkownik aktywuje funkcję mowy) — przetwarzane w celu transkrypcji i natychmiast usuwane</li>
      </ul>
    </section>

    <section>
      <h2>3. Cel przetwarzania danych</h2>
      <p>Dane przetwarzamy w celu:</p>
      <ul>
        <li>Świadczenia usług — komunikacja z agentami AI</li>
        <li>Zarządzania kontem użytkownika</li>
        <li>Ulepszania jakości usług i rozwiązywania problemów technicznych</li>
        <li>Bezpieczeństwa — wykrywanie nadużyć i ochrona przed nieautoryzowanym dostępem</li>
      </ul>
    </section>

    <section>
      <h2>4. Sztuczna inteligencja</h2>
      <p>Nasza aplikacja wykorzystuje modele sztucznej inteligencji (AI) do generowania odpowiedzi. Ważne informacje:</p>
      <ul>
        <li>Odpowiedzi AI są generowane automatycznie i mogą zawierać błędy</li>
        <li>Treści rozmów mogą być przetwarzane przez zewnętrznych dostawców AI (Anthropic, OpenAI) zgodnie z ich politykami prywatności</li>
        <li>Użytkownik może zgłosić nieodpowiednią treść AI za pomocą przycisku „Zgłoś" przy każdej odpowiedzi</li>
        <li>Nie wykorzystujemy treści rozmów do trenowania modeli AI</li>
      </ul>
    </section>

    <section>
      <h2>5. Udostępnianie danych</h2>
      <p>Dane mogą być udostępniane następującym podmiotom:</p>
      <ul>
        <li><strong>Dostawcy AI:</strong> Anthropic (Claude), OpenAI — przetwarzanie wiadomości</li>
        <li><strong>Hosting:</strong> Fly.io — serwery aplikacji</li>
        <li><strong>Analityka:</strong> Google Analytics 4 — anonimowe statystyki</li>
        <li><strong>TTS/STT:</strong> ElevenLabs, OpenAI Whisper — przetwarzanie mowy (opcjonalne)</li>
      </ul>
      <p>Nie sprzedajemy danych osobowych stronom trzecim.</p>
    </section>

    <section>
      <h2>6. Przechowywanie danych</h2>
      <p>Dane przechowujemy tak długo, jak jest to niezbędne do świadczenia usług. Historie rozmów są przechowywane na koncie użytkownika do momentu ich usunięcia. Użytkownik może usunąć swoje konto i wszystkie powiązane dane kontaktując się z nami.</p>
    </section>

    <section>
      <h2>7. Prawa użytkownika</h2>
      <p>Zgodnie z RODO, użytkownik ma prawo do:</p>
      <ul>
        <li>Dostępu do swoich danych osobowych</li>
        <li>Sprostowania danych</li>
        <li>Usunięcia danych („prawo do bycia zapomnianym")</li>
        <li>Ograniczenia przetwarzania</li>
        <li>Przenoszenia danych</li>
        <li>Sprzeciwu wobec przetwarzania</li>
      </ul>
      <p>Aby skorzystać z tych praw, skontaktuj się: <a href="mailto:kontakt@interagents.ai">kontakt@interagents.ai</a></p>
    </section>

    <section>
      <h2>8. Bezpieczeństwo</h2>
      <p>Stosujemy odpowiednie środki techniczne i organizacyjne w celu ochrony danych, w tym szyfrowanie haseł (bcrypt), szyfrowanie transmisji (HTTPS/TLS) oraz bezpieczne tokeny JWT do autoryzacji.</p>
    </section>

    <section>
      <h2>9. Dzieci</h2>
      <p>Nasza usługa nie jest przeznaczona dla osób poniżej 16. roku życia. Nie zbieramy świadomie danych od dzieci.</p>
    </section>

    <section>
      <h2>10. Zmiany w polityce prywatności</h2>
      <p>Zastrzegamy sobie prawo do zmiany niniejszej polityki. O istotnych zmianach poinformujemy użytkowników drogą e-mailową lub za pośrednictwem aplikacji.</p>
    </section>

    <section>
      <h2>11. Kontakt</h2>
      <p>Pytania dotyczące prywatności: <a href="mailto:kontakt@interagents.ai">kontakt@interagents.ai</a></p>
    </section>

    <?php else : ?>

    <section>
      <h2>1. Data Controller</h2>
      <p>The data controller is InterAgents.ai, Adam Borkowski, based in Norway. Contact: <a href="mailto:kontakt@interagents.ai">kontakt@interagents.ai</a></p>
    </section>

    <section>
      <h2>2. Data We Collect</h2>
      <p>We collect the following data:</p>
      <ul>
        <li><strong>Account data:</strong> email address and password (encrypted) upon registration</li>
        <li><strong>Conversation content:</strong> messages sent to AI agents while using the app</li>
        <li><strong>Technical data:</strong> IP address, device type, operating system version, server logs</li>
        <li><strong>Analytics data:</strong> anonymous usage statistics (Google Analytics 4)</li>
        <li><strong>Voice data:</strong> voice recordings (only when the user activates the speech feature) — processed for transcription and immediately deleted</li>
      </ul>
    </section>

    <section>
      <h2>3. Purpose of Data Processing</h2>
      <p>We process data for the following purposes:</p>
      <ul>
        <li>Providing services — communication with AI agents</li>
        <li>User account management</li>
        <li>Improving service quality and resolving technical issues</li>
        <li>Security — detecting abuse and protecting against unauthorized access</li>
      </ul>
    </section>

    <section>
      <h2>4. Artificial Intelligence</h2>
      <p>Our app uses artificial intelligence (AI) models to generate responses. Important information:</p>
      <ul>
        <li>AI responses are generated automatically and may contain errors</li>
        <li>Conversation content may be processed by third-party AI providers (Anthropic, OpenAI) in accordance with their privacy policies</li>
        <li>Users can report inappropriate AI content using the "Report" button on each response</li>
        <li>We do not use conversation content to train AI models</li>
      </ul>
    </section>

    <section>
      <h2>5. Data Sharing</h2>
      <p>Data may be shared with the following parties:</p>
      <ul>
        <li><strong>AI providers:</strong> Anthropic (Claude), OpenAI — message processing</li>
        <li><strong>Hosting:</strong> Fly.io — application servers</li>
        <li><strong>Analytics:</strong> Google Analytics 4 — anonymous statistics</li>
        <li><strong>TTS/STT:</strong> ElevenLabs, OpenAI Whisper — speech processing (optional)</li>
      </ul>
      <p>We do not sell personal data to third parties.</p>
    </section>

    <section>
      <h2>6. Data Retention</h2>
      <p>We retain data for as long as necessary to provide our services. Conversation history is stored in the user's account until deleted. Users can delete their account and all associated data by contacting us.</p>
    </section>

    <section>
      <h2>7. User Rights</h2>
      <p>Under GDPR, users have the right to:</p>
      <ul>
        <li>Access their personal data</li>
        <li>Rectification of data</li>
        <li>Erasure of data ("right to be forgotten")</li>
        <li>Restriction of processing</li>
        <li>Data portability</li>
        <li>Object to processing</li>
      </ul>
      <p>To exercise these rights, contact: <a href="mailto:kontakt@interagents.ai">kontakt@interagents.ai</a></p>
    </section>

    <section>
      <h2>8. Security</h2>
      <p>We implement appropriate technical and organizational measures to protect data, including password encryption (bcrypt), transmission encryption (HTTPS/TLS), and secure JWT tokens for authorization.</p>
    </section>

    <section>
      <h2>9. Children</h2>
      <p>Our service is not intended for persons under 16 years of age. We do not knowingly collect data from children.</p>
    </section>

    <section>
      <h2>10. Changes to This Policy</h2>
      <p>We reserve the right to modify this policy. Users will be notified of significant changes via email or through the app.</p>
    </section>

    <section>
      <h2>11. Contact</h2>
      <p>Privacy questions: <a href="mailto:kontakt@interagents.ai">kontakt@interagents.ai</a></p>
    </section>

    <?php endif; ?>

  </div>
</main>

<style>
.privacy-policy-page {
  background: var(--color-bg);
  min-height: 100vh;
  padding: 120px 20px 80px;
}

.privacy-container {
  max-width: 800px;
  margin: 0 auto;
  color: var(--color-text);
}

.privacy-container h1 {
  font-size: 2.4rem;
  color: var(--color-accent);
  margin-bottom: 0.3em;
}

.privacy-updated {
  color: var(--color-muted);
  font-size: 0.95rem;
  margin-bottom: 2.5em;
}

.privacy-container section {
  margin-bottom: 2em;
}

.privacy-container h2 {
  font-size: 1.3rem;
  color: var(--color-accent-2);
  margin-bottom: 0.5em;
}

.privacy-container p,
.privacy-container li {
  color: var(--color-text);
  line-height: 1.7;
  font-size: 1rem;
}

.privacy-container ul {
  padding-left: 1.5em;
  margin: 0.5em 0;
}

.privacy-container li {
  margin-bottom: 0.4em;
}

.privacy-container a {
  color: var(--color-accent);
  text-decoration: underline;
}

.privacy-container a:hover {
  color: var(--color-accent-2);
}

.privacy-container strong {
  color: var(--color-accent-2);
}
</style>

<?php get_footer(); ?>
