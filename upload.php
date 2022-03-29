<?php
include_once 'header.php';
?>

<!-- Begin page content -->
<main class="flex-shrink-0 mt-auto">
    <div class="container">


        <form action="processa.php" method="post" enctype="multipart/form-data">      
            <div class="mb-3">
                <label for="tainacanprod" class="form-label">Coleção PRODUÇÃO INTELECTUAL exportada do TAINACAN</label>
                <input class="form-control form-control-sm" type="file" id="tainacanprod" name="tainacanprod">
            </div>
            <div class="mb-3">
                <label for="tainacanaut" class="form-label">Coleção AUTORES exportada do TAINACAN</label>
                <input class="form-control form-control-sm" type="file" id="tainacanaut" name="tainacanaut">
            </div>
            <div class="mb-3">
                <label for="dedalus" class="form-label">Produção exportada do DEDALUS (em CSV)<br>
                    <em><u>Dica:</u> Use LibreOffice ou GooglePlanilhas para gerar o CSV</em></label>
                <input class="form-control form-control-sm" type="file" id="dedalus" name="dedalus">
            </div>
            <div class="mb-3">
                <label for="pasta" class="form-label">Pasta onde estão os arquivos (PDF e outros) no servidor</label>
                <input class="form-control form-control-sm" type="text" id="pasta" name="pasta">
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