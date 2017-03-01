<?= $this->insert('overall/header') ?>
  <body>
    <div class="container">
      <div class="presentacion center">
        <div class="row">
          <div class="col-xs-12">
            <h1>Página nueva - Ajax</h1>

            <form id="{{view}}_form" role="form">
              <div class="alert hide" id="ajax_{{view}}"></div>
              <div class="form-group">
                <label class="cole">Ejemplo:</label>
                <input type="text" class="form-control form-input" name="ejemplo" placeholder="Escribe algo..." />
              </div>
              <div class="form-group">
                <button type="button" id="{{view}}" class="btn btn-primary">Enviar</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
    <?= $this->insert('overall/footer') ?>
    <script src="views/app/js/{{view}}/{{view}}.js"></script>
  </body>
</html>
