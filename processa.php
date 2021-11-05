<?php
include_once 'header.php';
?>

<!-- Begin page content -->
<main class="flex-shrink-0 mt-auto">
  <div class="container">


<?php
    if (@$_POST['enviado']==10){      
        echo 'ok';
    }
    else {
        echo 'no';
    }
?> 

  </div>
</main>

<?php
include_once 'footer.php';
?>
