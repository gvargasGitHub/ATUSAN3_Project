<style>
  h6 {
    text-align: center;
  }

  #df_login {
    margin: 30px auto;
    padding: 0;
    border: 1px solid var(--color-primary-base);
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;

    background-color: var(--color-primary-soft);

    max-width: 300px;
  }

  #df_login .header {
    background-color: var(--color-primary-dark);
    color: white;

    height: 50px;
    line-height: 50px;
    vertical-align: middle;
    text-align: center;
  }

  #df_login .body {
    padding: 16px 20px;
  }
</style>
<!--
  HTML
-->
<div class="ats-row">
  <div class="ats-col-4"></div>
  <div class="ats-col-4">
    <?= $this->df_login->write() ?>
    <h6>Versión: <?= $this->version ?></h6>
    <h6>Actualizado: <?= $this->updated ?></h6>
    <h6>Migración: <?= $this->migration ?></h6>
  </div>
  <div class="ats-col-4"></div>
</div>
<!--
  JAVASCRIPT
-->
<script>
  var
    // Estado de bandera "logout"
    logoutState = 0,
    // Id de rutina intervalo para monitoreo de
    // estado de "logout"
    logoutIntervalId = 0;

  modAcceso.onOpen = function() {
    df_login.setFocus('cuenta');

    // establece rutina programada cada 5 minutos para:
    // 1. Monitorea estado de bandera "logout" para
    //    cuestiones de mantenimiento de portal.
    logoutIntervalId = setInterval(function() {
      ats.send('/state', {
        onDone: rs => {
          if (logoutState == 0 && rs.logout == 0) {
            // Al abrir modAcceso inicializa logoutState en 0 y,
            // si al hacer la primer consulta de "logout" retora
            // 0, significa que no hay mantenimiento en curso,
            // por lo tanto, detiene la tarea
            clearInterval(logoutIntervalId);
            console.log('Limpia tarea programada.');
          } else if (rs.logout == 1) {
            // En la consulta de "logout" retorna 1, significa
            // que existe un mantenimiento en curso, por lo tanto,
            // mantiene la tarea
            logoutState = 1;
            console.log('Mantenimiento en curso');
          } else if (logoutState == 1 && rs.logout == 0) {
            // Se determinó que había un mantenimiento en curso
            // y la consulta de "logout" retorna 0, por lo tanto,
            // emitirá un alerta, detiene la tarea y refresca el
            // contenido
            logoutState = 0;
            clearInterval(logoutIntervalId);
            alert("Mantenimiento finalizado.\n")
            ats.openModule('/');
          }
        }
      });
    }, (1000 * 10));
  }

  df_login.onSubmitDone = function(rs) {
    ats.openModule('/admin');
  };

  df_login.onSubmitFail = function(msg) {
    alert(msg);
  };
</script>