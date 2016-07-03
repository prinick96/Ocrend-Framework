<?= $this->insert('overall/header') ?>
<body class="framework">

  <div class="logo">
    <h3><?= strtoupper(APP) ?></h3>
  </div>

  <div class="content">
    <div class="ocrend-welcome">
      <span class="ocrend-welcome">Vista Ejemplo</span>
      <span class="ocrend-welcome-subtitle">Ejemplo de Vista</span>
    </div>
    <div class="form-actions">
      <p>Ejemplo generador</p>

      <form id="{{action}}_form" role="form">
        <div class="alert hide" id="ajax_{{action}}"></div>
        <div class="form-group">
          <label class="cole">Ejemplo:</label>
          <input type="text" class="form-control form-input" name="ejemplo" placeholder="Escribe algo..." />
        </div>
        <div class="form-group">
          <button type="button" id="{{action}}" class="btn red  btn-block">Enviar</button>
        </div>
      </form>

      <?= $this->insert('overall/modules') ?>
    </div>

    <?= $this->insert('overall/footer') ?>
    <script src="views/app/js/{{action}}.js"></script>

  </div>
</body>
</html>
