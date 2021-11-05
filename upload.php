<?php
include_once 'header.php';
?>

<!-- Begin page content -->
<main class="flex-shrink-0 mt-auto">
  <div class="container">


      <form action="processa.php" method="post">      
                <div class="mb-3">
                    <label for="exptaiprod" class="form-label">Produção exportado do TAINACAN</label>
                    <input class="form-control form-control-sm" type="file" id="exptaiprod">
                </div>
                <div class="mb-3">
                    <label for="exptaipesq" class="form-label">Pesquisadores exportado do TAINACAN</label>
                    <input class="form-control form-control-sm" type="file" id="exptaipesq">
                </div>
                <div class="mb-3">
                    <label for="dedalus" class="form-label">Produção exportado do DEDALUS</label>
                    <input class="form-control form-control-sm" type="file" id="dedalus">
                </div>
                <div class="mb-3">
                    <label for="pasta" class="form-label">Pasta onde estão os arquivos no servidor</label>
                    <input class="form-control form-control-sm" type="text" id="pasta" value="files">
                </div>
                <input type="hidden" id="enviado" name="enviado" value="10" />
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Processar arquivos</button>
                </div>
            </form>

  </div>
</main>

<?php
include_once 'footer.php';
?>