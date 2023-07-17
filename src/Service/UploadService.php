<?php

namespace App\Service;


use App\Entity\Project\Images;
use App\Entity\Project\Project;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService
{
    public function __construct(
        private string $publicDirectory,
    ){}

    public function isImage($file): bool
    {
        $extension = $file->getClientOriginalExtension();
        $arrVideoExtension  = ['jpeg', 'jpg', 'gif', 'png', 'bmp'];

        if(in_array(strtolower($extension), $arrVideoExtension))
        {
            return true;
        }
        return false;
    }

    public function isVideo($file): bool
    {
        $extension = $file->getClientOriginalExtension();
        $arrVideoExtension  = ['mov', 'mp4', 'avi', 'wmv', 'flv', '3gp', 'webm'];

        if(in_array(strtolower($extension), $arrVideoExtension))
        {
            return true;
        }
        return false;
    }

    public function isFile($file): bool
    {
        $extension = $file->getClientOriginalExtension();
        $arrVideoExtension  = ['pdf', 'xls', 'xlsx'];

        if(in_array(strtolower($extension), $arrVideoExtension))
        {
            return true;
        }
        return false;
    }

    public function upload(UploadedFile $file, $fileNameCode): string
    {
        $fileName = $fileNameCode.'.'.$file->getClientOriginalExtension();
//        $tempFileName = str_replace('.', '', uniqid('temp_', true)) . '.' . $file->getClientOriginalExtension();

        if ($this->isImage($file)) {
            $path = $this->publicDirectory . Images::UPLOAD_DIR;
            $file->move($path, $fileName);
        }

        return $fileName;
    }


    public function createFileName(UploadedFile $file, Project $project): string
    {
        $arrCode    = [];
        $arrLetter  = [];
        $alphas = range('a', 'z');

        if ($this->isImage($file)) {

            $arrImages = [];
            if ($project->getid()) {
                $arrImages = $this->imageRepository->findAllImagesByObject($objects);
            }

            //extraire code pour toutes les images de l'objet
            foreach ($arrImages as $fileCode) {
                $arrCode[] = $fileCode->getCode();
            }

            //extraire chacune des lettres unique pour les fichiers de l'objet
            foreach ($arrCode as $code) {
                $arrLetter[] = explode('_', $code)[1];
            }

            //Attribuer lettre du regex si inexistante
            foreach ($alphas as $a) {
                if (in_array($a, $arrLetter) == false) {
                    return $objects->getCode().'_'.$a;
                }
            }
            return $objects->getCode().'_'.'none';

        }
        return $objects->getCode().'_'.'none';
    }


    public function deleteFile(Image $entity): void
    {
        if ($entity->getSrc() !== null || $entity->getSrc() !== '') {
            $path = $this->publicDirectory . $entity::UPLOAD_DIR . $entity->getSrc();
            $filesystem = new Filesystem();
            $filesystem->remove($path);
        }
    }

}