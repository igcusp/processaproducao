<?php

require_once 'config.php';

require_once __DIR__ . '/vendor/autoload.php';
use Uspdev\Replicado\Pessoa;
use Uspdev\Replicado\Lattes;

$dir = "uploads/";

$okfiles = true; // booleana para checar que os arquivos subiram ok

// arquivo dedalus
$dedalusfile = $dir . basename($_FILES['dedalus']['name']);
if (!move_uploaded_file($_FILES['dedalus']['tmp_name'], $dedalusfile)){
    echo "Falha ao carregar arquivo Dedalus\n";
    $okfiles = false;
}

// arquivo tainacan produção
$tainacanprodfile = $dir . basename($_FILES['tainacanprod']['name']);
if (!move_uploaded_file($_FILES['tainacanprod']['tmp_name'], $tainacanprodfile)){
    echo "Falha ao carregar arquivo Tainacan Produção\n";
    $okfiles = false;
}

// arquivo tainacan autores
$tainacanautfile = $dir . basename($_FILES['tainacanaut']['name']);
if (!move_uploaded_file($_FILES['tainacanaut']['tmp_name'], $tainacanautfile)){
    echo "Falha ao carregar arquivo Tainacan Autores\n";
    $okfiles = false;
}

if ($okfiles){ // se ok, subiu os arquivos, inicia processamento
    
    // abre arquivos para leitura
    $fileD = fopen($dedalusfile,"r");
    $fileP = fopen($tainacanprodfile,"r");
    $fileA = fopen($tainacanautfile,"r");
    
    // abre arquivos para escrita
    $dir = 'processados/';
    $nomeP = $dir."producao-" . date("YmdHis") . ".csv";
    $csvP = fopen($nomeP,"w"); 
    $nomeA = $dir."autores-" . date("YmdHis") . ".csv";
    $csvA = fopen($nomeA,"w"); 
    
    // armazena ids da produção já cadastrada fazendo loop no tainacanprod
    $rowP = 0;
    $colSYSNO = -1; // identificador da coluna que contém o sysno
    while (($data = fgetcsv($fileP)) !== FALSE) {
        //identifica coluna com o sysno
        if ($rowP==0){
            foreach ($data as $key=>$val){
                $aux = explode("|", $val);
                if ($aux[0]=='SYSNO') $colSYSNO = $key;
            }
            $rowP++;
            continue;
        }
        // obtendo ids 
        $prod_cadastrada[$data[$colSYSNO]] = $data[0]; // $data[0] é o special_item_id no tainacan
    }
    
    // armazena ids dos autores já cadastrados fazendo loop no tainacanaut
    $rowA = 0;
    $colNUSP = -1; // identificador da coluna que contém o número usp
    while (($data = fgetcsv($fileA)) !== FALSE) {
        //identifica coluna com o nusp
        if ($rowA==0){
            foreach ($data as $key=>$val){
                $aux = explode("|", $val);
                if ($aux[0]=='NUSP') $colNUSP = $key;
            }
            $rowA++;
            continue;
        }
        // obtendo ids 
        $aut_cadastrado[$data[$colNUSP]] = $data[0]; // $data[0] é o special_item_id no tainacan
    }
       
    // loop no dedalus
    $rowD = 0;
    $producaonova = false; // para verificar se alguma produção nova foi inserida; se foi, não será ofertado o link para download do csv dos autores
    while (($data = fgetcsv($fileD)) !== FALSE) {

        $linha = array(); // array que será gravado na linha

        // checa se já é cadastrado para gravar ou não o special_item_id
        $sysno = $data[0];
        $linha['special_item_id'] = '';
        if ($rowD>0){ // não está na primeira linha
            if (array_key_exists($sysno, $prod_cadastrada)) {
                $linha['special_item_id'] = $prod_cadastrada[$sysno]; //special_item_id
            }
            else {
                $producaonova = true;
            }  
        }
        
        // agora outros itens que não precisam de processamento
        $linha['sysno'] = $data[0];
        $linha['titulo'] = $data[2];
        $linha['doi'] = $data[1];
        $linha['fonte_publicacao'] = $data[4];
        $linha['paginacao'] = $data[5];
        $linha['ano_publicacao'] = $data[6];
        $linha['issn'] = $data[7];
        $linha['local_publicacao'] = $data[8];
        $linha['editora'] = $data[9];
        $linha['nome_evento'] = $data[10];
        $linha['tipo_material'] = $data[11];
        $linha['tipo_tese'] = $data[12];
        $linha['internacionalizacao'] = $data[17];
        $linha['url'] = $data[19];
        $linha['resumo'] = $data[20];
        $linha['abstract'] = $data[21];
        
        // agora itens que precisamos de pouco processamento
        $autores = explode(";", $data[3]); // não só usp
        $linha['autores'] = implode("||", $autores);
        
        $assuntos = explode(";", $data[18]);
        $linha['assuntos'] = implode("||", $assuntos);
        
        // escreve cabecalho
        if ($rowD==0){
            $keys = array_keys($linha);
            fputcsv($csvP, $keys); 
            $rowD++;
            continue;
        }
        
        // escreve no arquivo csv da produção
        fputcsv($csvP, $linha);
        
        // incrementa linha
        $rowD++;
        
        // agora muito processamento
        
        // verificando autores
        // lembrando que o vínculo com a produção é feito na coleção de autores
        $autores = explode(";",$data[13]);
        $nusps = explode(";",$data[14]);
        $unidades = explode(";", $data[15]);
        $departamentos = explode(";", $data[16]); //bug no csv
        // estes 4 arrays devem ter o mesmo tamanho, com exceção do $departamentosusp, que pode ser menor
        if (!(count($autores)==count($nusps))){
            echo "SYSNO " . $sysno . " -> Quantidade AutoresUSP diferente de NúmerosUSP<br>"
            . $data[13] . " - " . $data[14] . "\n";
        }
        elseif (!(count($autores)==count($unidades))){
            echo "SYSNO " . $sysno . " -> Quantidade AutoresUSP diferente de UnidadesUSP<br>\n"
            . $data[13] . " - " . $data[15] . "\n";        
        }
        else { // grava autor em array
            foreach ($nusps as $key => $nusp) {
                  
                // checa se já existe
                $autorusp[$nusp]['special_item_id'] = '';
                if (array_key_exists($nusp, $aut_cadastrado)) {
                    $autorusp[$nusp]['special_item_id'] = $aut_cadastrado[$nusp];
                } 

                // informações básicas
                $autorusp[$nusp]['nusp'] = $nusp;
                $autorusp[$nusp]['nome'] = $autores[$key];
                
                // unidades a que pertence/pertenceu
                if (!array_key_exists('unidadeusp', $autorusp[$nusp])) {
                    $autorusp[$nusp]['unidadeusp'] = $unidades[$key];
                } else {
                    $autorusp[$nusp]['unidadeusp'].= "||" . $unidades[$key];
                }                                        
                        
                // vincula a produção atual
                if ($linha['special_item_id'] != '') {
                    if (!array_key_exists('producao', $autorusp[$nusp])) {
                        $autorusp[$nusp]['producao'] = $linha['special_item_id'];
                    } else {
                        $autorusp[$nusp]['producao'].= "||" . $linha['special_item_id'];
                    }
                }
            }
        }
        $linha = NULL;
    } // FIM loop no dedalus
    fclose($csvP);
    
    // loop nos autores detectados para gera csv
    if (!$producaonova){
        $row = 0;
        foreach ($autorusp as $nusp=>$autor){
        
            // verifica repetição de unidades do autor e as retira
            $uns = explode("||", $autor['unidadeusp']);
            $unsunique = array_unique($uns);
            $autor['unidadeusp'] = implode("||", $unsunique);

            // nome por extenso
            $autor['nomeporextenso'] = Pessoa::nomeCompleto($nusp);  
            
            // obtém link lattes
            $idLattes = Lattes::id($nusp);
            $autor['lattes'] = LINKLATTES . $idLattes;
            if ($autor['lattes']==LINKLATTES) 
                $autor['lattes'] = '';

            // se tem lattes, obtem outras informações
            if ($idLattes!=''){
                // minibiografia
                $resumocv = Lattes::retornarResumoCV($nusp);
                $autor['minibiografia'] = $resumocv;
                // link orcid
                $idOrcid = Lattes::retornarOrcidID($nusp);
                $autor['orcid'] = $idOrcid;  
            }
            else {
                $autor['minibiografia'] = '';
                $autor['orcid'] = '';
            }                  
            
            // escreve cabecalho
            if ($row==0){
                $keys = array_keys($autor);
                fputcsv($csvA, $keys);
            }
            
            // escreve no arquivo csv do autores
            fputcsv($csvA, $autor);
            $row++;
        }
        fclose($csvA);
    }
    
    // oferece os arquivos ao usuário   
    if (!$producaonova){
       echo "<p><a href={$nomeP}>Arquivo para importação na coleção PRODUÇÃO INTELECTUAL</a></p>\n";
       echo "<p><a href={$nomeA}>Arquivo para importação na coleção AUTORES</a></p>\n";
       echo "<p>Nota: Se este já é o segundo passo do processamento, ou seja, "
       . "você já importou a PRODUÇÃO INTELECTUAL pois havia novos registros, basta importar a coleção AUTORES nesse momento final.</p>\n";
    }
    else {
        echo "<p><a href={$nomeP}>Arquivo para importação na coleção PRODUÇÃO INTELECTUAL</a></p>\n";
        echo "<p>Nota: Como há registros novos, siga os passos:\n"
            . "<ol>\n"
                . "<li>Faça a importação <a href={$nomeP}>deste arquivo</a> no Tainacan.</li>\n"
                . "<li>Exporte a coleção PRODUÇÃO INTELECTUAL novamente, já com os novos dados.</li>\n"
                . "<li>Refaça o processamento dos arquivos, mantendo o mesmo arquivo importado do DEDALUS e o dos AUTORES, mas agora usando este novo arquivo da PRODUÇÃO INTELECTUAL.</li>\n"
                . "<li>Após este segundo processamento, você terá os arquivos para as duas coleções.</li>\n"
                . "<li>Importe apenas a de AUTORES, pois a da produção intelectual já foi importada anteriormente.</li>\n"
            . "</ol>\n"
            . "</p>\n";
    }
}
else {
    echo "Problema com upload dos arquivos.";
}




