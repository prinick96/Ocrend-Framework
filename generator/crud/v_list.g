<?= $this->insert('overall/header') ?>
<body class="framework">

  <div class="logo">
    <h3><?= strtoupper(APP) ?></h3>
  </div>

  <div class="content">
    <div class="ocrend-welcome">
      <span class="ocrend-welcome">{{model}}</span>
      <span class="ocrend-welcome-subtitle">Listado</span>
    </div>
    <div class="form-actions">
      <table class="table table-bordered">
        <?php foreach(false != $data ? $data : array() as $d): ?>
        <tr>
          <td><?= $d['id'] ?></td>
          <td><a href="{{action}}/editar/<?= $d['id'] ?>">Editar</a></td>
          <td><a href="{{action}}/eliminar/<?= $d['id'] ?>">Eliminar</a></td>
        </tr>
        <?php endforeach ?>
      </table>
      <a class="btn btn-primary" href="{{action}}/crear">Crear</a>
    </div>

    <?= $this->insert('overall/footer') ?>

  </div>
</body>
</html>
