<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploaderServices
{
 public function __construct(private SluggerInterface $slugger)
 {
 }

    //On va lui passer un objet de type UploadedFile
    //Et elle doit nous rretourner le nom de file
 public function uploadFile(UploadedFile $file, string $directoryFolder): string
 {
     //Création du nom de fichier
     $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
     // this is needed to safely include the file name as part of the URL
     $safeFilename = $this->slugger->slug($originalFilename);
     $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

     // Move the file to the directory where recipes are stored
     try {
         $file->move($directoryFolder, $newFilename

         );
     } catch (FileException $e) {
         // ... handle exception if something happens during file upload
     }
     return $newFilename;


 }
}