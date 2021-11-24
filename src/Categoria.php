<?php

declare(strict_types=1);

namespace Hackathon;

use PDO;
use Utils\QueryUtils;

class Categoria
{
    public function __construct(
        protected PDO $database
    ) {}

    public function get()
    {
        $sql = 'select id_categoria,categoria from hackathon.categoria';
    
        $stmt = $this->database->prepare($sql);

        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $records;
    }

    public function post($produtos)
    {
        $queryUtils = new QueryUtils();

        $response_S3 = $queryUtils->uploadS3($produtos->produto, '');

        $sql = "INSERT INTO hackathon.produto (produto, link, descricao, valor, categoria_id, empresa_id) VALUES ($produtos->produto, $response_S3->get('ObjectUrl'), $produtos->descricao, $produtos->valor, $produtos->categoria_id, $produtos->empresa_id)";

        $stmt = $this->database->prepare($sql);

        $stmt->execute();

        return $this->database->lastInsertId();
    }

    public function put()
    {
        $sql = 'select * from hackathon.produto';

        return $this->database->query($sql)->fetchAll();
    }

    public function delete()
    {
        $sql = 'select * from hackathon.produto';

        return $this->database->query($sql)->fetchAll();
    }
}
