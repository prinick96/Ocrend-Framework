<?= $this->insert('overall/header') ?>
  <body>
    <div class="container">
      <div class="presentacion center">
        <div class="row">
          <div class="col-xs-12">
            <h1>{{model}} - Edición</h1>

            <form id="{{view}}_form" role="form">
              <div class="alert hide" id="ajax_{{view}}"></div>
              <input type="hidden" name="id" value="<?= $data['id'] ?>" />

              {{inputs}}

              <div class="form-group">
                <button type="button" id="{{view}}" class="btn btn-primary">Guardar</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
    <?= $this->insert('overall/footer') ?>
    <script src="views/app/js/{{view}}/editar.js"></script>
  </body>
</html>
