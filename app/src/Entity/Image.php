<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Youshido\GraphQLFilesBundle\Entity\FileTrait;
use Youshido\GraphQLFilesBundle\Model\FileModelInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image implements FileModelInterface
{
    use FileTrait;
}
