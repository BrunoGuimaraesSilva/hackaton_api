<?php

declare(strict_types=1);

namespace Hackathon;

use PDO;

class Categoria
{
    public function __construct(
        protected PDO $database,
    ) {
    }

    public function getAll()
    {
        $sql = 
        'SELECT 
            id_categoria,
            categoria
        FROM hackathon.categoria';

        $stmt = $this->database->prepare($sql);

        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $records;
    }

    public function getById(int|string $id)
    {

        $sql = 
        'SELECT 
            id_categoria,
            categoria
        FROM hackathon.categoria WHERE id_categoria = :id';

        $stmt = $this->database->prepare($sql);

        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
          return $record;
        }

        return null;
    }


    public function post($categoria)
    {
        $sql = 
        'INSERT INTO hackathon.categoria (categoria) VALUES (:categoria)';

        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(":categoria", $categoria->categoria);
        $stmt->execute();

        return $this->database->lastInsertId();
    }

    public function put(int|string $id, $categoria)
    {   
        $sql = 
        'UPDATE hackathon.categoria t SET 
            t.categoria = :categoria
        WHERE t.id_categoria = :id';

        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":categoria", $categoria->categoria);
    
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
