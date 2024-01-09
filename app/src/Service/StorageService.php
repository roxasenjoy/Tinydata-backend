<?php
namespace App\Service;

use Aws\S3\S3Client;

/**
 * Class StorageService
 *
 * @package App\Service
 */
class StorageService
{

    private $client;
    private $bucket;

    public function __construct()
    {
        $this->setBucket('tinydata');
        $this->setClient(new S3Client([
            'version' => 'latest',
            'region'  => 'eu-west-3', // cette configuration n'a pas d'importance pour MinIO
            'endpoint' => 'http://127.0.0.1:9000', // Localhost
            'use_path_style_endpoint' => true, // ce paramètre doit obligatoirement être actif
            'credentials' => [
                'key'    => 'minio-access-key', // Key MinIO
                'secret' => 'minio-secret-key', // Secret MinIO
            ],
        ]));
    }

    /**
     * @param string $fileName
     * @param string $content
     * @param array  $meta
     * @param string $privacy
     * @return string file url
     */
    public function upload( $fileName, $content, array $meta = [], $privacy = 'public-read')
    {
        return $this->getClient()->upload($this->getBucket(), $fileName, $content, $privacy, [
            'Metadata' => $meta
        ])->toArray()['ObjectURL'];
    }

    /**
     * @param string       $fileName
     * @param string|null  $newFilename
     * @param array        $meta
     * @param string       $privacy
     * @return string file url
     */
    public function uploadFile($fileName, $newFilename = null, array $meta = [], $privacy = 'public-read') {
        if(!$newFilename) {
            $newFilename = basename($fileName);
        }

        if(!isset($meta['contentType'])) {
            // Detect Mime Type
            $mimeTypeHandler = finfo_open(FILEINFO_MIME_TYPE);
            $meta['contentType'] = finfo_file($mimeTypeHandler, $fileName);
            finfo_close($mimeTypeHandler);
        }

        return $this->upload($newFilename, file_get_contents($fileName), $meta, $privacy);
    }

    /**
     * Getter of client
     *
     * @return S3Client
     */
    protected function getClient()
    {
        return $this->client;
    }

    /**
     * Setter of client
     *
     * @param S3Client $client
     *
     * @return $this
     */
    private function setClient(S3Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Getter of bucket
     *
     * @return string
     */
    protected function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Setter of bucket
     *
     * @param string $bucket
     *
     * @return $this
     */
    private function setBucket($bucket)
    {
        $this->bucket = $bucket;

        return $this;
    }
}
