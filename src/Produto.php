<?php

declare(strict_types=1);

namespace Hackathon;

use PDO;

class Produto
{
    public function __construct(
        protected PDO $database,
    ) {
    }

    public function getAll()
    {
        $sql = 
        'SELECT 
            id_produto,
            produto,
            categoria_id,
            descricao,
            base64,
            valor,
            empresa_id
        FROM hackathon.produto';

        $stmt = $this->database->prepare($sql);

        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $records;
    }

    public function getById(int|string $id)
    {

        $sql = 
        'SELECT 
            id_produto,
            produto,
            categoria_id,
            descricao,
            base64,
            valor,
            empresa_id
        FROM hackathon.produto WHERE id_produto = :id';

        $stmt = $this->database->prepare($sql);

        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
          return $record;
        }

        return null;
    }


    public function post($produtos)
    {
        $sql = "INSERT INTO hackathon.produto (produto, base64, descricao, valor, categoria_id, empresa_id) VALUES ($produtos->produto, '', $produtos->descricao, $produtos->valor, $produtos->categoria_id, $produtos->empresa_id)";

        $stmt = $this->database->prepare($sql);

        $stmt->execute();

        return $this->database->lastInsertId();
    }

    public function put(int|string $id)
    {
        $sql = 'select * from hackathon.produto';

        return $this->database->query($sql)->fetchAll();
    }

    public function delete(int|string $id)
    {
        $sql = 'DELETE FROM hackathon.produto WHERE id_produto = :id';

        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
          return $record;
        }

        return null;
    }
}
