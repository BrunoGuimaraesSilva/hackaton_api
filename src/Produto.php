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

    public function getByCategoria(int|string $id)
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
        FROM hackathon.produto WHERE categoria_id = :id';

        $stmt = $this->database->prepare($sql);

        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
          return $record;
        }

        return null;
    }

    
    public function getByEmpresa(int|string $id)
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
        FROM hackathon.produto WHERE empresa_id = :id';

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
        $sql = 
        'INSERT INTO hackathon.produto (produto, base64, descricao, valor, categoria_id, empresa_id) VALUES 
        (
            :produto,
            :base64,
            :descricao,
            :valor,
            :categoria_id,
            :empresa_id
        )';

        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(":produto", $produtos->produto);
        $stmt->bindParam(":base64", $produtos->base64);
        $stmt->bindParam(":descricao", $produtos->descricao);
        $stmt->bindParam(":valor", $produtos->valor);
        $stmt->bindParam(":categoria_id", $produtos->categoria_id);
        $stmt->bindParam(":empresa_id", $produtos->empresa_id);

        
        $stmt->execute();

        return $this->database->lastInsertId();
    }

    public function put(int|string $id, $produto)
    {   
        $sql = 
        'UPDATE hackathon.produto t SET 
            t.produto = :produto,
            t.base64 = :base64,
            t.descricao = :descricao,
            t.valor = :valor,
            t.categoria_id = :categoria_id,
            t.empresa_id = :empresa_id
        WHERE t.id_produto = :id';

        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":produto", $produto->produto);
        $stmt->bindParam(":base64", $produto->base64);
        $stmt->bindParam(":descricao", $produto->descricao);
        $stmt->bindParam(":valor", $produto->valor);
        $stmt->bindParam(":categoria_id", $produto->categoria_id);
        $stmt->bindParam(":empresa_id", $produto->empresa_id);

        $stmt->execute();

        return $this->getById($id);
    }

    public function delete(int|string $id)
    {
        $sql = 'DELETE FROM hackathon.produto WHERE id_produto = :id';

        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return 'Success';
    }
}
