<?php

declare(strict_types=1);

namespace Hackathon;

use PDO;
use Utils\QueryUtils;

class Empresa
{
    public function __construct(
        protected PDO $database
    ) {}

    public function get()
    {
        $sql = 'SELECT id_empresa, empresa, telefone FROM hackathon.empresa';
    
        $stmt = $this->database->prepare($sql);

        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $records;
    }

    public function post($empresa)
    {
        $queryUtils = new QueryUtils();

        $response_S3 = $queryUtils->uploadS3($empresa->produto, '');

        $sql = "INSERT INTO hackathon.produto (produto, link, descricao, valor, categoria_id, empresa_id) VALUES ($empresa->produto, $response_S3->get('ObjectUrl'), $empresa->descricao, $empresa->valor, $empresa->categoria_id, $empresa->empresa_id)";

        $stmt = $this->database->prepare($sql);


        $stmt->execute();

        return $this->database->lastInsertId();
    }

    public function put()
    {
        $sql = 'select * from hackathon.empresa';

        return $this->database->query($sql)->fetchAll();
    }

    public function delete()
    {
        $sql = 'select * from hackathon.empresa';

        return $this->database->query($sql)->fetchAll();
    }

   
}
