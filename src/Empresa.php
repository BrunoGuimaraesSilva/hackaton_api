<?php

declare(strict_types=1);

namespace Hackathon;

use PDO;

class Empresa
{
    public function __construct(
        protected PDO $database,
    ) {
    }

    public function getAll()
    {
        $sql = 
        'SELECT 
            id_empresa,
            empresa,
            telefone
        FROM hackathon.empresa';

        $stmt = $this->database->prepare($sql);

        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $records;
    }

    public function getById(int|string $id)
    {

        $sql = 
        'SELECT 
            id_empresa,
            empresa,
            telefone
        FROM hackathon.empresa WHERE id_empresa = :id';

        $stmt = $this->database->prepare($sql);

        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
          return $record;
        }

        return null;
    }


    public function post($empresa)
    {
        $sql = 
        'INSERT INTO hackathon.empresa (empresa, telefone) VALUES (:empresa, :telefone)';

        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(":empresa", $empresa->empresa);
        $stmt->bindParam(":telefone", $empresa->telefone);
        $stmt->execute();

        return $this->database->lastInsertId();
    }

    public function put(int|string $id, $empresa)
    {   
        $sql = 
        'UPDATE hackathon.empresa t SET 
            t.empresa = :empresa,
            t.telefone = :telefone
        WHERE t.id_empresa = :id';

        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":empresa", $empresa->empresa);
        $stmt->bindParam(":telefone", $empresa->telefone);
    
        $stmt->execute();

        return $this->getById($id);
    }

    public function delete(int|string $id)
    {
        $sql = 'DELETE FROM hackathon.empresa WHERE id_empresa = :id';

        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return 'Success';
    }
}
