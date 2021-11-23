<?php

declare(strict_types=1);

namespace Hackathon;

use PDO;
use Aws\S3\S3Client;
use Exception;

class Query
{
    public function __construct(
        protected PDO $database
    ) {
    }

    private function uploadS3($name, $archive)
    {
        try {


            $clientS3 = S3Client::factory(
                [
                    'key'    => getenv('AWS_KEY'),
                    'secret' => getenv('AWS_SECRET')
                ]
            );

            $response = $clientS3->putObject(
                [
                    'Bucket' => getenv('AWS_BUCKET'),
                    'Key'    => $_FILES['file']['name'],
                    'Body'   => $file,
                    'ACL'    => 'public-read',
                ]
            );
        } catch (Exception $e) {
            echo "Erro > {$e->getMessage()}";
        }

        return $response;
    }

    public function get_produtos()
    {
        $sql = 'select * from hackathon.produto';

        return $this->database->query($sql)->fetchAll();
    }


    public function post_produtos($produtos)
    {
        $response_S3 = $this->uploadS3($produtos->produto, '');

        $sql = "INSERT INTO hackathon.produto (produto, link, descricao, valor, categoria_id, empresa_id) VALUES ($produtos->produto, $response_S3->get('ObjectUrl'), $produtos->descricao, $produtos->valor, $produtos->categoria_id, $produtos->empresa_id)";

        $stmt = $this->database->prepare($sql);

        foreach ($entityPersistenceInformation->columnsBind as $chave => $column) {
            $stmt->bindValue(
                $column,
                $entityPersistenceInformation->columnsValue[$chave]
            );
        }

        $stmt->execute();

        return $this->database->lastInsertId();
    }

    public function put_produtos()
    {
        $sql = 'select * from hackathon.produto';

        return $this->database->query($sql)->fetchAll();
    }

    public function teste()
    {
        $sql = 'select * from hackathon.produto';

        return $this->database->query($sql)->fetchAll();
    }
}
