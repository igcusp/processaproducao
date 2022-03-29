<?php

define("LINKLATTES", "https://lattes.cnpq.br/");

define("DIR", "pasta-no-servidor"); // pasta onde estará subdir com arquivos
define("URL", "https://site.br/"); // url do website de onde serão importados os arquivos
                                    // será somado a pasta definida em upload.php

// configuracoes para usar o uspdev/replicado
putenv('REPLICADO_HOST=192.168.100.99');
putenv('REPLICADO_PORT=1499');
putenv('REPLICADO_DATABASE=rep_dbc');
putenv('REPLICADO_USERNAME=dbmaint_read');
putenv('REPLICADO_PASSWORD=secret');
putenv('REPLICADO_CODUNDCLG=99');
putenv('REPLICADO_PATHLOG=' . DIR . '/logs/replicado.log'); // se não pusermos nada vai para default do replicado que é /tmp/log.log
