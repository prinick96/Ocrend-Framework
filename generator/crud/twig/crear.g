{% include 'overall/header' %}
  <body>
    <div class="container">
      <div class="presentacion center">
        <div class="row">
          <div class="col-xs-12">
            <h1>{{model}} - Creación</h1>

            <form id="{{view}}_form" role="form">
              <div class="alert hide" id="ajax_{{view}}"></div>

              {{inputs}}

              <div class="form-group">
                <button type="button" id="{{view}}" class="btn btn-primary">Crear</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
    {% include 'overall/footer' %}
    <script src="views/app/js/{{view}}/crear.js"></script>
  </body>
</html>
