<?php

declare(strict_types=1);

namespace App\Service;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Service
{
    private S3Client $s3Client;
    private string $bucket;

    public function __construct(string $accessKeyId, string $secretAccessKey, string $region, string $bucket)
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $region,
            'credentials' => [
                'key'    => $accessKeyId,
                'secret' => $secretAccessKey,
            ],
        ]);

        $this->bucket = $bucket;
    }

    public function upload($file, string $key): mixed
    {
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $key,
                'SourceFile' => $file,
                'ACL'    => 'public-read',
            ]);
            return $result->get('ObjectURL');
        } catch (AwsException $e) {
            throw new \Exception("Erreur d'upload : " . $e->getMessage());
        }
    }

    public function delete(string $key): void
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $key,
            ]);
        } catch (AwsException $e) {
            throw new \Exception("Erreur de suppression : " . $e->getMessage());
        }
    }
}