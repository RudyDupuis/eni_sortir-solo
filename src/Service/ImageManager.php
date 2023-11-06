<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageManager
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function saveImage($outingImageFile, $oldOutingImage, $picturesDirectory)
    {
        if ($outingImageFile instanceof UploadedFile) {

            if ($oldOutingImage) {
                $oldOutingImagePath = $this->parameterBag->get($picturesDirectory) . '/' . $oldOutingImage;
                if (file_exists($oldOutingImagePath)) {
                    unlink($oldOutingImagePath);
                }
            }

            $newFileName = md5(uniqid()) . '.' . $outingImageFile->guessExtension();

            $outingImageFile->move(
                $this->parameterBag->get($picturesDirectory),
                $newFileName
            );

            return $newFileName;
        }
    }
}
