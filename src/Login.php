<?php

declare(strict_types=1);

namespace Hackathon;

use PDO;

class Login
{
    public function __construct(
        protected PDO $database,
    ) {
    }

    public function usuario($usuario)
    {
        $sql = 'SELECT id_usuario FROM hackathon.usuario WHERE usuario = :usuario';

        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(":usuario", $usuario->usuario);
        $stmt->execute();
        
        
        if ($stmt->rowCount() > 0) {
            return false;
        } else {
            $senhaMD5 = MD5($usuario->senha);

            $sql = 'INSERT INTO hackathon.usuario (usuario, login, senha) VALUES (:usuario, :login, :senha)';
            $stmt = $this->database->prepare($sql);
            $stmt->bindParam(":usuario", $usuario->usuario);
            $stmt->bindParam(":login", $usuario->login);
            $stmt->bindParam(":login", $senhaMD5);

            $stmt->execute();
            return true;
        }
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

    public function delete(int|string $id)
    {
        $sql = 'DELETE FROM hackathon.empresa WHERE id_empresa = :id';

        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return 'Success';
    }
}
