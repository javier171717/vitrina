<?= $this->extend('layouts/marketing') ?>
<?= $this->section('content') ?>
<section class="hero">
  <div class="wrap hero-copy">
    <p class="kicker">Catálogo digital para tu negocio</p>
    <h1>Crea tu catálogo.<br>Vende digital.</h1>
    <p class="lead">Muestra tus productos, recibe cotizaciones y conecta a tus clientes directamente con tu equipo de ventas.</p>
    <div class="hero-actions">
      <?php if (! empty($loggedIn)): ?>
        <a class="btn btn-primary" href="<?= session()->get('user_role') === 'admin' ? site_url('admin') : site_url('panel') ?>">Ir al panel →</a>
      <?php else: ?>
        <a class="btn btn-primary" href="<?= site_url('registro') ?>">Crear mi vitrina →</a>
      <?php endif; ?>
      <a class="btn btn-ghost" href="#como-funciona" data-mkt-section="como-funciona">Ver cómo funciona</a>
    </div>
    <p class="hero-note">Sin complicaciones. Tu negocio, siempre disponible.</p>
  </div>
</section>

<section class="mkt-section wrap" id="como-funciona">
  <p class="kicker">Paso a paso</p>
  <h2 class="mkt-title">¿Cómo funciona?</h2>
  <ol class="how-steps">
    <li>
      <span>01</span>
      <h3>Crea tu vitrina</h3>
      <p>Registra tu negocio, elige tu color y sube el logo. En minutos ya tienes tu espacio.</p>
    </li>
    <li>
      <span>02</span>
      <h3>Agrega tu equipo</h3>
      <p>Cada asesor queda con su WhatsApp. El cliente cotiza directo con quien le corresponde.</p>
    </li>
    <li>
      <span>03</span>
      <h3>Carga tu catálogo</h3>
      <p>Categorías, fotos, precios y, si quieres, precio al por mayor según el valor de la compra.</p>
    </li>
    <li>
      <span>04</span>
      <h3>Comparte el enlace</h3>
      <p>Tus clientes abren el catálogo, arman la lista y la envían por WhatsApp. Sin apps extra.</p>
    </li>
  </ol>
</section>

<section class="mkt-section wrap" id="funciones">
  <p class="kicker">Lo que incluye</p>
  <h2 class="mkt-title">Funcionalidades</h2>
  <div class="features">
    <article class="feature">
      <h3>Un negocio, su marca</h3>
      <p>Logo, color, banner y datos de contacto los define el dueño. Cada catálogo se ve con su identidad.</p>
    </article>
    <article class="feature">
      <h3>Asesor en el enlace</h3>
      <p>El cliente entra ya cotizando con un asesor. Si hay varios, elige con quién hablar.</p>
    </article>
    <article class="feature">
      <h3>Lista para WhatsApp</h3>
      <p>Arma la cotización en el catálogo y la manda al celular del asesor, con referencias y cantidades.</p>
    </article>
  </div>
</section>

<section class="mkt-section wrap" id="negocios">
  <p class="kicker">Hecho para vender</p>
  <h2 class="mkt-title">Para negocios</h2>
  <p class="mkt-intro">Vitrina sirve si tu venta pasa por catálogo, asesor y WhatsApp: talleres, tiendas, distribuidores y equipos comerciales.</p>
  <div class="features">
    <article class="feature">
      <h3>Varios asesores</h3>
      <p>Cada vendedor tiene su enlace. La cotización llega a su WhatsApp, no a un buzón general.</p>
    </article>
    <article class="feature">
      <h3>Precios a tu medida</h3>
      <p>Muestra u oculta precios. Si la compra llega a un monto, puedes aplicar tarifa al por mayor.</p>
    </article>
    <article class="feature">
      <h3>Siempre disponible</h3>
      <p>Tu catálogo está en un enlace. El cliente lo abre cuando quiera, sin pedirte la lista a cada rato.</p>
    </article>
  </div>
</section>

<section class="mkt-section wrap" id="faq">
  <p class="kicker">Resolvemos dudas</p>
  <h2 class="mkt-title">Preguntas frecuentes</h2>
  <div class="faq">
    <details open>
      <summary>¿Necesito instalar una app?</summary>
      <p>No. Tú administras el catálogo desde el navegador y el cliente cotiza desde el enlace. La conversación sigue en WhatsApp, que ya usa tu equipo.</p>
    </details>
    <details>
      <summary>¿Cómo llega la cotización al asesor?</summary>
      <p>El cliente arma la lista en el catálogo y la envía al WhatsApp del asesor cuyo enlace está visitando. Si hay varios asesores, primero elige con quién cotizar.</p>
    </details>
    <details>
      <summary>¿Puedo tener más de un asesor?</summary>
      <p>Sí. Cada asesor tiene nombre, cobertura y WhatsApp. Tú decides quién aparece en el catálogo.</p>
    </details>
    <details>
      <summary>¿Puedo ocultar los precios?</summary>
      <p>Sí. En Mi negocio puedes mostrar u ocultar precios. El cliente igual arma la cotización y se la manda al asesor.</p>
    </details>
    <details>
      <summary>¿Cómo empiezo?</summary>
      <p>Crea tu vitrina, agrega un asesor y tus primeros productos. Luego comparte el enlace con tus clientes.</p>
    </details>
  </div>
</section>
<?= $this->endSection() ?>
