{% include 'overall/header' %}
  <body>
    <div class="container">
      <div class="presentacion center">
        <div class="row">
          {% if get.success is defined %}
            <div class="col-xs-12">
              <div class="alert alert-success">Acción realizada con éxito</div>
            </div>
          {% endif %}
          <div class="col-xs-12">
            <h1>{{model}}</h1>

            {{tables}}

          </div>
          <div class="col-xs-12">
            <a href="{{view}}/crear" class="btn btn-sm btn-primary">Crear</a>
          </div>
        </div>
      </div>
    </div>
    {% include 'overall/footer' %}
  </body>
</html>
